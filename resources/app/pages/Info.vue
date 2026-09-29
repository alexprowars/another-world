<template>
	<Head :title="'Информация о персонаже — ' + page.person.name" />
	<main class="person-info mx-auto max-w-6xl p-4">
		<h1 class="mb-6 text-center text-xl font-bold">Информация о персонаже — {{ page.person.name }}</h1>
		<BlockedNotice v-if="page.person.blocked" :reason="page.block_reason" class="mb-4" />

		<div class="grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_260px_minmax(0,1fr)]">
			<div class="space-y-6">
				<Stats :stats="page.person.stats" />
				<Gifts :gifts="page.gifts" :has-more="page.has_more_gifts" :person-id="page.person.id" />
			</div>
			<div class="flex justify-center">
				<PersonView :person="page.person" readonly />
			</div>
			<div class="space-y-6">
				<Statistics :person="page.person" />
				<Features :person="page.person" />
			</div>
		</div>

		<Friends :friends="page.friends" :friend-of="page.friend_of" class="mt-6 border-t pt-4" />
		<Questionnaire :person="page.person" class="mt-6 border-t pt-4" />
		<PrivateInfo v-if="page.private_info" :info="page.private_info" class="mt-6 border-t pt-4" />
	</main>
</template>

<script setup>
	import { Head } from '@inertiajs/vue3';
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
