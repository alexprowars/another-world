<template>
	<Tooltip
		ref="tooltip"
		placement="right-start"
		popper-class="item-info-popper"
		:triggers="['hover', 'focus', 'touch']"
		:popper-triggers="[]"
		:delay="{ show: 150, hide: 180 }"
	>
		<slot />
		<template #popper>
			<div @mouseenter="tooltip.show()" @mouseleave="tooltip.hide()">
				<ItemInfo v-if="item" :item="item" :player="player" />
				<slot v-else name="empty" />
			</div>
		</template>
	</Tooltip>
</template>

<script setup>
	import { useTemplateRef } from 'vue';
	import { Tooltip } from 'floating-vue';
	import ItemInfo from './ItemInfo.vue';

	defineProps({ item: Object, player: Object });
	const tooltip = useTemplateRef('tooltip');
</script>
