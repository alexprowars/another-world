<template>
	<Head title="Настройки"/>
	<div class="textblock space-y-4">
		<h2 class="font-bold">Настройки</h2>

		<form @submit.prevent="save(optionsForm)" class="rounded border border-slate-300 p-4 space-y-3">
			<h3 class="font-bold">Статус</h3>
			<div v-if="Object.keys(optionsForm.errors).length" class="text-red-600" role="alert">
				<p v-for="(error, field) in optionsForm.errors" :key="field">{{ error }}</p>
			</div>
			<label class="block space-y-1">
				<span class="block">Ваш статус</span>
				<select v-model="optionsForm.presence_status" class="w-full rounded border border-slate-300 px-2 py-1">
					<option v-for="status in [0, 1, 2, 3, 4]" :key="status" :value="status">{{ $t('status.' + status) }}</option>
				</select>
			</label>
			<button type="submit" class="btn btn-primary" :disabled="busy">Применить</button>
		</form>

		<form @submit.prevent="save(profileForm)" class="rounded border border-slate-300 p-4 space-y-3">
			<h3 class="font-bold">Анкета</h3>
			<div v-if="Object.keys(profileForm.errors).length" class="text-red-600" role="alert">
				<p v-for="(error, field) in profileForm.errors" :key="field">{{ error }}</p>
			</div>
			<label class="block space-y-1">
				<span class="block">Город</span>
				<input v-model="profileForm.city" type="text" maxlength="255" autocomplete="address-level2" class="w-full rounded border border-slate-300 px-2 py-1">
			</label>
			<label class="block space-y-1">
				<span class="block">Немного о себе</span>
				<textarea v-model="profileForm.about" rows="6" maxlength="10000" class="w-full rounded border border-slate-300 px-2 py-1"></textarea>
			</label>
			<button type="submit" class="btn btn-primary" :disabled="busy">Сохранить анкету</button>
		</form>

		<form @submit.prevent="save(passwordForm, true)" class="rounded border border-slate-300 p-4 space-y-3">
			<h3 class="font-bold">Сменить пароль</h3>
			<div v-if="Object.keys(passwordForm.errors).length" class="text-red-600" role="alert">
				<p v-for="(error, field) in passwordForm.errors" :key="field">{{ error }}</p>
			</div>
			<label class="block space-y-1">
				<span class="block">Текущий пароль</span>
				<input v-model="passwordForm.current_password" type="password" autocomplete="current-password" required class="w-full rounded border border-slate-300 px-2 py-1">
			</label>
			<label class="block space-y-1">
				<span class="block">Новый пароль</span>
				<input v-model="passwordForm.password" type="password" autocomplete="new-password" minlength="6" maxlength="72" required class="w-full rounded border border-slate-300 px-2 py-1">
			</label>
			<label class="block space-y-1">
				<span class="block">Подтверждение пароля</span>
				<input v-model="passwordForm.password_confirmation" type="password" autocomplete="new-password" minlength="6" maxlength="72" required class="w-full rounded border border-slate-300 px-2 py-1">
			</label>
			<button type="submit" class="btn btn-primary" :disabled="busy">Изменить пароль</button>
		</form>

		<form @submit.prevent="save(emailForm, true)" class="rounded border border-slate-300 p-4 space-y-3">
			<h3 class="font-bold">Сменить e-mail</h3>
			<div v-if="Object.keys(emailForm.errors).length" class="text-red-600" role="alert">
				<p v-for="(error, field) in emailForm.errors" :key="field">{{ error }}</p>
			</div>
			<label class="block space-y-1">
				<span class="block">Текущий e-mail</span>
				<input v-model.trim="emailForm.current_email" type="text" autocomplete="email" required class="w-full rounded border border-slate-300 px-2 py-1">
			</label>
			<label class="block space-y-1">
				<span class="block">Новый e-mail</span>
				<input v-model.trim="emailForm.email" type="email" autocomplete="email" maxlength="50" required class="w-full rounded border border-slate-300 px-2 py-1">
			</label>
			<button type="submit" class="btn btn-primary" :disabled="busy">Изменить e-mail</button>
		</form>
	</div>
</template>

<script setup>
	import { computed } from 'vue';
	import { Head, useForm } from '@inertiajs/vue3';
	import GameLayout from '~/layouts/Game.vue';
	import PersonLayout from '~/layouts/Person.vue';

	defineOptions({ layout: [GameLayout, PersonLayout] });
	const props = defineProps({ page: Object });

	const optionsForm = useForm({ action: 'options', presence_status: props.page.options.presence_status });
	const profileForm = useForm({ action: 'profile', city: props.page.city ?? '', about: props.page.about ?? '' });
	const passwordForm = useForm({ action: 'password', current_password: '', password: '', password_confirmation: '' });
	const emailForm = useForm({ action: 'email', current_email: '', email: '' });
	const busy = computed(() => optionsForm.processing || profileForm.processing || passwordForm.processing || emailForm.processing);

	function save(form, reset = false) {
		if (busy.value) return;

		form.post('/person/settings', {
			preserveScroll: true,
			onSuccess: () => {
				if (reset) form.reset();
			},
		});
	}
</script>
