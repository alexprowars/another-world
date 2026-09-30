<?php

namespace App\Engine\Battle\Services;

use App\Engine\Battle\Data\AttackResult;
use App\Engine\Battle\Enums\BattleType;
use App\Engine\Battle\Enums\ParticipantResult;
use App\Models\Battle;
use App\Models\BattleMember;
use App\Models\Level;
use App\Models\User;
use App\Models\UserItem;
use App\Services\BattleService;
use App\Services\ChatService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Random\Randomizer;

class RewardService
{
	public function __construct(private readonly Randomizer $randomizer)
	{
	}

	public function award(
		Battle $battle,
		BattleMember $fighter,
		User $user,
		ParticipantResult $result,
		CarbonImmutable $time,
	): void {
		$experienceReward = 0;

		// Восстанавливаем запас сил
		$user->stamina_now = min($user->stamina_now + 20, $user->getCombatStats()->vitality * 20);

		if ($result === ParticipantResult::DRAW) {
			$user->draws += 1;
		} elseif ($result === ParticipantResult::LOSS) {
			$user->losses += 1;
		}

		// Начисляем опыт за победу
		if ($result === ParticipantResult::WIN) {
			$experienceReward = $this->awardExperience($battle, $fighter, $user, $time);
		}

		$clanPoints = 0;

		// Начисляем рейтинг клана за победу
		if ($user->tribe && $result === ParticipantResult::WIN) {
			$minPoints = (int) round($fighter->damage / 2);
			$maxPoints = (int) round($fighter->damage * 1.5);

			$clanPoints = $this->randomizer->getInt($minPoints, $maxPoints);

			$user->tribe->points += $clanPoints;
			$user->tribe->save();
		}

		$goldReward = 0;

		if ($result === ParticipantResult::WIN) {
			if ($user->room == 1) {
				$rewardMultiplier = match ($battle->type) {
					BattleType::DUEL => 0.25,
					BattleType::GROUP => 0.3,
					BattleType::CHAOS => 0.35,
					BattleType::ALIGN => 0.4,
				};

				$goldReward = $rewardMultiplier * $user->level;
			} else {
				$goldReward = 0.2 * $user->level;
			}
		}

		if ($goldReward > 0) {
			$user->gold += $goldReward;
		}

		if ($battle->is_blood && $result === ParticipantResult::LOSS) {
			$user->injury = $time->addHours(3);
		}

		$wornItems = [];

		if ($result !== ParticipantResult::WIN) {
			$wornItems = BattleService::wearout($user, $this->randomizer);
		}

		if ($result === ParticipantResult::DRAW) {
			$message = 'К сожалению ваш бой закончился ничьёй. Попытайтесь снова. Нанесено урона: ' . $fighter->damage . ' HP.';
		} elseif ($result === ParticipantResult::LOSS) {
			$message = 'Ваш бой закончен, Вы проиграли. Нанесено урона: ' . $fighter->damage . ' HP.';
		} else {
			$message = 'Вы одержали победу! Нанесено урона: ' . $fighter->damage . ' HP. Получено опыта: ' . $experienceReward . '.';

			if ($goldReward > 0) {
				$message .= ' Получена награда: ' . $goldReward . ' золота.';
			}
		}

		ChatService::sendSystemMessage($message, [$user]);

		if (!empty($wornItems)) {
			$itemNames = array_map(fn(UserItem $item) => $item->title, $wornItems);
			ChatService::sendSystemMessage(
				'Ваши вещи приобрели единицу износа: ' . implode(', ', $itemNames),
				[$user],
			);
		}

		if ($clanPoints > 0) {
			ChatService::sendSystemMessage('Вы заработали для клана ' . $clanPoints . ' очков рейтинга.', [$user]);
		}
	}

	public function addAttackExperience(BattleMember $attacker, BattleMember $defender, AttackResult $attack): void
	{
		$healthRatio = min(1, $attack->damage / $attacker->user->hp_max);
		$experience = $healthRatio * BattleService::getBaseLevelExp($defender->user->level);
		$experience *= 2;
		$experience *= $attack->experienceMultiplier;

		if ($defender->user->rating > $attacker->user->rating) {
			$defender->user->rating = $attacker->user->rating;
		}

		if ($experience > 0) {
			$attacker->exp += (int) round($experience);
		}
	}

