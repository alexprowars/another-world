<template>
	<div class="battle-impact-form text-center">
		<div class="battle-impact-form__header">
			<div class="battle-impact-form__column battle-impact-form__column--impact">
				<button type="button" class="battle-impact-form__heading" title="Выбрать случайные зоны атаки" @click="randomFill('impact')">
					<GameIcon name="swords" /> Атака <span id="colImp">{{ impactsLeft }}</span>
				</button>
			</div>
			<div class="battle-impact-form__column battle-impact-form__column--block">
				<button type="button" class="battle-impact-form__heading" title="Выбрать случайные зоны защиты" @click="randomFill('block')">
					<GameIcon name="shield" /> Защита <span id="colbl">{{ blocksLeft }}</span>
				</button>
			</div>
		</div>
		<div class="battle-impact-form__body">
			<div class="battle-impact-form__column battle-impact-form__column--impact">
				<div class="battle-impact-form__areas">
					<button
						v-for="area in areas"
						:key="area.key"
						type="button"
						class="battle-impact-form__area"
						:class="{ 'is-selected': selectedImpacts[area.key] }"
						:title="area.title + (selectedImpacts[area.key] ? ' — выбрано' : '')"
						:style="{ height: `${area.height}px`, backgroundImage: 'url(' + area.background + ')' }"
						@click="toggleImpact(area.key)"
					>
						<img :src="`/assets/images/battle/impact_action_${selectedImpacts[area.key] ? 'true' : 'false'}.gif`" alt="" />
					</button>
				</div>
			</div>
			<div class="battle-impact-form__column battle-impact-form__column--block">
				<div class="battle-impact-form__areas">
					<button
						v-for="area in areas"
						:key="area.key"
						type="button"
						class="battle-impact-form__area"
						:class="{ 'is-selected': selectedBlocks[area.key] }"
						:title="area.title + (selectedBlocks[area.key] ? ' — выбрано' : '')"
						:style="{ height: `${area.height}px`, backgroundImage: 'url(' + area.background + ')' }"
						@click="toggleBlock(area.key)"
					>
						<img :src="`/assets/images/battle/block_action_${selectedBlocks[area.key] ? 'true' : 'false'}.gif`" alt="" />
					</button>
				</div>
			</div>
		</div>
	</div>

	<div class="battle-auto">
		<input type="checkbox" name="auto" id="autofight" :checked="auto" @change="$emit('update:auto', $event.target.checked)" />
		<label for="autofight">Автоматический ход после выбора удара и блока</label>
	</div>
</template>

<script setup>
	import { computed, reactive, watch } from 'vue';
	import GameIcon from '~/components/Layout/GameIcon.vue';

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
		{ key: 'head', title: 'Голова', height: 27, background: '/assets/images/battle/f_head.gif' },
		{ key: 'case', title: 'Грудь', height: 25, background: '/assets/images/battle/f_grud.gif' },
		{ key: 'stomach', title: 'Живот', height: 24, background: '/assets/images/battle/f_zhiv.gif' },
		{ key: 'belt', title: 'Пах', height: 27, background: '/assets/images/battle/f_poyas.gif' },
		{ key: 'legs', title: 'Ноги', height: 27, background: '/assets/images/battle/f_nogi.gif' },
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
