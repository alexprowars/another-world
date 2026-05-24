<template>
	<div class="w-full">
		<div class="mb-4">Подарить "{{ item.title }}"</div>
		<form method="post" action="/map/?otdel=4" class="space-y-3" @submit.prevent="send">
			<div>
				<input v-model="form.user" type="text" name="user" placeholder="Введите логин игрока"
					class="w-full border border-gray-300 bg-white px-2 py-1 text-sm outline-none transition focus:border-gray-500 focus:ring-1 focus:ring-gray-400"
					:class="{ 'border-red-500 focus:border-red-500 focus:ring-red-200': v$.user.$error }"
				>
			</div>
			<div class="space-y-1 text-sm">
				<label class="flex cursor-pointer items-center gap-2">
					<input v-model="form.from" type="radio" name="from" :value="1" class="size-3.5">
					<span>От имени игрока</span>
				</label>
				<label v-if="user.tribe" class="flex cursor-pointer items-center gap-2">
					<input v-model="form.from" type="radio" name="from" :value="2" class="size-3.5">
					<span>От имени клана</span>
				</label>
				<label class="flex cursor-pointer items-center gap-2">
					<input v-model="form.from" type="radio" name="from" :value="3" class="size-3.5">
					<span>Анонимно</span>
				</label>
			</div>
			<div>
				<textarea v-model="form.text" name="text" placeholder="Текст пожелания"
					class="min-h-20 w-full resize-y border border-gray-300 bg-white px-2 py-1 text-sm outline-none transition focus:border-gray-500 focus:ring-1 focus:ring-gray-400"
				></textarea>
			</div>
			<div class="flex justify-end">
				<button type="submit" class="bg-green-600 px-3 py-1.5 text-sm font-bold text-white transition hover:bg-green-700 disabled:cursor-not-allowed disabled:opacity-60" :disabled="form.processing">
					Подарить
				</button>
			</div>
		</form>
	</div>
</template>

<script setup>
	import useState from '~/composables/useState.js';
	import { computed } from 'vue';
	import { useForm } from '@inertiajs/vue3';
	import { useVuelidate } from '@vuelidate/core';
	import { required } from '@vuelidate/validators';
	import { closeModals } from '~/composables/useModals.js';

	const props = defineProps({
		item: Object,
	});

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
			required
		},
	}

	const v$ = useVuelidate(
		validations,
		form,
		{ $autoDirty: true }
	);

	async function send() {
		if (!await v$.value.$validate()) {
			return
		}

		form.post('', {
			onSuccess() {
				closeModals();
			}
		});
	}
</script>
