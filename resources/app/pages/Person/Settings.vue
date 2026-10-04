<template>
	<Head title="Настройки" />
	<section class="person-settings">
		<header class="person-page-heading">
			<div>
				<h1>Настройки персонажа</h1>
				<p>Статус, личная анкета и данные для входа в игру.</p>
			</div>
		</header>

		<form class="person-form-panel person-status-form" @submit.prevent="save(optionsForm)">
			<header class="person-form-heading">
				<h2>Статус в игре</h2>
				<p>Как вас видят другие игроки.</p>
			</header>
			<div v-if="Object.keys(optionsForm.errors).length" class="ui-notice ui-notice--red ui-notice--compact" role="alert">
				<p v-for="(error, field) in optionsForm.errors" :key="field">{{ error }}</p>
			</div>
			<div class="person-status-controls">
				<label class="person-form-field">
					<span>Ваш статус</span>
					<select class="ui-input ui-input--compact" v-model="optionsForm.presence_status">
						<option v-for="status in [0, 1, 2, 3, 4]" :key="status" :value="status">{{ $t('status.' + status) }}</option>
					</select>
				</label>
				<button type="submit" class="ui-button ui-button--compact" :disabled="busy">
					{{ optionsForm.processing ? 'Сохранение…' : 'Применить' }}
				</button>
			</div>
		</form>

		<div class="person-settings-forms">
			<form class="person-form-panel" @submit.prevent="save(profileForm)">
				<header class="person-form-heading">
					<h2>Личная анкета</h2>
					<p>Расскажите о себе другим игрокам.</p>
				</header>
				<div v-if="Object.keys(profileForm.errors).length" class="ui-notice ui-notice--red ui-notice--compact" role="alert">
					<p v-for="(error, field) in profileForm.errors" :key="field">{{ error }}</p>
				</div>
				<div v-if="!page.gender" class="ui-notice ui-notice--compact" role="status">
					<p>Выберите пол персонажа и сохраните анкету. Это необходимо для заключения брака.</p>
				</div>
				<label class="person-form-field">
					<span>Пол персонажа</span>
					<select
						class="ui-input ui-input--compact"
						v-model="profileForm.gender"
						required
						:class="{ 'is-invalid': profileForm.errors.gender }"
					>
						<option disabled value="">Выберите пол</option>
						<option value="M">Мужской</option>
						<option value="F">Женский</option>
					</select>
				</label>
				<label class="person-form-field">
					<span>Город</span>
					<input
						class="ui-input ui-input--compact"
						v-model="profileForm.city"
						type="text"
						maxlength="255"
						autocomplete="address-level2"
						:class="{ 'is-invalid': profileForm.errors.city }"
					/>
				</label>
				<label class="person-form-field">
					<span>Немного о себе</span>
					<textarea
						class="ui-input ui-input--compact"
						v-model="profileForm.about"
						rows="7"
						maxlength="10000"
						:class="{ 'is-invalid': profileForm.errors.about }"
					></textarea>
				</label>
				<footer class="person-form-actions">
					<button type="submit" class="ui-button ui-button--compact" :disabled="busy">
						{{ profileForm.processing ? 'Сохранение…' : 'Сохранить анкету' }}
					</button>
				</footer>
			</form>

			<form class="person-form-panel" @submit.prevent="save(passwordForm, true)">
				<header class="person-form-heading">
					<h2>Смена пароля</h2>
					<p>Новый пароль — от 6 до 72 символов.</p>
				</header>
				<div v-if="Object.keys(passwordForm.errors).length" class="ui-notice ui-notice--red ui-notice--compact" role="alert">
					<p v-for="(error, field) in passwordForm.errors" :key="field">{{ error }}</p>
				</div>
				<label class="person-form-field">
					<span>Текущий пароль</span>
					<input
						class="ui-input ui-input--compact"
						v-model="passwordForm.current_password"
						type="password"
						autocomplete="current-password"
						required
						:class="{ 'is-invalid': passwordForm.errors.current_password }"
					/>
				</label>
				<label class="person-form-field">
					<span>Новый пароль</span>
					<input
						class="ui-input ui-input--compact"
						v-model="passwordForm.password"
						type="password"
						autocomplete="new-password"
						minlength="6"
						maxlength="72"
						required
						:class="{ 'is-invalid': passwordForm.errors.password }"
					/>
				</label>
				<label class="person-form-field">
					<span>Подтверждение пароля</span>
					<input
						class="ui-input ui-input--compact"
						v-model="passwordForm.password_confirmation"
						type="password"
						autocomplete="new-password"
						minlength="6"
						maxlength="72"
						required
						:class="{ 'is-invalid': passwordForm.errors.password_confirmation }"
					/>
				</label>
				<footer class="person-form-actions">
					<button type="submit" class="ui-button ui-button--compact" :disabled="busy">
						{{ passwordForm.processing ? 'Сохранение…' : 'Изменить пароль' }}
					</button>
				</footer>
			</form>

			<form class="person-form-panel" @submit.prevent="save(emailForm, true)">
				<header class="person-form-heading">
					<h2>Смена e-mail</h2>
					<p>Укажите текущий и новый адрес почты.</p>
				</header>
				<div v-if="Object.keys(emailForm.errors).length" class="ui-notice ui-notice--red ui-notice--compact" role="alert">
					<p v-for="(error, field) in emailForm.errors" :key="field">{{ error }}</p>
				</div>
				<label class="person-form-field">
					<span>Текущий e-mail</span>
					<input
						class="ui-input ui-input--compact"
						v-model.trim="emailForm.current_email"
						type="text"
						autocomplete="email"
						required
						:class="{ 'is-invalid': emailForm.errors.current_email }"
					/>
				</label>
				<label class="person-form-field">
					<span>Новый e-mail</span>
					<input
						class="ui-input ui-input--compact"
						v-model.trim="emailForm.email"
						type="email"
						autocomplete="email"
						maxlength="50"
						required
						:class="{ 'is-invalid': emailForm.errors.email }"
					/>
				</label>
				<footer class="person-form-actions">
					<button type="submit" class="ui-button ui-button--compact" :disabled="busy">
						{{ emailForm.processing ? 'Сохранение…' : 'Изменить e-mail' }}
					</button>
				</footer>
			</form>
		</div>
	</section>
</template>

<script setup>
	import { computed } from 'vue';
	import { Head, useForm } from '@inertiajs/vue3';
	import GameLayout from '~/layouts/Game.vue';
	import PersonLayout from '~/layouts/Person.vue';

	defineOptions({
		layout: [GameLayout, PersonLayout]
	});

	const props = defineProps({
		page: Object
	});

	const optionsForm = useForm({
		action: 'options',
		presence_status: props.page.options.presence_status
	});

	const profileForm = useForm({
		action: 'profile',
		gender: props.page.gender ?? '',
		city: props.page.city ?? '',
		about: props.page.about ?? ''
	});

	const passwordForm = useForm({
		action: 'password',
		current_password: '',
		password: '',
		password_confirmation: ''
	});

	const emailForm = useForm({
		action: 'email',
		current_email: '',
		email: ''
	});

	const busy = computed(() => optionsForm.processing || profileForm.processing || passwordForm.processing || emailForm.processing);

	function save(form, reset = false) {
		if (busy.value) {
			return;
		}

		form.post('/person/settings', {
			preserveScroll: true,
			onSuccess: () => {
				if (reset) form.reset();
			},
		});
	}
</script>
