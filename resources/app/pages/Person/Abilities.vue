<template>
	<div class="textblock">
		<p v-if="false" class="message alert-danger">{{ message }}</p>

		<div v-if="Object.keys(page.active).length">
			<div class="text-base font-bold">Список активных приёмов:</div>
			<table class="w-full text-center table-auto min-w-max text-sm border">
				<thead>
					<tr>
						<th width="50" class="p-1">№</th>
						<th width="300" class="p-1">Название</th>
						<th class="p-1">Действие</th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="(item, i) in page.active" class="even:bg-gray-50/50">
						<td class="p-1">{{ i }}</td>
						<td class="p-1">{{ page.items[item]['name'] }}</td>
						<td class="p-1">
							<a class="btn btn-danger" href="" @click.prevent="deactivateAbility(i)">Убрать</a>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="text-base font-bold my-2">Список доступных приёмов:</div>
		<table class="w-full text-center table-auto min-w-max text-sm border">
			<thead>
				<tr class="border-b">
					<th width="150" class="px-2 py-1">Название</th>
					<th class="px-2 py-1">Информация</th>
					<th width="20" class="px-2 py-1">lvl</th>
					<th width="20" class="px-2 py-1"><img src="/assets/images/battle/abilities/blocks.gif"></th>
					<th width="20" class="px-2 py-1"><img src="/assets/images/battle/abilities/hits.gif"></th>
					<th width="20" class="px-2 py-1"><img src="/assets/images/battle/abilities/crits.gif"></th>
					<th width="20" class="px-2 py-1"><img src="/assets/images/battle/abilities/parry.gif"></th>
					<th width="20" class="px-2 py-1"><img src="/assets/images/battle/abilities/hp.gif"></th>
					<th width="20" class="px-2 py-1"><img src="/assets/images/battle/abilities/magic.gif"></th>
					<th class="px-2 py-1">Действие</th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="(item, id) in page.items" class="even:bg-gray-50/50">
					<td class="px-2 py-1">
						{{ item['name'] }}
					</td>
					<td class="px-2 py-1">
						{{ item['about'] }}
					</td>
					<td class="px-2 py-1">{{ item['level'] }}</td>
					<td class="px-2 py-1">{{ item['block'] }}</td>
					<td class="px-2 py-1">{{ item['hit'] }}</td>
					<td class="px-2 py-1">{{ item['crit'] }}</td>
					<td class="px-2 py-1">{{ item['parry'] }}</td>
					<td class="px-2 py-1">{{ item['damage'] }}</td>
					<td class="px-2 py-1">{{ item['magic'] }}</td>
					<td class="px-2 py-1">
						<a v-if="!item['onset']" class="inline-block rounded-md bg-green-600 py-1 px-2 border border-transparent text-center text-sm text-white transition-all shadow-md hover:shadow-lg focus:bg-slate-700 focus:shadow-none active:bg-slate-700 hover:bg-slate-700 active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none" href="" @click.prevent="activateAbility(id)">Использовать</a>
					</td>
				</tr>
			</tbody>
		</table>
		<div class="text-base font-bold my-2">Легенда:</div>
		<table>
			<tr>
				<td class="text-center" width="30"><img src="/assets/images/battle/abilities/blocks.gif"></td>
				<td class="px-2 py-0.5">Очки блока</td>
			</tr>
			<tr>
				<td class="text-center"><img src="/assets/images/battle/abilities/hits.gif"></td>
				<td class="px-2 py-0.5">Очки пробоя</td>
			</tr>
			<tr>
				<td class="text-center"><img src="/assets/images/battle/abilities/crits.gif"></td>
				<td class="px-2 py-0.5">Очки крита</td>
			</tr>
			<tr>
				<td class="text-center"><img src="/assets/images/battle/abilities/parry.gif"></td>
				<td class="px-2 py-0.5">Очки уворота</td>
			</tr>
			<tr>
				<td class="text-center"><img src="/assets/images/battle/abilities/hp.gif"></td>
				<td class="px-2 py-0.5">Очки повреждений</td>
			</tr>
			<tr>
				<td class="text-center"><img src="/assets/images/battle/abilities/magic.gif"></td>
				<td class="px-2 py-0.5">Очки магии</td>
			</tr>
		</table>
	</div>
</template>

<script setup>
	import GameLayout from '~/layouts/Game.vue';
	import PersonLayout from '~/layouts/Person.vue';
	import { router, useForm } from '@inertiajs/vue3';

	defineOptions({
		layout: [GameLayout, PersonLayout]
	});

	defineProps({
		page: Object,
	});

	function activateAbility(id) {
		useForm({
			onset: id,
		})
		.post('/person/abilities');
	}

	function deactivateAbility(id) {
		useForm({
			unset: id,
		})
		.post('/person/abilities');
	}
</script>