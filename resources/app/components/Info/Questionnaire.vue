<template>
	<section class="person-info-panel ui-panel">
		<h2 class="person-info-panel-heading"><GameIcon name="book" />Анкетные данные</h2>
		<dl class="person-info-data">
			<div>
				<dt>Имя</dt>
				<dd>{{ person.name }}</dd>
			</div>
			<div>
				<dt>Пол</dt>
				<dd>{{ person.gender === 'M' ? 'Мужской' : person.gender === 'F' ? 'Женский' : 'Не указан' }}</dd>
			</div>
			<div v-if="person.city">
				<dt>Город</dt>
				<dd>{{ person.city }}</dd>
			</div>
		</dl>
		<div v-if="person.about" class="person-info-about">
			<h3>О себе</h3>
			<p>{{ person.about }}</p>
		</div>
	</section>
</template>

<script setup>
	import GameIcon from '~/components/Layout/GameIcon.vue';

	defineProps({
		person: Object,
	});
</script>
