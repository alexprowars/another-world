<template>
	<ContentBlock title="Академия">
		<div class="mb-4 flex w-full justify-end gap-1">
			<a href="/map"><img src="/assets/images/images/refresh.gif" alt="Обновить"></a>
			<a href="/map/change/9"><img src="/assets/images/images/back.gif" alt="Вернуться"></a>
		</div>

		<div class="w-full text-center">
			<div v-if="user.r_date" class="inline-flex flex-wrap items-center justify-center gap-2 text-red-600">
				<span class="font-bold">Оставшееся время обучения:</span>
				<Timer :value="user.r_date" class="font-bold underline"/>
			</div>

			<div v-else class="space-y-4">
				<p class="font-bold">
					В нашем заведении Вы можете стать высококвалифицированным специалистом.
					Ниже приведён список предлагаемых Вам профессий:
				</p>

				<div class="overflow-x-auto">
					<table class="w-full border-collapse text-left">
						<thead>
							<tr class="border-b border-slate-300">
								<th class="w-5 px-2 py-2 text-center font-bold">№</th>
								<th class="px-2 py-2 font-bold">Наименование</th>
								<th class="w-36 px-2 py-2 text-center font-bold">Уровень</th>
								<th class="w-36 px-2 py-2 text-center font-bold">Срок обучения</th>
								<th class="w-40 px-2 py-2 text-center font-bold">Стоимость обучения</th>
								<th class="w-32 px-2 py-2 text-center font-bold">Действие</th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="(item, i) in page.professions" :key="item.id" class="border-b border-slate-200 last:border-b-0">
								<td class="px-2 py-2 text-center font-bold">{{ i + 1 }}</td>
								<td class="px-2 py-2 font-bold">{{ item['title'] }}</td>
								<td class="px-2 py-2 text-center font-bold">{{ item['level'] }}</td>
								<td class="px-2 py-2 text-center font-bold">{{ $formatTime(item['duration']) }}</td>
								<td class="px-2 py-2 text-center font-bold">{{ item['price'] }} зол.</td>
								<td class="px-2 py-2 text-center">
									<a href="" @click.prevent="learn(item)"
										class="inline-flex min-h-8 items-center justify-center rounded bg-blue-600 px-3 py-1.5 text-sm font-bold text-white hover:bg-blue-700"
									>
										Обучаться
									</a>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</ContentBlock>
</template>

<script setup>
	import ContentBlock from '~/components/ContentBlock.vue';
	import useState from '~/composables/useState.js';
	import { computed } from 'vue';
	import Timer from '~/components/Timer.vue';
	import { openConfirmModal } from '~/composables/useModals.js';
	import { router } from '@inertiajs/vue3';

	defineProps({
		page: Object,
	});

	const state = useState();
	const user = computed(() => state.user);

	function learn(item) {
		openConfirmModal(
			'Подтвердите действие',
			'Вы действительно хотите получить данную профессию?',
			[{
				title: 'Нет',
			}, {
				title: 'Да',
				handler() {
					router.post('/map', {
						learn: item.id,
					})
				}
			}]
		)
	}
</script>
