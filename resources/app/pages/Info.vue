<template>
	<Head :title="'Информация о персонаже — ' + page.person.name" />
	<div class="person-info-shell">
		<div class="person-info-masthead">
			<a href="/" class="person-info-brand">Another World</a>
			<span>Досье персонажа · № {{ page.person.id }}</span>
		</div>
		<main class="location-card person-info">
			<CityHeader :title="'Информация о персонаже — ' + page.person.name" />
			<div class="location-card-body">
				<BlockedNotice v-if="page.person.blocked" :reason="page.block_reason" />

				<div class="person-info-overview">
					<Stats :stats="page.person.stats" />
					<section class="person-info-portrait ui-panel">
						<PersonView :person="page.person" readonly />
					</section>
					<div class="person-info-column">
						<Statistics :person="page.person" />
						<Features :person="page.person" />
					</div>
				</div>

				<Gifts :gifts="page.gifts" :has-more="page.has_more_gifts" :person-id="page.person.id" />
				<div class="person-info-details">
					<Questionnaire :person="page.person" />
					<Friends :friends="page.friends" :friend-of="page.friend_of" />
				</div>
				<PrivateInfo v-if="page.private_info" :info="page.private_info" />
			</div>
		</main>
	</div>
</template>

<script setup>
	import { Head } from '@inertiajs/vue3';
	import CityHeader from '~/components/CityHeader.vue';
	import PersonView from '~/components/PersonView.vue';
	import BlockedNotice from '~/components/Info/BlockedNotice.vue';
	import Features from '~/components/Info/Features.vue';
	import Friends from '~/components/Info/Friends.vue';
	import Gifts from '~/components/Info/Gifts.vue';
	import PrivateInfo from '~/components/Info/PrivateInfo.vue';
	import Questionnaire from '~/components/Info/Questionnaire.vue';
	import Statistics from '~/components/Info/Statistics.vue';
	import Stats from '~/components/Info/Stats.vue';

	defineOptions({
		layout: []
	});

	defineProps({
		page: Object
	});
</script>
