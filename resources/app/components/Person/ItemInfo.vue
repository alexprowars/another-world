<template>
	<InfoPopoverContent :title="item.title" :subtitle="$t('weapon.' + item.type)">
		<dl v-if="item.wearout_max || item.price != null" class="info-popover-stats">
			<div v-if="item.wearout_max">
				<dt>Износ</dt>
				<dd :class="{ 'is-unmet': item.wearout >= item.wearout_max }">{{ item.wearout }} / {{ item.wearout_max }}</dd>
			</div>
			<div v-if="item.price != null">
				<dt>Цена</dt>
				<dd>{{ item.price }} {{ item.price_type === 1 ? 'пл.' : 'зол.' }}</dd>
			</div>
		</dl>
		<section v-if="requirements.length" class="info-popover-section">
			<h4>Требования</h4>
			<dl class="info-popover-stats">
				<div v-for="row in requirements" :key="row.key" :class="{ 'is-unmet': row.failed }">
					<dt>{{ row.label }}</dt>
					<dd>{{ row.value }}</dd>
				</div>
			</dl>
		</section>
		<section v-if="bonuses.length" class="info-popover-section">
			<h4>Параметры предмета</h4>
			<dl class="info-popover-stats">
				<div v-for="row in bonuses" :key="row.key">
					<dt>{{ row.label }}</dt>
					<dd>{{ row.value }}</dd>
				</div>
			</dl>
		</section>
		<section v-if="item.magic" class="info-popover-section">
			<h4>Встроенная магия</h4>
			<div v-html="item.magic"></div>
		</section>
		<section v-if="item.engraving" class="info-popover-section">
			<h4>Гравировка</h4>
			<p>{{ item.engraving }}</p>
		</section>
		<section v-if="item.about" class="info-popover-section">
			<h4>Описание</h4>
			<div v-html="item.about"></div>
		</section>
		<template v-if="$slots.actions" #footer>
			<slot name="actions" />
		</template>
	</InfoPopoverContent>
</template>

<script setup>
	import { computed } from 'vue';
	import { useI18n } from 'vue-i18n';
	import InfoPopoverContent from '~/components/InfoPopoverContent.vue';

	const props = defineProps({
		item: {
			type: Object,
			required: true,
		},
		player: {
			type: Object,
			default: () => ({}),
		},
	});

	const { t } = useI18n();

	const requirements = computed(() =>
		[
			requirement('profession', 'Профессия', value => t('profession.' + value)),
			requirement('level', 'Уровень'),
			requirement('strength', t('stats.strength')),
			requirement('dexterity', t('stats.dexterity')),
			requirement('agility', t('stats.agility')),
			requirement('vitality', t('stats.vitality')),
			requirement('magic', t('stats.magic')),
			requirement('intelligence', t('stats.intelligence')),
		].filter(Boolean),
	);

	const bonuses = computed(() =>
		[
			bonus('poison', 'Отравление'),
			bonus('hp', 'Уровень жизни'),
			bonus('energy', 'Уровень энергии'),
			damageBonus(),
			bonus('strength', 'Сила'),
			bonus('dexterity', 'Удача'),
			bonus('agility', 'Ловкость'),
			bonus('vitality', 'Выносливость'),
			bonus('intelligence', 'Разум'),
			bonus('armor1', 'Броня головы'),
			bonus('armor2', 'Броня корпуса'),
			bonus('armor3', 'Броня живота'),
			bonus('armor4', 'Броня пояса'),
			bonus('armor5', 'Броня ног'),
			bonus('krit', 'Крит'),
			bonus('unkrit', 'Антикрит'),
			bonus('uv', 'Уворот'),
			bonus('unuv', 'Антиуворот'),
			bonus('mkrit', 'Мощность крита'),
			bonus('pblock', 'Пробой блока'),
			bonus('mblock', 'Мощность блока'),
			bonus('pbr', 'Пробой брони'),
			bonus('kbr', 'Крепость брони'),
			bonus('metk', 'Меткость'),
		].filter(Boolean),
	);

	function requirement(key, label, format = null) {
		const required = props.item.requirements?.[key];

		if (!required) {
			return null;
		}

		const current = props.player[key] ?? 0;
		const failed = key === 'profession' ? required !== current : required > current;

		return {
			key,
			label,
			value: format ? format(required) : required,
			failed,
		};
	}

	function bonus(key, label) {
		const value = props.item[key];

		if (!value) {
			return null;
		}

		return {
			key,
			label,
			value: signed(value),
		};
	}

	function damageBonus() {
		if (!props.item.min && !props.item.max) {
			return null;
		}

		return {
			key: 'damage',
			label: 'Урон',
			value: `${props.item.min ?? 0}–${props.item.max ?? 0}`,
		};
	}

	function signed(value) {
		return value > 0 ? `+${value}` : String(value);
	}
</script>
