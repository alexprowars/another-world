<template>
	<article class="ui-panel library-item">
		<header class="library-item-heading">
			<div class="library-item-image">
				<img :src="'/assets/images/items/' + item.type + '/' + item.code + '.gif'" :alt="item.title" loading="lazy" />
			</div>
			<div>
				<p class="library-eyebrow">{{ $t('weapon.' + item.type) }}</p>
				<h2>{{ item.title }}</h2>
				<span class="ui-badge library-item-level">Уровень {{ item.requirements?.level || 0 }}</span>
			</div>
		</header>

		<div class="library-item-price">
			<span>Государственная цена</span>
			<div>
				<strong v-if="Number(item.price) > 0 || !Number(item.credits)">
					<img src="/assets/images/currencies/gold.png" alt="" />
					{{ item.price }}
					<small>зол.</small>
				</strong>
				<strong v-if="Number(item.credits) > 0">
					<img src="/assets/images/currencies/platinum.png" alt="" />
					{{ item.credits }}
					<small>пл.</small>
				</strong>
			</div>
		</div>

		<dl v-if="item.wearout || item.life || item.mana" class="library-item-properties library-item-stats">
			<div v-if="item.wearout">
				<dt>Долговечность</dt>
				<dd>{{ item.wearout }}</dd>
			</div>
			<div v-if="item.life">
				<dt>Срок жизни</dt>
				<dd>{{ item.life / 86400 }} дн.</dd>
			</div>
			<div v-if="item.mana">
				<dt>Затраты маны</dt>
				<dd>{{ item.mana }}</dd>
			</div>
		</dl>

		<div class="library-item-details">
			<section class="library-item-section">
				<h3>Требования</h3>
				<dl v-if="requirements.length" class="library-item-stats">
					<div v-for="row in requirements" :key="row.key">
						<dt>{{ row.label }}</dt>
						<dd>{{ row.value }}</dd>
					</div>
				</dl>
				<p v-else class="library-item-note">Нет требований</p>
			</section>
			<section class="library-item-section">
				<h3>Характеристики</h3>
				<dl v-if="bonuses.length" class="library-item-stats">
					<div v-for="row in bonuses" :key="row.key" :class="{ 'is-negative': row.negative }">
						<dt>{{ row.label }}</dt>
						<dd>{{ row.value }}</dd>
					</div>
				</dl>
				<p v-else class="library-item-note">Нет дополнительных характеристик</p>
			</section>
		</div>

		<section v-if="item.magic" class="library-item-description">
			<h3>Встроенная магия</h3>
			<div v-html="item.magic"></div>
		</section>
		<section v-if="item.about" class="library-item-description">
			<h3>Описание</h3>
			<div v-html="item.about"></div>
		</section>
	</article>
</template>

<script setup>
	import { computed } from 'vue';
	import { useI18n } from 'vue-i18n';

	const props = defineProps({ item: { type: Object, required: true } });
	const { t } = useI18n();

	const requirements = computed(() =>
		Object.entries(props.item.requirements ?? {}).map(([key, value]) => ({
			key,
			label: key === 'level' ? 'Уровень' : key === 'profession' ? 'Профессия' : t('stats.' + key),
			value: key === 'profession' ? t('profession.' + value) : value,
		})),
	);

	const bonuses = computed(() =>
		Object.entries(props.item.bonuses ?? {}).map(([key, value]) => ({
			key,
			label: t('stats.' + key),
			value: value > 0 ? '+' + value : String(value),
			negative: value < 0,
		})),
	);
</script>
