<template>
	<form class="person-form-panel" @submit.prevent="add">
		<header class="person-form-heading">
			<h2>Добавить персонажа</h2>
			<p>Выберите, кем он будет в вашем списке.</p>
		</header>
		<div v-if="Object.keys(addForm.errors).length" class="ui-notice ui-notice--red ui-notice--compact" role="alert">
			<p v-for="(error, field) in addForm.errors" :key="field">{{ error }}</p>
		</div>
		<label class="person-form-field">
			<span>Ник персонажа</span>
			<input
				class="ui-input ui-input--compact"
				v-model.trim="addForm.name"
				type="text"
				maxlength="100"
				required
				placeholder="Введите ник"
				:class="{ 'is-invalid': addForm.errors.name }"
			/>
		</label>
		<label class="person-form-field">
			<span>Отношение</span>
			<select class="ui-input ui-input--compact" v-model="addForm.is_ignored">
				<option :value="false">Друг</option>
				<option :value="true">Враг (игнор)</option>
			</select>
		</label>
		<footer class="person-form-actions">
			<button type="submit" class="ui-button ui-button--compact" :disabled="busy">
				{{ addForm.processing ? 'Добавление…' : 'Добавить' }}
			</button>
		</footer>
	</form>
</template>

<script setup>
	import { useForm } from '@inertiajs/vue3';

	const props = defineProps({
		busy: Boolean,
	});

	const emit = defineEmits(['processing']);

	const addForm = useForm({
		action: 'add',
		name: '',
		is_ignored: false
	});

	function add() {
		if (props.busy || addForm.processing) {
			return;
		}

		addForm.post('/person/friends', {
			preserveScroll: true,
			onStart: () => emit('processing', true),
			onFinish: () => emit('processing', false),
		});
	}
</script>
