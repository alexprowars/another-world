<template>
	<form @submit.prevent="submit">
		<div class="rounded border border-slate-300 p-4 space-y-4">
			<h3 class="font-bold">Новая заявка</h3>
			<div v-if="Object.keys(form.errors).length" class="text-red-600" role="alert">
				<p v-for="(error, field) in form.errors" :key="field">{{ error }}</p>
			</div>
			<div class="grid gap-4 sm:grid-cols-2">
				<label class="space-y-1">
					<span class="block">Таймаут хода</span>
					<select v-model.number="form.timeout" class="w-full rounded border border-slate-300 px-2 py-1">
						<option :value="1">1,5 минуты</option>
						<option :value="3">3 минуты</option>
						<option :value="5">5 минут</option>
						<option :value="10">10 минут</option>
					</select>
				</label>
				<label class="space-y-1">
					<span class="block">Комментарий</span>
					<input v-model="form.comment" type="text" maxlength="255" class="w-full rounded border border-slate-300 px-2 py-1">
				</label>
				<template v-if="battleType !== 1">
					<label class="space-y-1">
						<span class="block">Уровни участников</span>
						<select v-model.number="form.offer_level" class="w-full rounded border border-slate-300 px-2 py-1">
							<option :value="1">Любого уровня</option>
							<option :value="2">Только моего уровня</option>
							<option :value="3">Моего уровня и ниже</option>
							<option :value="4">Только ниже уровнем</option>
						</select>
					</label>
					<label class="space-y-1">
						<span class="block">Начало боя через</span>
						<select v-model.number="form.time_battle_start" class="w-full rounded border border-slate-300 px-2 py-1">
							<option v-for="minutes in [3, 5, 10, 15]" :key="minutes" :value="minutes * 60">{{ minutes }} мин.</option>
						</select>
					</label>
				</template>
				<label v-if="battleType === 2" class="space-y-1">
					<span class="block">Бойцов в каждой команде</span>
					<input v-model.number="form.capacity" type="number" min="2" max="25" required class="w-full rounded border border-slate-300 px-2 py-1">
				</label>
			</div>
			<div class="flex flex-wrap gap-4">
				<label v-if="battleType !== 2" class="flex items-center gap-2"><input v-model="form.blood" type="checkbox">Кровавый бой</label>
				<label v-if="battleType === 1" class="flex items-center gap-2"><input v-model="form.unarmed" type="checkbox">Рукопашный бой</label>
			</div>
			<p v-if="battleType === 1" class="text-sm text-slate-600">Заявка действует 10 минут. В рукопашном бою экипировка снимается с обоих участников. В кровавом бою проигравший получает травму.</p>
			<p v-if="battleType === 2" class="text-sm text-slate-600">Для начала боя нужны участники в обеих командах. Условия по уровню одинаковы для обеих команд.</p>
			<p v-if="battleType === 3" class="text-sm text-slate-600">Для начала боя нужны минимум 4 участника. Команды распределяются случайным образом.</p>
			<button type="submit" class="btn btn-primary" :disabled="form.processing">Подать заявку</button>
		</div>
	</form>
</template>

<script setup>
	import { useForm } from '@inertiajs/vue3';
	import { computed } from 'vue';

	const props = defineProps({ battleType: { type: Number, required: true } });
	const form = useForm({
		action: 'create',
		battle_type: props.battleType,
		timeout: 3,
		comment: '',
		offer_level: 1,
		time_battle_start: 180,
		capacity: 2,
		blood: false,
		unarmed: false,
	});

	defineExpose({ processing: computed(() => form.processing) });

	function submit() {
		form.post('/battle', { preserveScroll: true });
	}
</script>
