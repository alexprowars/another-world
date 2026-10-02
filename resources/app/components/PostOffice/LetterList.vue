<template>
	<section class="service-section">
		<header class="service-heading post-office-list-heading">
			<h2>
				{{ section === 'sent' ? 'Отправленные письма' : 'Ваши письма' }}
				<span class="ui-badge">{{ pagination.total }}</span>
			</h2>
			<p>{{ section === 'sent' ? 'Здесь хранятся ваши письма и отметки о прочтении.' : 'Непрочитанные письма отмечены печатью «Новое».' }}</p>
		</header>

		<div v-if="letters.length" class="post-office-list ui-panel">
			<Link
				v-for="letter in letters"
				:key="letter.id"
				:href="'/map?section=' + section + '&letter=' + letter.id + '&page=' + pagination.current_page"
				class="post-office-entry"
				:class="{ 'is-unread': section === 'inbox' && !letter.read_at }"
			>
				<GameIcon name="mail" />
				<div class="post-office-entry-copy">
					<strong>{{ letter.subject }}</strong>
					<span>{{ section === 'sent' ? 'Кому: ' + letter.recipient.name : 'От: ' + letter.sender.name }}</span>
				</div>
				<div class="post-office-entry-meta">
					<time :datetime="letter.created_at">{{ $formatDate(letter.created_at, 'DD.MM.YYYY HH:mm') }}</time>
					<span v-if="section === 'inbox' && !letter.read_at" class="ui-badge post-office-unread">Новое</span>
					<span v-else-if="section === 'sent'" class="post-office-read-status">{{ letter.read_at ? 'Прочитано' : 'Не прочитано' }}</span>
				</div>
			</Link>
		</div>
		<div v-else class="ui-empty" role="status">
			<GameIcon name="mail" />
			<h3>{{ section === 'sent' ? 'Вы ещё не отправляли писем' : 'Почтовый ящик пуст' }}</h3>
			<p>{{ section === 'sent' ? 'Напишите другу — почта доставит вашу весточку.' : 'Здесь появятся письма от других персонажей.' }}</p>
			<Link href="/map?section=compose" class="ui-button">
				<GameIcon name="quill" />
				Написать письмо
			</Link>
		</div>

		<nav v-if="pagination.last_page > 1" class="post-office-pagination">
			<Link v-if="pagination.previous_url" :href="pagination.previous_url" class="ui-button ui-button--secondary ui-button--compact">
				<GameIcon name="back" />
				Назад
			</Link>
			<span>Страница {{ pagination.current_page }} из {{ pagination.last_page }}</span>
			<Link v-if="pagination.next_url" :href="pagination.next_url" class="ui-button ui-button--secondary ui-button--compact">
				Далее
				<GameIcon name="forward" />
			</Link>
		</nav>
	</section>
</template>

<script setup>
	import { Link } from '@inertiajs/vue3';
	import GameIcon from '~/components/Layout/GameIcon.vue';

	defineProps({
		letters: Array,
		section: String,
		pagination: Object,
	});
</script>
