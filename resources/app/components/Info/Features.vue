<template>
	<section class="person-info-panel ui-panel">
		<h2 class="person-info-panel-heading"><GameIcon name="gem" />Особенности</h2>
		<ul v-if="hasFeatures" class="person-info-features">
			<li v-if="person.zodiac" class="person-info-zodiac">
				<img :src="'/assets/images/zodiac/' + person.zodiac.id + '.png'" :alt="person.zodiac.name" width="30" height="30"/>
				{{ person.zodiac.name }}
			</li>
			<li v-if="person.admin"><span class="ui-badge">Администратор Another World</span></li>
			<li v-if="person.vip"><span class="ui-badge">VIP-персона Another World</span></li>
			<li v-if="person.battle_fury">
				Боевая ярость: опыт в боях увеличен в 2 раза до {{ $formatDate(person.battle_fury, 'DD.MM.YYYY HH:mm') }}.
			</li>
			<li v-if="person.prison" class="person-info-danger">
				В тюрьме до {{ $formatDate(person.prison, 'DD.MM.YYYY HH:mm') }}.
				<span v-if="person.prison_reason">Причина: {{ person.prison_reason }}</span>
			</li>
			<li v-if="person.silence_until">Запрещено общение в чате до {{ $formatDate(person.silence_until, 'DD.MM.YYYY HH:mm') }}.</li>
			<li v-if="person.injury_until">Персонаж травмирован до {{ $formatDate(person.injury_until, 'DD.MM.YYYY HH:mm') }}.</li>
		</ul>
		<p v-else class="person-info-empty">Особенностей пока нет.</p>
	</section>
</template>

<script setup>
	import { computed } from 'vue';
	import GameIcon from '~/components/Layout/GameIcon.vue';

	const props = defineProps({
		person: Object,
	});

	const hasFeatures = computed(() => {
		const person = props.person;

		return person.zodiac
			|| person.admin
			|| person.vip
			|| person.battle_fury
			|| person.prison
			|| person.silence_until
			|| person.injury_until;
	});
</script>
