<template>
	<section class="ui-panel service-panel post-office-compose">
		<header class="service-panel-heading">
			<GameIcon name="quill" />
			<h2>{{ draft.recipient ? 'Ответить на письмо' : 'Новое письмо' }}</h2>
		</header>
		<div class="service-panel-body">
			<form class="service-form" @submit.prevent="send">
				<label class="service-field">
					<span>Кому</span>
					<input
						v-model="form.recipient"
						class="ui-input"
						type="text"
						maxlength="100"
						required
						placeholder="Имя персонажа"
						:disabled="form.processing"
					/>
				</label>
				<p v-if="form.errors.recipient" class="service-error" role="alert">{{ form.errors.recipient }}</p>
				<label class="service-field">
					<span>Тема</span>
					<input
						v-model="form.subject"
						class="ui-input"
						type="text"
						maxlength="100"
						required
						placeholder="О чём ваше письмо?"
						:disabled="form.processing"
					/>
				</label>
				<p v-if="form.errors.subject" class="service-error" role="alert">{{ form.errors.subject }}</p>
				<label class="service-field">
					<span>Текст письма</span>
					<textarea
						v-model="form.body"
						class="ui-input post-office-textarea"
						rows="10"
						maxlength="5000"
						required
						placeholder="Напишите весточку…"
						:disabled="form.processing"
					></textarea>
				</label>
				<div class="post-office-form-hints">
					<p v-if="form.errors.body" class="service-error" role="alert">{{ form.errors.body }}</p>
					<span class="service-hint">{{ form.body.length }} / 5000</span>
				</div>
				<div class="service-result">
					<span>Стоимость отправки</span>
					<strong>{{ sendCost }} зол.</strong>
				</div>
				<p v-if="!canAfford" class="service-error" role="alert">Для отправки письма нужно {{ sendCost }} зол.</p>
				<p v-if="form.errors.letter" class="service-error" role="alert">{{ form.errors.letter }}</p>
				<div class="post-office-actions">
					<button type="submit" class="ui-button" :disabled="form.processing || !canAfford">
						<GameIcon name="send" />
						{{ form.processing ? 'Отправляем…' : 'Отправить письмо' }}
					</button>
					<Link :href="location.url + '?section=inbox'" class="ui-button ui-button--secondary">К входящим</Link>
				</div>
			</form>
		</div>
	</section>
</template>

<script setup>
	import useLocation from '~/composables/useLocation.js';

	import { computed } from 'vue';
	import { Link, useForm } from '@inertiajs/vue3';
	import GameIcon from '~/components/Layout/GameIcon.vue';
	import useState from '~/composables/useState.js';

	const location = useLocation();

	const props = defineProps({
		draft: Object,
		sendCost: Number,
	});

	const state = useState();
	const canAfford = computed(() => Number(state.user.gold) >= props.sendCost);
	const form = useForm({
		action: 'letter',
		recipient: props.draft.recipient,
		subject: props.draft.subject,
		body: '',
	});

	function send() {
		if (form.processing) {
			return;
		}

		form.post(location.value.actions.letter, {
			preserveScroll: true,
		});
	}
</script>
