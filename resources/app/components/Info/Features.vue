<template>
	<section>
		<h2 class="mb-3 font-bold">Особенности</h2>
		<ul class="space-y-2">
			<li v-if="person.zodiac" class="flex items-center gap-2">
				<img :src="'/assets/images/zodiac/' + person.zodiac.id + '.gif'" :alt="person.zodiac.name" />
				{{ person.zodiac.name }}
			</li>
			<li v-if="person.admin">Администратор Another World</li>
			<li v-if="person.vip">VIP-персона Another World</li>
			<li v-if="person.prison_until" class="text-red-700">
				В тюрьме до {{ $formatDate(person.prison_until, 'DD.MM.YYYY HH:mm') }}.
				<span v-if="person.prison_reason">Причина: {{ person.prison_reason }}</span>
			</li>
			<li v-if="person.silence_until">Запрещено общение в чате до {{ $formatDate(person.silence_until, 'DD.MM.YYYY HH:mm') }}.</li>
			<li v-if="person.injury_until">Персонаж травмирован до {{ $formatDate(person.injury_until, 'DD.MM.YYYY HH:mm') }}.</li>
		</ul>
	</section>
</template>

<script setup>
	defineProps({
		person: Object,
	});
</script>
