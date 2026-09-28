<template>
	<section class="equipment-sets">
		<form class="equipment-set-form" @submit.prevent="save">
			<label for="equipment-set-name">Сохранить текущую экипировку</label>
			<div class="equipment-set-form-fields">
				<input
					class="ui-input ui-input--compact"
					id="equipment-set-name"
					v-model.trim="saveForm.name"
					type="text"
					maxlength="255"
					required
					placeholder="Название комплекта"
					:disabled="busy"
				/>
				<button type="submit" class="ui-button ui-button--compact" :disabled="busy">Сохранить</button>
			</div>
			<p>В комплект войдут вещи, которые сейчас надеты на персонажа.</p>
		</form>
		<div v-if="Object.keys(saveForm.errors).length" class="ui-notice ui-notice--red ui-notice--compact" role="alert">
			<p v-for="(error, field) in saveForm.errors" :key="field">{{ error }}</p>
		</div>
		<div v-if="Object.keys(actionForm.errors).length" class="ui-notice ui-notice--red ui-notice--compact" role="alert">
			<p v-for="(error, field) in actionForm.errors" :key="field">{{ error }}</p>
		</div>
		<div class="inventory-section-heading">
			<h2>Сохранённые комплекты</h2>
			<span class="person-section-count">{{ sets.length }}</span>
		</div>
		<ul v-if="sets.length" class="equipment-set-list">
			<li v-for="set in sets" :key="set.id" class="equipment-set">
				<img src="/assets/images/stats/armor.png" class="person-stat-icon" alt="" />
				<b>{{ set.name }}</b>
				<div class="equipment-set-actions">
					<button type="button" class="ui-button ui-button--compact" :disabled="busy" @click="act('wear', set.id)">Надеть</button>
					<button type="button" class="ui-text-button" :disabled="busy" @click="act('delete', set.id)">Удалить</button>
				</div>
			</li>
		</ul>
		<div v-else class="person-empty-state">
			<b>Комплектов пока нет</b>
			<p>Наденьте нужные вещи и сохраните их под своим названием.</p>
		</div>
	</section>
</template>

<script setup>
	import { computed } from 'vue';
	import { useForm } from '@inertiajs/vue3';

	defineProps({
		sets: Array,
	});

	const saveForm = useForm({ action: 'save', name: '' });
	const actionForm = useForm({ action: '', id: null });
	const busy = computed(() => saveForm.processing || actionForm.processing);

	function save() {
		if (busy.value) {
			return;
		}

		actionForm.clearErrors();

		saveForm.post('/person/inventory/sets', {
			preserveScroll: true,
			onSuccess: () => saveForm.reset('name'),
		});
	}

	function act(action, id) {
		if (busy.value) {
			return;
		}

		saveForm.clearErrors();

		actionForm.action = action;
		actionForm.id = id;
		actionForm.post('/person/inventory/sets', { preserveScroll: true });
	}
</script>
