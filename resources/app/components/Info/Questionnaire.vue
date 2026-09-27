<template>
	<section>
		<h2 class="mb-3 font-bold">Анкетные данные</h2>
		<p><b>Имя:</b> {{ person.name }}</p>
		<p><b>Пол:</b> {{ person.gender === 'M' ? 'Мужской' : (person.gender === 'F' ? 'Женский' : 'Не указан') }}</p>
		<p v-if="person.city"><b>Город:</b> {{ person.city }}</p>
		<template v-if="person.about">
			<p class="mt-3 font-bold">О себе:</p>
			<p class="whitespace-pre-line break-words">{{ person.about }}</p>
		</template>
	</section>
</template>

<script setup>
	defineProps({
		person: Object,
	});
</script>
