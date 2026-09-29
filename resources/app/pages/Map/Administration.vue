<template>
	<Head title="Администрация" />
	<ContentBlock title="Администрация">
		<template #actions>
			<Link href="/map/change/14" class="ui-icon-button" title="Назад">
				<GameIcon name="back" />
			</Link>
			<Link :href="'/map?section=' + page.section" class="ui-icon-button" title="Обновить">
				<GameIcon name="refresh" />
			</Link>
		</template>

		<nav class="ui-tabs ui-tabs--stacked">
			<Link
				class="ui-tab"
				v-for="(title, index) in sections"
				:key="index"
				:href="'/map?section=' + (index + 1)"
				:class="{ 'is-active': page.section === index + 1 }"
			>
				<GameIcon :name="['clan', 'justice', 'character', 'book'][index]" />
				{{ title }}
			</Link>
		</nav>
		<RegistrationRules v-if="page.section === 1" :min-level="page.min_level" />
		<Requests
			v-else-if="page.section === 2"
			:request-price="page.request_price"
			:min-level="page.min_level"
			:has-request="page.has_request"
			:requests="page.requests"
		/>
		<Images v-else-if="page.section === 3" :image-price="page.image_price" :images="page.images" />
		<ClanArchive v-else :tribes="page.tribes" />
	</ContentBlock>
</template>

<script setup>
	import { Head, Link } from '@inertiajs/vue3';
	import ContentBlock from '~/components/ContentBlock.vue';
	import GameIcon from '~/components/Layout/GameIcon.vue';
	import RegistrationRules from '~/components/Administration/RegistrationRules.vue';
	import Requests from '~/components/Administration/Requests.vue';
	import Images from '~/components/Administration/Images.vue';
	import ClanArchive from '~/components/Administration/ClanArchive.vue';

	defineProps({
		page: Object
	});

	const sections = ['Правила регистрации', 'Подать заявку на проверку', 'Образ', 'Архив кланов'];
</script>
