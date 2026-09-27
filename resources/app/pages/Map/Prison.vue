<template>
	<ContentBlock title="Тюрьма">
		<div class="mb-4 flex w-full justify-end gap-1">
			<Link href="/map"><img src="/assets/images/images/refresh.gif" alt="Обновить"></Link>
			<Link v-if="!page.until" href="/map/change/666"><img src="/assets/images/images/back.gif" alt="Вернуться"></Link>
		</div>

		<div v-if="page.until" class="space-y-4">
			<div class="flex flex-wrap items-center gap-2">
				<span>Оставшийся срок наказания:</span>
				<Timer :key="page.until" :value="page.until" :callback="onTimeout" class="font-bold"/>
			</div>
			<p class="border-t border-slate-300 pt-4">
				Причина отправки в тюрьму: <span class="font-bold text-red-600 underline">{{ page.reason }}</span>
			</p>
		</div>

		<div v-else class="space-y-4 text-center">
			<p class="font-bold">
				Вы пока еще на свободе!<br>
				Надеемся, Вы сюда не попадёте никогда :)
			</p>

			<template v-if="page.prisoners.length">
				<p class="font-bold">Список заключённых:</p>
				<div class="overflow-x-auto">
					<table class="w-full border-collapse">
						<thead>
							<tr class="border-b border-slate-300">
								<th class="w-1/4 px-2 py-2">Ник</th>
								<th class="w-1/2 px-2 py-2">Причина</th>
								<th class="w-1/4 px-2 py-2">Дата освобождения</th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="prisoner in page.prisoners" :key="prisoner.id" class="border-b border-slate-200 last:border-b-0">
								<td class="px-2 py-2 font-bold">{{ prisoner.name }}</td>
								<td class="px-2 py-2 font-bold">{{ prisoner.reason }}</td>
								<td class="px-2 py-2 font-bold">{{ $formatDate(prisoner.until, 'DD.MM.YYYY HH:mm') }}</td>
							</tr>
						</tbody>
					</table>
				</div>
			</template>
		</div>
	</ContentBlock>
</template>

<script setup>
	import { Link, router } from '@inertiajs/vue3';
	import ContentBlock from '~/components/ContentBlock.vue';
	import Timer from '~/components/Timer.vue';

	defineProps({
		page: Object,
	});

	function onTimeout() {
		router.reload();
	}
</script>