	private function awardExperience(
		Battle $battle,
		BattleMember $fighter,
		User $user,
		CarbonImmutable $time,
	): int {
		$addExp = 0;

		$levelUp = Level::query()
			->where('level', $user->level)
			->where('up', $user->up)
			->first();

		if ($levelUp) {
			// Рассчитываем опыт за бой
			if ($battle->type == BattleType::DUEL) {
				/** @var BattleMember $enemy */
				$enemy = $battle->members
					->where('user_id', '!=', $user->id)
					->first();

				$expMultiplier = $this->randomizer->getInt(100, 120) / 100;
				$addExp = round($enemy->exp * $expMultiplier);
			} else {
				$addExp = $this->calculateGroupExperience($battle, $fighter, $user);
			}

			$addExp *= 2;

			if ($battle->type == BattleType::CHAOS) {
				$addExp *= 1.3;
			}

			$maxExp = match ($user->level) {
				7 => 14000,
				8 => 18000,
				9 => 24000,
				10 => 36000,
				11 => 48000,
				12 => 60000,
				default => 12000,
			};

			if ($addExp > $maxExp) {
				$addExp = $maxExp;
			}

			// Боевая ярость удваивает опыт до окончания срока действия.
			if ($user->battle_fury?->greaterThan($time)) {
				$addExp *= 2;
			}
			// VIP увеличивает опыт втрое.
			if ($user->vip?->greaterThan($time)) {
				$addExp *= 3;
			}

			$addExp = (int) round($addExp);

			$newExp = $user->exp + $addExp;

			$reachedLevel = Level::query()
				->where('id', '>', $levelUp->id)
				->where('exp', '<=', $newExp)
				->orderByDesc('id')
				->first();

			if ($reachedLevel) {
				$addons = Level::query()
					->select(
						DB::raw('SUM(credits) as credits'),
						DB::raw('SUM(updates) as updates'),
					)
					->where('id', '>', $levelUp->id)
					->where('id', '<=', $reachedLevel->id)
					->toBase()
					->first();

				if ($reachedLevel->level > $user->level) {
					ChatService::sendPublicSystemMessage(
						'Персонаж ' . $user->name . ' получил повышение! Теперь он '
							. $reachedLevel->level . ' уровня! Поздравим его с этим достижением.',
					);
				}

				$user->level = $reachedLevel->level;
				$user->up = $reachedLevel->up;

				if ($addons) {
					$user->updates += $addons->updates;
					$user->credits += $addons->credits;
				}
			}

			$user->wins += 1;
			$user->exp = $newExp;
		}

		return $addExp;
	}

	private function calculateGroupExperience(Battle $battle, BattleMember $fighter, User $user): int
	{
		$baseExp = $battle->members
			->where('side', '!=', $fighter->side)
			->avg('exp');

		if ($baseExp === null) {
			return 0;
		}

		$baseExp = round($baseExp);
		$equippedSlots = $user->getSlotsInfo();
		$equipmentWeight = 0;

		$slotWeights = [
			1 => 4,
			3 => 4,
			4 => 4,
			5 => 4,
			9 => 0.765,
			13 => 0.765,
			14 => 0.765,
			15 => 0.765,
			2 => 0.64,
			16 => 0.64,
			19 => 0.64,
			6 => 0.17,
			7 => 0.17,
			8 => 0.17,
			10 => 0.17,
			11 => 0.17,
			12 => 0.17,
		];

		foreach ($slotWeights as $slot => $weight) {
			if (isset($equippedSlots['slot_' . $slot])) {
				$equipmentWeight += $weight;
			}
		}

		if ($equipmentWeight == 0) {
			return (int) ceil(0.15 * $baseExp);
		}

		$maxHealth = $user->getCombatStats()->vitality * 5 + $user->hp;

		if ($maxHealth <= 0) {
			return 0;
		}

		return (int) ceil(
			$equipmentWeight * 0.07 * $baseExp * ($fighter->damage / $maxHealth)
		);
	}
}
