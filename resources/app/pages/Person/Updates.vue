<template>
	<div class="textblock">
		<p v-if="page.message" class="message mb-4 bg-red-100 text-red-700">{{ page.message }}</p>

		<div class="flex flex-wrap">
			<div class="w-full text-center">
				<div class="bg-[#930407] p-0.5 font-bold text-white">
					Физические параметры [<u>{{ user.updates }}</u>]
				</div>
			</div>
		</div>
		<table class="w-full">
			<thead>
				<tr>
					<th></th>
					<th>Параметр</th>
					<th class="text-center">Текущая прокачка</th>
					<th class="text-center">Действие</th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="stat in ['strength', 'dexterity', 'agility', 'vitality', 'magic', 'intelligence', 'battery']">
					<td class="align-middle">
						<img src="/assets/images/help.gif" class="text" v-tooltip="{ content: '<b>' + $t('stats.' + stat) + '</b><br>' + $t('stats-info.' + stat), html: true }">
					</td>
					<td class="w-1/3 text-left align-middle">
						{{ $t('stats.' + stat) }}
					</td>
					<td class="w-1/3 text-center align-middle">
						<b>{{ user[stat] || 0 }}</b> очков
					</td>
					<td class="w-1/3 text-center align-middle">
						<template v-if="user.updates">
							<button class="px-3 py-1.5 text-xs rounded bg-green-600 text-white hover:bg-green-700" @click.prevent="updateStat(stat)">
								прокачать
							</button>
						</template>
						<template v-else>
							нет очков
						</template>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</template>

<script setup>
	import useState from '~/composables/useState.js';
	import { computed } from 'vue';
	import { openConfirmModal } from '~/composables/useModals.js';
	import { router } from '@inertiajs/vue3';
	import { useI18n } from 'vue-i18n';
	import GameLayout from '~/layouts/Game.vue';
	import PersonLayout from '~/layouts/Person.vue';

	defineOptions({
		layout: [GameLayout, PersonLayout]
	});

	defineProps({
		page: Object
	});

	const { t } = useI18n();

	const state = useState();
	const user = computed(() => state.user);

	function updateStat(stat) {
		openConfirmModal(
			'Подтвердите действие',
			'Увеличить физический параметр ' + t('stats.' + stat) + '?',
			[{ title: 'Нет' }, {
				title: 'Да',
				handler() {
					router.get('', {
						update: stat,
					});
				},
			},
		]);
	}
</script>
