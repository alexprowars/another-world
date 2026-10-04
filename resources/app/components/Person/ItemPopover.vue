<template>
	<Tooltip
		ref="tooltip"
		placement="right-start"
		popper-class="info-popover-popper"
		:arrow-padding="6"
		:triggers="['hover', 'focus', 'touch']"
		:popper-triggers="[]"
		:delay="{ show: 150, hide: 180 }"
	>
		<slot />
		<template #popper>
			<div @mouseenter="tooltip.show()" @mouseleave="tooltip.hide()">
				<ItemInfo v-if="item" :item="item" :player="player">
					<template v-if="canUse && item.can_use" #actions>
						<button type="button" class="ui-button ui-button--compact" @click="useItem">Использовать</button>
					</template>
				</ItemInfo>
				<slot v-else name="empty" />
			</div>
		</template>
	</Tooltip>
</template>

<script setup>
	import { useTemplateRef } from 'vue';
	import { Tooltip } from 'floating-vue';
	import ItemInfo from './ItemInfo.vue';

	const props = defineProps({
		item: Object,
		player: Object,
		canUse: { type: Boolean, default: false },
	});

	const emit = defineEmits(['use']);
	const tooltip = useTemplateRef('tooltip');

	function useItem() {
		tooltip.value.hide();
		emit('use', props.item);
	}
</script>
