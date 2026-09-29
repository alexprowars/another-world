<template>
	<li class="works-entry">
		<div class="works-entry-title">
			<span class="works-entry-number">{{ String(number).padStart(2, '0') }}</span>
			<h4>{{ work.title }}</h4>
		</div>
		<div class="works-entry-stat">
			<span class="works-stat-label">Срок работы</span>
			<span class="works-stat-value">
				<GameIcon name="hourglass" />
				{{ $formatTime(work.duration) }}
			</span>
		</div>
		<div class="works-entry-stat">
			<span class="works-stat-label">Награда</span>
			<strong class="works-stat-value works-reward">
				<GameIcon name="coins" />
				{{ work.price }} зол.
			</strong>
		</div>
		<button type="button" class="ui-button" :disabled="processing" @click="emit('start')">
			Работать
			<GameIcon name="forward" />
		</button>
	</li>
</template>

<script setup>
	import GameIcon from '~/components/Layout/GameIcon.vue';

	defineProps({
		work: Object,
		number: Number,
		processing: Boolean,
	});

	const emit = defineEmits(['start']);
</script>
