<template>
	<section class="person-development">
		<header class="person-page-heading">
			<div>
				<h1>Развитие персонажа</h1>
				<p>Усильте характеристики за свободные очки.</p>
			</div>
			<div class="person-points">
				<span>Свободные очки</span>
				<b>{{ user.updates }}</b>
			</div>
		</header>

		<p v-if="page?.message" class="ui-notice ui-notice--blue ui-notice--compact">{{ page.message }}</p>
		<p v-if="!user.updates" class="person-page-hint">Все очки распределены. Новые очки появятся при повышении уровня.</p>

		<div class="person-upgrade-list">
			<div v-for="stat in stats" :key="stat" class="person-upgrade-row">
				<img :src="'/assets/images/stats/' + stat + '.png'" class="person-stat-icon" alt="" />
				<div class="person-upgrade-description">
					<h2>{{ $t('stats.' + stat) }}</h2>
					<p>{{ $t('stats-info.' + stat) }}</p>
				</div>
				<b class="person-upgrade-value">{{ user['s_' + stat] || 0 }}</b>
				<button
					type="button"
					class="ui-button ui-button--compact"
					:disabled="!user.updates || processing"
					:title="'Увеличить: ' + $t('stats.' + stat)"
					@click="updateStat(stat)"
				>
					+1
				</button>
			</div>
		</div>
	</section>
</template>

<script setup>
	import useState from '~/composables/useState.js';
	import { computed, ref } from 'vue';
	import { openConfirmModal } from '~/composables/useModals.js';
	import { router } from '@inertiajs/vue3';
	import { useI18n } from 'vue-i18n';
	import GameLayout from '~/layouts/Game.vue';
	import PersonLayout from '~/layouts/Person.vue';

	defineOptions({
		layout: [GameLayout, PersonLayout],
	});

	defineProps({ page: Object });

	const { t } = useI18n();
	const state = useState();
	const user = computed(() => state.user);
	const processing = ref(false);
	const stats = ['strength', 'dexterity', 'agility', 'vitality', 'magic', 'intelligence'];

	function updateStat(stat) {
		if (!user.value.updates || processing.value) return;

		openConfirmModal('Увеличить характеристику', 'Потратить 1 очко на параметр «' + t('stats.' + stat) + '»?', [
			{ title: 'Отмена' },
			{
				title: 'Увеличить',
				handler() {
					if (processing.value) return;

					router.post(
						'/person/updates',
						{ update: stat },
						{
							preserveScroll: true,
							onStart: () => {
								processing.value = true;
							},
							onFinish: () => {
								processing.value = false;
							},
						},
					);
				},
			},
		]);
	}
</script>
