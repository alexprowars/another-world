<template>
	<div class="person-abilities">
		<header class="person-page-heading">
			<div>
				<h1>Боевые приёмы</h1>
				<p>Выберите приёмы, которые будете использовать в бою.</p>
			</div>
			<span class="person-section-count">Активно: {{ activeCount }}</span>
		</header>

		<section class="person-ability-section">
			<h2 class="person-section-heading">Активные приёмы</h2>

			<div v-if="activeCount" class="person-active-abilities">
				<div v-for="(id, slot) in page.active" :key="slot" class="person-active-ability">
					<span class="person-ability-slot">{{ slot }}</span>
					<img :src="'/assets/images/battle/abilities/' + id + '.gif'" class="person-ability-icon" alt="" />
					<span class="person-active-ability-name">{{ page.items[id].name }}</span>
					<button type="button" class="ui-button ui-button--compact ui-button--secondary" :disabled="form.processing"
						@click="deactivateAbility(slot)"
					>
						Убрать
					</button>
				</div>
			</div>
			<p v-else class="person-page-hint">Активных приёмов пока нет. Добавьте их из списка ниже.</p>
		</section>

		<section class="person-ability-section">
			<div class="person-section-heading-row">
				<h2 class="person-section-heading">Доступные приёмы</h2>
				<span class="person-section-count">{{ Object.keys(page.items).length }}</span>
			</div>
			<div class="person-ability-list">
				<AbilityCard
					v-for="(item, id) in page.items"
					:key="id"
					:item="item"
					:ability-id="id"
					:processing="form.processing"
					@activate="activateAbility(id)"
				/>
			</div>
			<p v-if="!Object.keys(page.items).length" class="person-page-hint">Пока нет доступных приёмов.</p>
		</section>
	</div>
</template>

<script setup>
	import { computed } from 'vue';
	import GameLayout from '~/layouts/Game.vue';
	import PersonLayout from '~/layouts/Person.vue';
	import AbilityCard from '~/components/Person/AbilityCard.vue';
	import { useForm } from '@inertiajs/vue3';

	defineOptions({
		layout: [GameLayout, PersonLayout],
	});

	const props = defineProps({
		page: Object
	});

	const activeCount = computed(() => Object.keys(props.page.active).length);

	const form = useForm({
		onset: null,
		unset: null
	});

	function activateAbility(id) {
		if (form.processing) {
			return;
		}

		form.onset = id;
		form.unset = null;
		form.post('/person/abilities', { preserveScroll: true });
	}

	function deactivateAbility(slot) {
		if (form.processing) {
			return;
		}

		form.onset = null;
		form.unset = slot;
		form.post('/person/abilities', { preserveScroll: true });
	}
</script>
