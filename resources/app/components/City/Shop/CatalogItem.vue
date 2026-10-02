<template>
	<article class="storefront-item">
		<div class="storefront-item-top">
			<ItemPopover :item="details" :player="player">
				<button type="button" class="storefront-item-image" :title="'Характеристики: ' + product.title">
					<img :src="getItemImagePath(product)" :alt="product.title" loading="lazy" />
				</button>
			</ItemPopover>
			<div class="storefront-item-heading">
				<p>{{ $t('weapon.' + product.type) }}</p>
				<h3>{{ product.title }}</h3>
			</div>
		</div>
		<dl class="storefront-item-stats">
			<div :class="{ 'is-unmet': (player.level ?? 0) < (product.requirements?.level || 0) }">
				<dt>Уровень</dt>
				<dd>{{ product.requirements?.level || 0 }}</dd>
			</div>
			<div v-if="owned ? product.wearout_max : product.wearout">
				<dt>{{ owned ? 'Износ' : 'Долговечность' }}</dt>
				<dd>{{ owned ? product.wearout + ' / ' + product.wearout_max : product.wearout }}</dd>
			</div>
			<div v-if="!owned && item.stock != null">
				<dt>На складе</dt>
				<dd>{{ item.stock }} шт.</dd>
			</div>
			<div v-if="!owned && item.delivery">
				<dt>Завоз</dt>
				<dd>{{ item.delivery }}</dd>
			</div>
			<div v-if="product.life">
				<dt>Срок жизни</dt>
				<dd>{{ product.life / 86400 }} дн.</dd>
			</div>
			<div v-if="product.mana">
				<dt>Затраты маны</dt>
				<dd>{{ product.mana }}</dd>
			</div>
		</dl>
		<section v-if="requirements.length" class="storefront-item-requirements">
			<h4>Требования</h4>
			<dl class="storefront-item-stats">
				<div v-for="requirement in requirements" :key="requirement.key" class="is-unmet">
					<dt>{{ requirement.label }}</dt>
					<dd>{{ requirement.value }}</dd>
				</div>
			</dl>
		</section>
		<p v-if="product.engraving" class="storefront-item-engraving">«{{ product.engraving }}»</p>
		<slot name="details" />
		<div class="storefront-item-footer">
			<slot name="price">
				<div class="storefront-item-price">
					<span>{{ selling ? 'Цена продажи' : 'Государственная цена' }}</span>
					<strong>
						<img :src="'/assets/images/currencies/' + (platinum ? 'platinum' : 'gold') + '.png'" alt="" />
						{{ price }}
						<small>{{ platinum ? 'пл.' : 'зол.' }}</small>
					</strong>
				</div>
			</slot>
			<slot name="actions">
				<button type="button" class="ui-button" :disabled="processing" @click="$emit('trade')">{{ selling ? 'Продать' : 'Купить' }}</button>
			</slot>
		</div>
	</article>
</template>

<script setup>
	import { computed } from 'vue';
	import { useI18n } from 'vue-i18n';
	import { getUnmetItemRequirements } from '~/utils/itemRequirements.js';
	import { getItemImagePath } from '~/utils/itemImage.js';
	import ItemPopover from '~/components/Person/ItemPopover.vue';

	const props = defineProps({
		item: { type: Object, required: true },
		player: { type: Object, required: true },
		selling: Boolean,
		inventoryItem: Boolean,
		processing: Boolean,
	});
	defineEmits(['trade']);
	const { t } = useI18n();

	const owned = computed(() => props.selling || props.inventoryItem);
	const product = computed(() => (owned.value ? props.item : props.item.item));
	const requirements = computed(() =>
		getUnmetItemRequirements(product.value, props.player)
			.filter(([key]) => key !== 'level')
			.map(([key, value]) => ({
				key,
				label: key === 'profession' ? 'Профессия' : t('stats.' + key),
				value: key === 'profession' ? t('profession.' + value) : value,
			})),
	);

	const platinum = computed(() => (owned.value ? product.value.price_type === 1 : product.value.credits > 0));

	const price = computed(() => {
		if (props.selling) {
			return product.value.price_sell;
		}

		if (owned.value) {
			return product.value.price;
		}

		return product.value.price_buy;
	});

	const details = computed(() =>
		owned.value
			? product.value
			: {
					...product.value,
					...product.value.bonuses,
					price: price.value,
					price_type: platinum.value ? 1 : 0,
					wearout: 0,
					wearout_max: product.value.wearout,
				},
	);
</script>
