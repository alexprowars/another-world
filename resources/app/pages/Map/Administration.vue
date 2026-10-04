<template>
	<Head title="Администрация" />
	<ContentBlock title="Администрация">
		<template #actions>
			<MovementLink :to="location.exit" class="ui-icon-button" title="Назад">
				<GameIcon name="back" />
			</MovementLink>
			<Link :href="location.url + '/' + page.tab" class="ui-icon-button" title="Обновить">
				<GameIcon name="refresh" />
			</Link>
		</template>

		<nav class="ui-tabs ui-tabs--stacked">
			<Link
				class="ui-tab"
				v-for="tab in tabs"
				:key="tab.id"
				:href="location.url + '/' + tab.id"
				:class="{ 'is-active': page.tab === tab.id }"
			>
				<GameIcon :name="tab.icon" />
				{{ tab.title }}
			</Link>
		</nav>
		<RegistrationRules v-if="page.tab === 'rules'" :min-level="page.min_level" />
		<Requests
			v-else-if="page.tab === 'requests'"
			:request-price="page.request_price"
			:min-level="page.min_level"
			:has-request="page.has_request"
			:requests="page.requests"
		/>
		<Images v-else-if="page.tab === 'images'" :image-price="page.image_price" :images="page.images" />
		<ClanArchive v-else :tribes="page.tribes" />
	</ContentBlock>
</template>

<script setup>
	import MovementLink from '~/components/City/MovementLink.vue';
	import useLocation from '~/composables/useLocation.js';

	import { Head, Link } from '@inertiajs/vue3';
	import ContentBlock from '~/components/ContentBlock.vue';
	import GameIcon from '~/components/Layout/GameIcon.vue';
	import RegistrationRules from '~/components/Administration/RegistrationRules.vue';
	import Requests from '~/components/Administration/Requests.vue';
	import Images from '~/components/Administration/Images.vue';
	import ClanArchive from '~/components/Administration/ClanArchive.vue';

	const location = useLocation();

	defineProps({
		page: Object
	});

	const tabs = [
		{ id: 'rules', title: 'Правила регистрации', icon: 'clan' },
		{ id: 'requests', title: 'Подать заявку на проверку', icon: 'justice' },
		{ id: 'images', title: 'Образ', icon: 'character' },
		{ id: 'clans', title: 'Архив кланов', icon: 'book' },
	];
</script>
