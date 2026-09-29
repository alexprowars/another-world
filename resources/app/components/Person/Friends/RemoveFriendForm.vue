<template>
	<form class="person-form-panel" @submit.prevent="remove">
		<header class="person-form-heading">
			<h2>Удалить из списка</h2>
			<p>Укажите ник друга или игнорируемого персонажа.</p>
		</header>
		<div v-if="Object.keys(removeForm.errors).length" class="ui-notice ui-notice--red ui-notice--compact" role="alert">
			<p v-for="(error, field) in removeForm.errors" :key="field">{{ error }}</p>
		</div>
		<label class="person-form-field">
			<span>Ник персонажа</span>
			<input
				class="ui-input ui-input--compact"
				v-model.trim="removeForm.name"
				type="text"
				maxlength="100"
				required
				placeholder="Введите ник"
				:class="{ 'is-invalid': removeForm.errors.name }"
			/>
		</label>
		<footer class="person-form-actions">
			<button type="submit" class="ui-button ui-button--compact ui-button--secondary" :disabled="busy">
				{{ removeForm.processing ? 'Удаление…' : 'Удалить' }}
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

	const removeForm = useForm({
		action: 'remove',
		name: ''
	});

	function remove() {
		if (props.busy || removeForm.processing) {
			return;
		}

		removeForm.post('/person/friends', {
			preserveScroll: true,
			onStart: () => emit('processing', true),
			onFinish: () => emit('processing', false),
		});
	}
</script>
