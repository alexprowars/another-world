<template>
	<ContentBlock title="Кузница" class="smithy">
		<template #actions>
			<button v-if="page.busy" type="button" class="ui-icon-button" disabled title="Возвращение доступно после окончания работы">
				<GameIcon name="back" />
			</button>
			<MovementLink v-else :to="location.exit" class="ui-icon-button" title="Назад">
				<GameIcon name="back" />
			</MovementLink>
			<Link :href="location.url + '/' + currentSection.path" class="ui-icon-button" title="Обновить">
				<GameIcon name="refresh" />
			</Link>
		</template>

		<nav class="ui-tabs ui-tabs--stacked">
			<Link
				class="ui-tab"
				v-for="section in availableSections"
				:key="section.id"
				:href="location.url + '/' + section.path"
				:class="{ 'is-active': page.section === section.id }"
			>
				<GameIcon :name="section.icon" />
				{{ section.title }}
			</Link>
		</nav>

		<p v-if="page.message" class="ui-notice ui-notice--blue" role="status" v-html="page.message"></p>

		<div v-if="page.busy" class="smithy-active" role="status">
			<div class="smithy-emblem"><GameIcon name="hourglass" /></div>
			<h2>Работа идёт</h2>
			<p>Вы заняты работой. Дождитесь её завершения, чтобы воспользоваться кузницей.</p>
			<div v-if="page.until" class="smithy-active-time">
				<span>До окончания работы</span>
				<Timer :key="page.until" :value="page.until" :callback="finishWork" class="smithy-countdown" />
			</div>
		</div>
		<div v-else-if="page.notice" class="ui-empty" role="status">
			<GameIcon :name="currentSection.icon" />
			<h3>Нужна другая профессия</h3>
			<p>{{ page.notice }}</p>
			<Link :href="location.url + '/repair'" class="ui-button">К починке вещей</Link>
		</div>
		<component v-else :is="sectionComponents[page.section]" :key="page.section" :page="page" :user="user" />

		<template #footer>
			<GameIcon :name="page.busy ? 'hourglass' : 'book'" />
			<span>
				{{
					page.busy
						? 'Вернуться в город можно после окончания работы.'
						: 'Наведите на изображение предмета или нажмите на него, чтобы посмотреть характеристики.'
				}}
			</span>
		</template>
	</ContentBlock>
</template>

<script setup>
	import MovementLink from '~/components/City/MovementLink.vue';
	import useLocation from '~/composables/useLocation.js';

	import { computed } from 'vue';
	import { Link, router } from '@inertiajs/vue3';
	import ContentBlock from '~/components/ContentBlock.vue';
	import GameIcon from '~/components/Layout/GameIcon.vue';
	import Timer from '~/components/Timer.vue';
	import useState from '~/composables/useState.js';
	import Repair from '~/components/Smithy/Repair.vue';
	import Cut from '~/components/Smithy/Cut.vue';
	import Engraving from '~/components/Smithy/Engraving.vue';
	import Insert from '~/components/Smithy/Insert.vue';

	const location = useLocation();

	const props = defineProps({
		page: Object
	});

	const state = useState();
	const user = computed(() => state.user);

	const sections = [
		{ id: 1, path: 'repair', title: 'Починка вещей', icon: 'tools' },
		{
			id: 2,
			path: 'cut',
			title: 'Огранка камней',
			icon: 'gem',
			profession: 3,
		},
		{
			id: 3,
			path: 'engraving',
			title: 'Гравировка и модернизация',
			icon: 'swords',
		},
		{
			id: 4,
			path: 'insert',
			title: 'Вставка камней',
			icon: 'gem',
			profession: 2,
		},
	];

	const availableSections = computed(() => sections.filter(section => !section.profession || section.profession === user.value.profession));
	const currentSection = computed(() => sections.find(section => section.id === props.page.section));

	const sectionComponents = {
		1: Repair,
		2: Cut,
		3: Engraving,
		4: Insert,
	};

	function finishWork() {
		router.reload();
	}
</script>
