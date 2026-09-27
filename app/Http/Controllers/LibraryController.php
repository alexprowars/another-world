<?php

namespace App\Http\Controllers;

use App\Http\Controller;
use App\Http\Resources\ShopItemResource;
use App\Models\Academy;
use App\Models\Level;
use App\Models\ShopItem;
use App\Models\WorkType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Inertia\Inertia;
use Inertia\Response;
use Kirschbaum\PowerJoins\PowerJoinClause;

class LibraryController extends Controller
{
	private const array SECTIONS = [
		'Оружие' => [
			1 => 'Ножи и кинжалы',
			2 => 'Мечи',
			3 => 'Топоры и секиры',
			4 => 'Дубины и булавы',
			5 => 'Луки и арбалеты',
		],
		'Доспехи' => [
			6 => 'Шлемы',
			7 => 'Рубахи',
			8 => 'Броня',
			16 => 'Нарукавники',
			9 => 'Перчатки',
			10 => 'Щиты',
			11 => 'Пояса',
			17 => 'Штаны',
			12 => 'Обувь',
		],
		'Ювелирные изделия' => [
			13 => 'Ожерелья',
			14 => 'Кольца',
			15 => 'Серьги',
		],
		'Подарки' => [
			101 => 'Букеты',
			100 => 'Открытки',
			102 => 'Подарки',
		],
		'Прочее' => [
			30 => 'Ресурсы',
			31 => 'Драгоценные камни',
		],
		'Справочная' => [
			20 => 'Таблица опыта',
			21 => 'Центр занятости',
			22 => 'Академия',
			23 => 'Модификаторы',
		],
	];

	public function index(Request $request): Response
	{
		$section = $request->integer('section', 1);
		$sections = collect(self::SECTIONS)->collapseWithKeys();

		abort_unless($sections->has($section), 404);

		$data = match ($section) {
			20 => ['levels' => $this->levels()],
			21 => ['workTypes' => WorkType::query()
				->with(['works' => fn ($query) => $query->orderBy('duration')])
				->orderBy('id')
				->get()],
			22 => ['professions' => Academy::query()->orderBy('level')->orderBy('duration')->orderBy('id')->get()],
			23 => [],
			default => ['items' => $this->items($section)],
		};

		return Inertia::render('Library', [
			'section' => $section,
			'title' => $sections->get($section),
			'groups' => collect(self::SECTIONS)->map(fn (array $items, string $title) => [
				'title' => $title,
				'sections' => collect($items)->map(fn (string $title, int $id) => [
					'id' => $id,
					'title' => $title,
				])->values(),
			])->values(),
			...$data,
		]);
	}

	private function items(int $section): AnonymousResourceCollection
	{
		$gifts = [100 => 1, 101 => 2, 102 => 3];

		$items = ShopItem::query()
			->with('item')
			->where('shop_id', isset($gifts[$section]) ? 4 : 1)
			->where('section_id', $gifts[$section] ?? $section)
			->joinRelationship('item', fn (PowerJoinClause $join) => $join->as('item'))
			->orderBy('item.req_level')
			->orderBy('item.id')
			->get();

		return ShopItemResource::collection($items);
	}

	private function levels(): array
	{
		$gold = 0;
		$updates = 0;
		$base = 0;
		$result = [];

		foreach (Level::query()->orderBy('exp')->orderBy('id')->get() as $level) {
			$gold += $level->credits;
			$updates += $level->updates;

			$result[] = [
				'id' => $level->id,
				'level' => $level->level,
				'up' => $level->up,
				'gold' => $level->credits,
				'totalGold' => $gold,
				'updates' => $level->updates,
				'totalUpdates' => $updates,
				'exp' => $level->exp,
				'base' => $level->base,
				'wins' => $base > 0 ? round($level->exp / $base) : 0,
			];

			$base = $level->base;
		}

		return $result;
	}
}