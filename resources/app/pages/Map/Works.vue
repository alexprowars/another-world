<template>
	<ContentBlock title="Центр занятости">
		<div class="mb-4 flex w-full justify-end gap-1">
			<Link href="/map"><img src="/assets/images/images/refresh.gif" alt="Обновить"></Link>
			<Link v-if="!busy" href="/map/change/16"><img src="/assets/images/images/back.gif" alt="Вернуться"></Link>
		</div>

		<div v-if="busy" class="space-y-2 text-center">
			<div v-if="user.r_date" class="inline-flex flex-wrap items-center justify-center gap-2 text-red-600">
				<span class="font-bold">Оставшееся время работы:</span>
				<Timer :key="user.r_date" :value="user.r_date" :callback="onTimeout" class="font-bold underline"/>
			</div>
			<p v-if="page.salary !== null">Зарплата по окончании работы: <b>{{ page.salary }} зол.</b></p>
		</div>

		<div v-else class="space-y-4">
			<fieldset v-for="type in page.types" :key="type.id" class="rounded border border-slate-300 p-3">
				<legend class="px-2 font-bold">{{ type.title }} (требует {{ type.level }} уровень и {{ type.activity }} ед. сил за час)</legend>
				<div class="overflow-x-auto">
					<table class="w-full border-collapse text-left">
						<thead>
							<tr class="border-b border-slate-300">
								<th class="w-5 px-2 py-2 text-center">№</th>
								<th class="px-2 py-2">Наименование</th>
								<th class="w-36 px-2 py-2 text-center">Срок работы</th>
								<th class="w-40 px-2 py-2 text-center">Зарплата</th>
								<th class="w-32 px-2 py-2 text-center">Действие</th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="(work, index) in type.works" :key="work.id" class="border-b border-slate-200 last:border-b-0">
								<td class="px-2 py-2 text-center font-bold">{{ index + 1 }}</td>
								<td class="px-2 py-2 font-bold">{{ work.title }}</td>
								<td class="px-2 py-2 text-center font-bold">{{ $formatTime(work.duration) }}</td>
								<td class="px-2 py-2 text-center font-bold">{{ work.price }} зол.</td>
								<td class="px-2 py-2 text-center">
									<button type="button" class="btn btn-primary" :disabled="form.processing" @click="start(work)">Работать</button>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</fieldset>
		</div>
	</ContentBlock>
</template>

<script setup>
	import { computed } from 'vue';
	import { Link, router, useForm } from '@inertiajs/vue3';
	import ContentBlock from '~/components/ContentBlock.vue';
	import Timer from '~/components/Timer.vue';
	import useState from '~/composables/useState.js';
	import { openConfirmModal } from '~/composables/useModals.js';

	defineProps({ page: Object });

	const state = useState();
	const user = computed(() => state.user);
	const busy = computed(() => !!user.value.r_date || !!user.value.r_type);
	const form = useForm({ work: null });

	function start(work) {
		openConfirmModal(
			'Подтвердите действие',
			'Вы действительно хотите получить данную работу?',
			[{
				title: 'Нет',
			}, {
				title: 'Да',
				handler() {
					form.work = work.id;
					form.post('/map');
				},
			}],
		);
	}

	function onTimeout() {
		router.reload();
	}
</script>
