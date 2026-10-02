<template>
	<article class="ui-panel service-panel post-office-letter">
		<header class="service-panel-heading">
			<GameIcon name="mail" />
			<h2>{{ letter.subject }}</h2>
		</header>
		<div class="service-panel-body">
			<dl class="post-office-letter-details">
				<div>
					<dt>От кого</dt>
					<dd><Link :href="'/info/' + letter.sender.id">{{ letter.sender.name }}</Link></dd>
				</div>
				<div>
					<dt>Кому</dt>
					<dd><Link :href="'/info/' + letter.recipient.id">{{ letter.recipient.name }}</Link></dd>
				</div>
				<div>
					<dt>Отправлено</dt>
					<dd>{{ $formatDate(letter.created_at, 'DD.MM.YYYY HH:mm') }}</dd>
				</div>
				<div v-if="section === 'sent'">
					<dt>Статус</dt>
					<dd>{{ letter.read_at ? 'Прочитано ' + $formatDate(letter.read_at, 'DD.MM.YYYY HH:mm') : 'Ещё не прочитано' }}</dd>
				</div>
			</dl>
			<div class="post-office-letter-body">{{ letter.body }}</div>
			<div class="post-office-actions">
				<Link v-if="section === 'inbox'" :href="'/map?section=compose&reply=' + letter.id" class="ui-button">
					<GameIcon name="quill" />
					Ответить
				</Link>
				<Link :href="listUrl" class="ui-button ui-button--secondary">
					<GameIcon name="back" />
					К списку писем
				</Link>
			</div>
		</div>
	</article>
</template>

<script setup>
	import { Link } from '@inertiajs/vue3';
	import GameIcon from '~/components/Layout/GameIcon.vue';

	defineProps({
		letter: Object,
		section: String,
		listUrl: String,
	});
</script>
