<template>
	<div class="service-section">
		<header class="service-heading">
			<h2>Государственный архив кланов</h2>
			<p>Истории и уставы зарегистрированных объединений.</p>
		</header>
		<details v-for="tribe in tribes" :key="tribe.id" class="administration-tribe">
			<summary>
				<img :src="'/assets/images/tribe/' + tribe.id + '.gif'" :alt="tribe.short" />
				<span>{{ tribe.short }} — {{ tribe.name }}</span>
				<GameIcon name="chevron" />
			</summary>
			<div class="service-panel-body">
				<p class="service-prose">{{ tribe.about || 'Описание клана пока не добавлено.' }}</p>
				<template v-if="tribe.laws">
					<h3>Устав клана</h3>
					<p class="service-prose">{{ tribe.laws }}</p>
				</template>
			</div>
		</details>
		<div v-if="!tribes.length" class="ui-empty" role="status">
			<GameIcon name="clan" />
			<h3>Кланы пока не зарегистрированы</h3>
			<p>После регистрации сведения о кланах появятся в архиве.</p>
		</div>
	</div>
</template>

<script setup>
	import GameIcon from '~/components/Layout/GameIcon.vue';

	defineProps({
		tribes: Array,
	});
</script>
