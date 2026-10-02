<template>
	<form class="service-form gift-form" @submit.prevent="send">
		<div class="gift-form-item">
			<div class="gift-form-image">
				<img :src="getItemImagePath(item)" :alt="item.title" />
			</div>
			<div>
				<p class="service-hint">Ваш подарок</p>
				<h2>{{ item.title }}</h2>
			</div>
		</div>
		<div v-if="Object.keys(form.errors).length" class="ui-notice ui-notice--red" role="alert">
			<p v-for="(error, field) in form.errors" :key="field">{{ error }}</p>
		</div>
		<label class="service-field">
			<span>Получатель</span>
			<input
				v-model="form.user"
				type="text"
				name="user"
				placeholder="Введите логин игрока"
				class="ui-input"
				:class="{ 'is-invalid': v$.user.$error || form.errors.user }"
				:disabled="form.processing"
			/>
			<span v-if="v$.user.$error" class="service-error" role="alert">Введите логин получателя.</span>
		</label>
		<fieldset class="gift-form-sender" :disabled="form.processing">
			<legend>Отправитель</legend>
			<div class="gift-form-options">
				<label class="gift-form-option" :class="{ 'is-selected': form.from === 1 }">
					<input v-model="form.from" type="radio" name="from" :value="1" />
					<span>От имени игрока</span>
				</label>
				<label v-if="user.tribe" class="gift-form-option" :class="{ 'is-selected': form.from === 2 }">
					<input v-model="form.from" type="radio" name="from" :value="2" />
					<span>От имени клана</span>
				</label>
				<label class="gift-form-option" :class="{ 'is-selected': form.from === 3 }">
					<input v-model="form.from" type="radio" name="from" :value="3" />
					<span>Анонимно</span>
				</label>
			</div>
		</fieldset>
		<label class="service-field">
			<span>Пожелание</span>
			<textarea
				v-model="form.text"
				name="text"
				rows="4"
				placeholder="Напишите пожелание получателю"
				class="ui-input"
				:disabled="form.processing"
			></textarea>
			<span class="service-hint">Необязательно</span>
		</label>
		<div class="gift-form-actions">
			<button type="button" class="ui-button ui-button--secondary" :disabled="form.processing" @click="emit('close')">Отмена</button>
			<button type="submit" class="ui-button" :disabled="form.processing">
				{{ form.processing ? 'Отправляем…' : 'Подарить' }}
			</button>
		</div>
	</form>
</template>

<script setup>
	import useState from '~/composables/useState.js';
	import { computed } from 'vue';
	import { useForm } from '@inertiajs/vue3';
	import { useVuelidate } from '@vuelidate/core';
	import { required } from '@vuelidate/validators';
	import { closeModals } from '~/composables/useModals.js';
	import { getItemImagePath } from '~/utils/itemImage.js';

	const props = defineProps({
		item: Object,
	});

	const emit = defineEmits(['close']);

	const state = useState();
	const user = computed(() => state.user);

	const form = useForm({
		gift: props.item.id,
		user: '',
		from: 1,
		text: '',
	});

	const validations = {
		user: {
			required,
		},
	};

	const v$ = useVuelidate(validations, form, { $autoDirty: true });

	async function send() {
		if (form.processing || !(await v$.value.$validate())) {
			return;
		}

		form.post('', {
			onSuccess() {
				closeModals();
			},
		});
	}
</script>
