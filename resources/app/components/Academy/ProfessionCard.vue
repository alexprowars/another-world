<template>
	<article class="ui-panel service-panel academy-profession">
		<header class="service-panel-heading">
			<GameIcon name="book" />
			<h2>{{ profession.title }}</h2>
		</header>
		<div class="service-panel-body">
			<dl class="service-facts">
				<div>
					<dt>Уровень</dt>
					<dd>{{ profession.level }}</dd>
				</div>
				<div>
					<dt>Срок обучения</dt>
					<dd>{{ $formatTime(profession.duration) }}</dd>
				</div>
				<div>
					<dt>Стоимость</dt>
					<dd>{{ profession.price }} зол.</dd>
				</div>
			</dl>
			<button type="button" class="ui-button" :disabled="processing" @click="emit('learn')">
				{{ learning ? 'Начинаем обучение…' : 'Обучаться' }}
			</button>
		</div>
	</article>
</template>

<script setup>
	import GameIcon from '~/components/Layout/GameIcon.vue';

	defineProps({
		profession: Object,
		processing: Boolean,
		learning: Boolean,
	});

	const emit = defineEmits(['learn']);
</script>
