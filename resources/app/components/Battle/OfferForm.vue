<template>
	<form class="arena-form" @submit.prevent="submit">
		<div class="arena-form-body">
			<div v-if="Object.keys(form.errors).length" class="ui-notice ui-notice--red" role="alert">
				<p v-for="(error, field) in form.errors" :key="field">{{ error }}</p>
			</div>
			<div class="arena-form-grid">
				<label class="arena-field">
					<span>Таймаут хода</span>
					<select v-model.number="form.timeout" class="ui-input">
						<option :value="1">1,5 минуты</option>
						<option :value="3">3 минуты</option>
						<option :value="5">5 минут</option>
						<option :value="10">10 минут</option>
					</select>
				</label>
				<label class="arena-field">
					<span>Комментарий</span>
					<input v-model="form.comment" type="text" maxlength="255" placeholder="Необязательно" class="ui-input" />
				</label>
				<template v-if="battleType !== 1">
					<label class="arena-field">
						<span>Уровни участников</span>
						<select v-model.number="form.offer_level" class="ui-input">
							<option :value="1">Любого уровня</option>
							<option :value="2">Только моего уровня</option>
							<option :value="3">Моего уровня и ниже</option>
							<option :value="4">Только ниже уровнем</option>
						</select>
					</label>
					<label class="arena-field">
						<span>Начало боя через</span>
						<select v-model.number="form.time_battle_start" class="ui-input">
							<option v-for="minutes in [3, 5, 10, 15]" :key="minutes" :value="minutes * 60">{{ minutes }} мин.</option>
						</select>
					</label>
				</template>
				<label v-if="battleType === 2" class="arena-field">
					<span>Бойцов в каждой команде</span>
					<input v-model.number="form.capacity" type="number" min="2" max="25" required class="ui-input" />
				</label>
			</div>
			<div v-if="battleType !== 2" class="arena-form-options">
				<label class="arena-checkbox">
					<input v-model="form.blood" type="checkbox" />
					Кровавый бой
				</label>
				<label v-if="battleType === 1" class="arena-checkbox">
					<input v-model="form.unarmed" type="checkbox" />
					Рукопашный бой
				</label>
			</div>
			<p v-if="battleType === 1" class="arena-hint">
				Заявка действует 10 минут. В рукопашном бою экипировка снимается с обоих участников. В кровавом бою проигравший получает травму.
			</p>
			<p v-if="battleType === 2" class="arena-hint">Для начала боя нужны участники в обеих командах. Условия по уровню одинаковы для обеих команд.</p>
			<p v-if="battleType === 3" class="arena-hint">Для начала боя нужны минимум 4 участника. Команды распределяются случайным образом.</p>
			<button type="submit" class="ui-button" :disabled="form.processing">{{ form.processing ? 'Подаём заявку…' : 'Подать заявку' }}</button>
		</div>
	</form>
</template>

<script setup>
	import { useForm } from '@inertiajs/vue3';

	const props = defineProps({
		battleType: {
			type: Number,
			required: true
		}
	});

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

	const emit = defineEmits(['close']);

	function submit() {
		if (form.processing) {
			return;
		}

		form.post('/battle', {
			preserveScroll: true,
			onSuccess: () => emit('close')
		});
	}
</script>
