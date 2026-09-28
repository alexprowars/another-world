<template>
	<article class="inventory-item">
		<ItemPopover :item="item" :player="player" class="inventory-item-preview">
			<button type="button" class="inventory-item-image" :title="'Информация: ' + item.title">
				<img :src="'/assets/images/items/' + item.type + '/' + item.code + '.gif'" :alt="item.title" />
			</button>
		</ItemPopover>
		<div class="inventory-item-description">
			<h3>{{ item.title }}</h3>
			<p class="inventory-item-type">
				{{ $t('weapon.' + item.type) }}
			</p>
			<dl class="inventory-item-meta">
				<div>
					<dt>Уровень</dt>
					<dd :class="{ 'is-unmet': player.level < item.requirements?.level }">
						{{ item.requirements?.level || 0 }}
					</dd>
				</div>
				<div v-if="item.wearout_max">
					<dt>Износ</dt>
					<dd :class="{ 'is-unmet': item.wearout >= item.wearout_max }">{{ item.wearout }} / {{ item.wearout_max }}</dd>
				</div>
				<div>
					<dt>Цена</dt>
					<dd>{{ item.price }} {{ item.price_type === 1 ? 'пл.' : 'зол.' }}</dd>
				</div>
			</dl>
			<p v-if="item.engraving" class="inventory-item-engraving">«{{ item.engraving }}»</p>
		</div>
		<div class="inventory-item-actions">
			<button type="button" class="ui-button ui-button--compact" @click="confirmWear">Надеть</button>
			<button v-if="item.can_use" type="button" class="ui-button ui-button--compact ui-button--secondary" @click="useItem">Использовать</button>
			<button v-if="item.can_drop" type="button" class="ui-text-button" :disabled="dropping" @click="confirmDrop">Выбросить</button>
		</div>
	</article>
</template>

<script setup>
	import { openConfirmModal } from '~/composables/useModals.js';
	import ItemPopover from './ItemPopover.vue';

	const props = defineProps({
		item: {
			type: Object,
			required: true,
		},
		player: {
			type: Object,
			required: true,
		},
		dropping: Boolean,
	});

	const emit = defineEmits(['wear', 'drop', 'use']);

	function confirmWear() {
		openConfirmModal('Рюкзак', 'Вы действительно хотите надеть эту вещь?', [
			{ title: 'Нет' },
			{
				title: 'Да',
				handler() {
					emit('wear', props.item);
				},
			},
		]);
	}

	function confirmDrop() {
		if (props.dropping || !props.item.can_drop) {
			return;
		}

		openConfirmModal('Рюкзак', 'Вы действительно хотите выбросить этот предмет? Восстановить его будет нельзя.', [
			{ title: 'Нет' },
			{
				title: 'Да',
				handler() {
					emit('drop', props.item);
				},
			},
		]);
	}

	function useItem() {
		emit('use', props.item);
	}
</script>
