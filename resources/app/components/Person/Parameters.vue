<template>
	<div class="person-parameters">
		<dl class="person-stat-list person-progression">
			<div>
				<dt>Уровень</dt>
				<dd>
					{{ player.level }}
					<small>[{{ player.level_up?.up || 0 }}]</small>
				</dd>
			</div>
			<div>
				<dt>Опыт</dt>
				<dd>{{ player.exp }}</dd>
			</div>
			<div :title="remainingExp === null ? 'Максимальный уровень' : 'Осталось ' + remainingExp + ' очков опыта'">
				<dt>До уровня</dt>
				<dd>{{ player.level_up?.exp ?? '—' }}</dd>
			</div>
			<div v-if="player.profession">
				<dt>Профессия</dt>
				<dd>{{ $t('profession.' + player.profession) }}</dd>
			</div>
			<div v-if="player.tribe">
				<dt>Клан</dt>
				<dd>{{ player.tribe.name }}</dd>
			</div>
		</dl>
		<dl class="person-currencies">
			<div>
				<dt>
					<img src="/assets/images/currencies/gold.png" alt="" />
					Золото
				</dt>
				<dd>{{ player.gold }}</dd>
			</div>
			<div>
				<dt>
					<img src="/assets/images/currencies/platinum.png" alt="" />
					Платина
				</dt>
				<dd>{{ player.credits }}</dd>
			</div>
		</dl>
		<section class="person-stat-section">
			<h3>Основные параметры</h3>
			<div class="person-attributes">
				<Popper v-for="stat in stats" :key="stat.key" class="person-attribute">
					<button type="button" class="person-attribute-value">
						<img :src="'/assets/images/stats/' + stat.key + '.png'" class="person-stat-icon" alt="" />
						<span>{{ $t('stats.' + stat.key) }}</span>
						<b>{{ player[stat.key] }}</b>
					</button>
					<template #content>
						<dl class="person-stat-tooltip">
							<div>
								<dt>{{ $t('stats.' + stat.key) }}</dt>
								<dd>{{ player[stat.key] }}</dd>
							</div>
							<div>
								<dt>Своя</dt>
								<dd>{{ player['s_' + stat.key] }}</dd>
							</div>
							<div>
								<dt>Эффекты</dt>
								<dd>{{ player[stat.key] - player['s_' + stat.key] }}</dd>
							</div>
						</dl>
					</template>
				</Popper>
			</div>
			<Link v-if="player.updates" href="/person/updates" class="person-free-stats">
				Распределить параметры
				<b>+{{ player.updates }}</b>
			</Link>
		</section>
		<div v-if="player.poison" class="person-poison">
			Отравление
			<b>{{ player.poison }}%</b>
		</div>
		<div class="person-combat">
			<dl class="person-stat-list">
				<div>
					<dt>
						<img src="/assets/images/stats/strength.png" class="person-stat-icon" alt="" />
						Физ. урон
					</dt>
					<dd>{{ player.damage_min }}–{{ player.damage_max }}</dd>
				</div>
				<div>
					<dt>
						<img src="/assets/images/stats/magic.png" class="person-stat-icon" alt="" />
						Маг. урон
					</dt>
					<dd>{{ Math.round(player.magic_min) }}–{{ Math.round(player.magic_max) }}</dd>
				</div>
			</dl>
			<div class="person-armor">
				<span class="person-armor-label">
					<img src="/assets/images/stats/armor.png" class="person-stat-icon" alt="" />
					Броня
				</span>
				<div>
					<span v-for="(title, index) in armorParts" :key="title" :title="title">{{ player['armor' + (index + 1)] }}</span>
				</div>
			</div>
			<dl class="person-stat-list person-modifiers">
				<div v-for="stat in combatStats" :key="stat.key">
					<dt>{{ stat.label }}</dt>
					<dd>{{ player[stat.key] }}</dd>
				</div>
			</dl>
		</div>
	</div>
</template>

<script setup>
	import { computed } from 'vue';
	import { Link } from '@inertiajs/vue3';
	import Popper from '~/components/Popper.vue';

	const props = defineProps({ player: Object });
	const remainingExp = computed(() => (props.player.level_up ? Math.max(0, props.player.level_up.exp - props.player.exp) : null));
	const stats = [{ key: 'strength' }, { key: 'dexterity' }, { key: 'agility' }, { key: 'vitality' }, { key: 'magic' }, { key: 'intelligence' }];
	const armorParts = ['Броня головы', 'Броня груди', 'Броня живота', 'Броня пояса', 'Броня ног'];
	const combatStats = [
		{ key: 'krit', label: 'Крит' },
		{ key: 'mkrit', label: 'Мощность крита' },
		{ key: 'unkrit', label: 'Антикрит' },
		{ key: 'uv', label: 'Уворот' },
		{ key: 'unuv', label: 'Антиуворот' },
		{ key: 'pblock', label: 'Пробитие блока' },
		{ key: 'mblock', label: 'Мощность блока' },
		{ key: 'pbr', label: 'Пробитие брони' },
		{ key: 'kbr', label: 'Крепкость брони' },
	];
</script>
