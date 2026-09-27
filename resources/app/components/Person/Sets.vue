<template>
	<section class="space-y-4">
		<h2 class="hitem text-center">Комплекты</h2>
		<p>Сохраните надетые вещи, чтобы затем надеть весь комплект одним действием.</p>
		<form class="flex flex-wrap items-end gap-3" @submit.prevent="save">
			<label class="block">
				Название комплекта
				<input v-model.trim="saveForm.name" type="text" maxlength="255" required class="block w-full" placeholder="Имя комплекта">
			</label>
			<button type="submit" class="button" :disabled="busy">Сохранить</button>
		</form>
		<div v-if="Object.keys(saveForm.errors).length" class="text-red-700" role="alert">
			<p v-for="(error, field) in saveForm.errors" :key="field">{{ error }}</p>
		</div>
		<div v-if="Object.keys(actionForm.errors).length" class="text-red-700" role="alert">
			<p v-for="(error, field) in actionForm.errors" :key="field">{{ error }}</p>
		</div>
		<ul v-if="sets.length" class="space-y-2">
			<li v-for="set in sets" :key="set.id" class="flex flex-wrap items-center gap-3 border-b border-slate-300 py-2">
				<b class="min-w-0 flex-1 break-words">{{ set.name }}</b>
				<button type="button" class="button" :disabled="busy" @click="act('wear', set.id)">Надеть</button>
				<button type="button" class="button" :disabled="busy" @click="act('delete', set.id)">Удалить</button>
			</li>
		</ul>
		<p v-else>У вас пока нет сохранённых комплектов.</p>
	</section>
</template>

<script setup>
	import { computed } from 'vue';
	import { useForm } from '@inertiajs/vue3';

	defineProps({ sets: Array });

	const saveForm = useForm({ action: 'save', name: '' });
	const actionForm = useForm({ action: '', id: null });
	const busy = computed(() => saveForm.processing || actionForm.processing);

	function save() {
		if (busy.value) return;
		actionForm.clearErrors();
		saveForm.post('/person/inventory/sets', {
			preserveScroll: true,
			onSuccess: () => saveForm.reset('name'),
		});
	}

	function act(action, id) {
		if (busy.value) return;
		saveForm.clearErrors();
		actionForm.action = action;
		actionForm.id = id;
		actionForm.post('/person/inventory/sets', { preserveScroll: true });
	}
</script>