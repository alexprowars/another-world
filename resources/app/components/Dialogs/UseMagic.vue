<template>
	<form class="service-form" @submit.prevent="cast">
		<p>{{ item.title }}</p>
		<p v-if="error" class="service-error" role="alert">{{ error }}</p>
		<label class="service-field">
			<span>Персонаж</span>
			<input v-model="target" type="text" class="ui-input" maxlength="100" required :disabled="processing" />
		</label>
		<div class="flex flex-wrap gap-2">
			<button type="button" class="ui-button ui-button--compact ui-button--secondary" :disabled="processing" @click="target = selfName">На себя</button>
			<button v-if="opponentName" type="button" class="ui-button ui-button--compact ui-button--secondary" :disabled="processing" @click="target = opponentName">
				На противника
			</button>
		</div>
		<div class="flex justify-end gap-2">
			<button type="button" class="ui-button ui-button--secondary" :disabled="processing" @click="emit('close')">Отмена</button>
			<button type="submit" class="ui-button" :disabled="processing">{{ processing ? 'Применяем…' : 'Использовать' }}</button>
		</div>
	</form>
</template>

<script setup>
	import { ref } from 'vue';
	import { router, useHttp } from '@inertiajs/vue3';
	import { toast } from 'vue3-toastify';

	const props = defineProps({
		item: {
			type: Object,
			required: true
		},
		selfName: {
			type: String,
			required: true
		},
		opponentName: {
			type: String,
			default: ''
		},
		battle: {
			type: Number,
			default: null
		},
		round: {
			type: Number,
			default: null
		},
	});

	const emit = defineEmits(['close', 'used']);
	const target = ref(props.selfName);
	const error = ref('');
	const processing = ref(false);

	async function cast() {
		if (processing.value) {
			return;
		}

		processing.value = true;
		error.value = '';

		try {
			const result = await useHttp({
				item: props.item.id,
				target: target.value.trim(),
				battle: props.battle,
				round: props.round,
			}).post('/magic');

			toast.success(result.message);

			if (result.redirect) {
				emit('close');
				router.visit(result.redirect);
				return;
			}

			emit('used');
			emit('close');
		} catch (e) {
			error.value = e.response?.data?.message || 'Не удалось применить заклинание. Обновите данные и попробуйте снова.';
			processing.value = false;
		}
	}
</script>
