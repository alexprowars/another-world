<template>
	<div class="battle-impact-form text-center">
		<div class="battle-impact-form__header">
			<div class="battle-impact-form__column battle-impact-form__column--impact">
				<b><small><a href="#" @click.prevent="randomFill('impact')">Атака</a>&nbsp;(<span id="colImp">{{ impactsLeft }}</span>)</small></b>
			</div>
			<div class="battle-impact-form__column battle-impact-form__column--block">
				<b><small><a href="#" @click.prevent="randomFill('block')">Защита</a> (<span id="colbl">{{ blocksLeft }}</span>)</small></b>
			</div>
		</div>
		<div class="battle-impact-form__body">
			<div class="battle-impact-form__column battle-impact-form__column--impact">
				<div class="battle-impact-form__areas">
					<div v-for="area in areas" class="battle-impact-form__area" :style="{ height: `${area.height}px`, backgroundImage: 'url(' + area.background + ')' }" @click="toggleImpact(area.key)">
						<img :src="`/assets/images/battle/impact_action_${selectedImpacts[area.key] ? 'true' : 'false'}.gif`" alt="">
					</div>
				</div>
			</div>
			<div class="battle-impact-form__column battle-impact-form__column--block">
				<div class="battle-impact-form__areas">
					<div v-for="area in areas" class="battle-impact-form__area" :style="{ height: `${area.height}px`, backgroundImage: 'url(' + area.background + ')' }" @click="toggleBlock(area.key)">
						<img :src="`/assets/images/battle/block_action_${selectedBlocks[area.key] ? 'true' : 'false'}.gif`" alt="">
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="text-center">
		<input type="checkbox" name="auto" id="autofight" :checked="auto" @change="$emit('update:auto', $event.target.checked)">
		<label for="autofight"> - автоматический ход, если выбран удар и блок</label>
	</div>
</template>

<script setup>
	import { computed, reactive, watch } from 'vue';

	const props = defineProps({
		blocksCount: {
			type: Number,
			required: true,
		},
		impactsCount: {
			type: Number,
			required: true,
		},
		auto: {
			type: Boolean,
			default: false,
		},
	});

	const emit = defineEmits(['update:auto', 'complete']);

	const areas = [
		{ key: 'head', height: 27, background: '/assets/images/battle/f_head.gif' },
		{ key: 'case', height: 25, background: '/assets/images/battle/f_grud.gif' },
		{ key: 'stomach', height: 24, background: '/assets/images/battle/f_zhiv.gif' },
		{ key: 'belt', height: 27, background: '/assets/images/battle/f_poyas.gif' },
		{ key: 'legs', height: 27, background: '/assets/images/battle/f_nogi.gif' },
	];

	const selectedImpacts = reactive(emptySelection());
	const selectedBlocks = reactive(emptySelection());

	const impactsLeft = computed(() => props.impactsCount - selectedCount(selectedImpacts));
	const blocksLeft = computed(() => props.blocksCount - selectedCount(selectedBlocks));

	watch(() => [props.blocksCount, props.impactsCount], reset, { immediate: true });
	watch(() => [props.auto, impactsLeft.value, blocksLeft.value], autoSubmit);

	function emptySelection() {
		return {
			head: false,
			case: false,
			stomach: false,
			belt: false,
			legs: false,
		};
	}

	function reset() {
		Object.assign(selectedImpacts, emptySelection());
		Object.assign(selectedBlocks, emptySelection());
	}

	function selectedCount(selection) {
		return Object.values(selection).filter(Boolean).length;
	}

	function toggleImpact(key) {
		if (selectedImpacts[key]) {
			selectedImpacts[key] = false;
		} else if (impactsLeft.value > 0) {
			selectedImpacts[key] = true;
		}

		autoSubmit();
	}

	function toggleBlock(key) {
		if (selectedBlocks[key]) {
			selectedBlocks[key] = false;
		} else if (blocksLeft.value > 0) {
			selectedBlocks[key] = true;
		}

		autoSubmit();
	}

	function randomFill(type) {
		const selection = type === 'impact' ? selectedImpacts : selectedBlocks;
		const left = type === 'impact' ? impactsLeft : blocksLeft;

		while (left.value > 0) {
			selection[areas[Math.floor(Math.random() * areas.length)].key] = true;
		}

		autoSubmit();
	}

	function autoSubmit() {
		if (props.auto && props.impactsCount > 0 && props.blocksCount > 0 && isImpactsComplete() && isBlocksComplete()) {
			emit('complete');
		}
	}

	function isImpactsComplete() {
		return impactsLeft.value === 0;
	}

	function isBlocksComplete() {
		return blocksLeft.value === 0;
	}

	function payload() {
		return {
			headImpact: selectedImpacts.head ? 1 : 0,
			caseImpact: selectedImpacts.case ? 1 : 0,
			stomachImpact: selectedImpacts.stomach ? 1 : 0,
			beltImpact: selectedImpacts.belt ? 1 : 0,
			legsImpact: selectedImpacts.legs ? 1 : 0,
			headBlock: selectedBlocks.head ? 1 : 0,
			caseBlock: selectedBlocks.case ? 1 : 0,
			stomachBlock: selectedBlocks.stomach ? 1 : 0,
			beltBlock: selectedBlocks.belt ? 1 : 0,
			legsBlock: selectedBlocks.legs ? 1 : 0,
		};
	}

	defineExpose({
		isImpactsComplete,
		isBlocksComplete,
		payload,
	});
</script>