<template>
	<ContentBlock title="Почта">
		<template #actions>
			<Link href="/map/change/25" class="ui-icon-button" title="Вернуться в парк">
				<GameIcon name="back" />
			</Link>
			<Link :href="refreshUrl" class="ui-icon-button" title="Обновить">
				<GameIcon name="refresh" />
			</Link>
		</template>

		<header class="post-office-heading">
			<div class="ui-emblem"><GameIcon name="mail" /></div>
			<div class="service-heading">
				<h2>Городское почтовое отделение</h2>
				<p>Вести от друзей, приглашения и важные слова — всё дойдёт до адресата.</p>
			</div>
		</header>

		<nav class="ui-tabs ui-tabs--stacked">
			<Link href="/map?section=inbox" class="ui-tab" :class="{ 'is-active': page.section === 'inbox' }">
				<GameIcon name="mail" />
				Входящие
				<span v-if="page.unread_count" class="ui-badge post-office-unread">{{ page.unread_count }}</span>
			</Link>
			<Link href="/map?section=sent" class="ui-tab" :class="{ 'is-active': page.section === 'sent' }">
				<GameIcon name="send" />
				Исходящие
			</Link>
			<Link href="/map?section=compose" class="ui-tab" :class="{ 'is-active': page.section === 'compose' }">
				<GameIcon name="quill" />
				Написать письмо
			</Link>
		</nav>

		<Compose
			v-if="page.section === 'compose'"
			:key="page.draft.recipient + ':' + page.draft.subject"
			:draft="page.draft"
			:send-cost="page.send_cost"
		/>
		<Letter v-else-if="page.letter" :letter="page.letter" :section="page.section" :list-url="listUrl" />
		<LetterList v-else :letters="page.letters" :section="page.section" :pagination="page.pagination" />

		<template #footer>
			<GameIcon name="coins" />
			<span>Отправка одного письма — {{ page.send_cost }} зол. Чтение писем бесплатно.</span>
		</template>
	</ContentBlock>
</template>

<script setup>
	import { computed } from 'vue';
	import { Link, usePage } from '@inertiajs/vue3';
	import ContentBlock from '~/components/ContentBlock.vue';
	import GameIcon from '~/components/Layout/GameIcon.vue';
	import Compose from '~/components/PostOffice/Compose.vue';
	import Letter from '~/components/PostOffice/Letter.vue';
	import LetterList from '~/components/PostOffice/LetterList.vue';

	const props = defineProps({
		page: Object,
	});

	const inertiaPage = usePage();
	const refreshUrl = computed(() => inertiaPage.url);
	const listUrl = computed(() =>
		'/map?section=' + props.page.section + '&page=' + props.page.pagination.current_page
	);
</script>
