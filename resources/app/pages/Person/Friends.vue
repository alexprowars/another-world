<template>
	<div class="textblock space-y-4">
		<div class="flex flex-wrap items-center justify-between gap-3">
			<h2 class="font-bold">Список друзей и врагов</h2>
			<div class="flex gap-3">
				<Link href="/person/friends" preserve-scroll>Обновить</Link>
				<Link href="/person">Вернуться</Link>
			</div>
		</div>

		<div v-if="page.friends.length" class="overflow-x-auto">
			<table class="w-full text-left">
				<thead>
					<tr class="border-b border-slate-300">
						<th class="px-2 py-2">Ник</th>
						<th class="px-2 py-2">Комната</th>
						<th class="px-2 py-2 text-center">Кем является</th>
						<th class="px-2 py-2 text-center">Статус</th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="entry in page.friends" :key="entry.id" class="border-b border-slate-200 last:border-b-0">
						<td class="px-2 py-2"><Name :player="entry.user"/></td>
						<td class="px-2 py-2">{{ $t('rooms.' + entry.user.room) }}</td>
						<td class="px-2 py-2 text-center font-bold" :class="entry.is_ignored ? 'text-red-600' : 'text-green-700'">
							{{ entry.is_ignored ? 'Враг (игнор)' : 'Друг' }}
						</td>
						<td class="px-2 py-2 text-center" :class="entry.user.is_online ? 'text-green-700' : 'text-slate-500'">
							{{ entry.user.is_online ? 'В сети' : 'Не в сети' }}
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<p v-else class="py-4 text-center text-slate-500">Ваш список пока пуст.</p>

		<div class="grid gap-4 lg:grid-cols-2">
			<form @submit.prevent="add">
				<div class="rounded border border-slate-300 p-4 space-y-3">
					<h3 class="font-bold">Добавить персонажа</h3>
					<div v-if="Object.keys(addForm.errors).length" class="text-red-600" role="alert">
						<p v-for="(error, field) in addForm.errors" :key="field">{{ error }}</p>
					</div>
					<label class="block space-y-1">
						<span class="block">Ник персонажа</span>
						<input v-model.trim="addForm.name" type="text" maxlength="100" required class="w-full rounded border border-slate-300 px-2 py-1">
					</label>
					<label class="block space-y-1">
						<span class="block">Кем будет являться</span>
						<select v-model="addForm.is_ignored" class="w-full rounded border border-slate-300 px-2 py-1">
							<option :value="false">Друг</option>
							<option :value="true">Враг</option>
						</select>
					</label>
					<button type="submit" class="btn btn-primary" :disabled="busy">Добавить</button>
				</div>
			</form>

			<form @submit.prevent="remove">
				<div class="rounded border border-slate-300 p-4 space-y-3">
					<h3 class="font-bold">Удалить персонажа</h3>
					<div v-if="Object.keys(removeForm.errors).length" class="text-red-600" role="alert">
						<p v-for="(error, field) in removeForm.errors" :key="field">{{ error }}</p>
					</div>
					<label class="block space-y-1">
						<span class="block">Ник персонажа</span>
						<input v-model.trim="removeForm.name" type="text" maxlength="100" required class="w-full rounded border border-slate-300 px-2 py-1">
					</label>
					<button type="submit" class="btn btn-primary" :disabled="busy">Удалить</button>
				</div>
			</form>
		</div>
	</div>
</template>

<script setup>
	import { computed } from 'vue';
	import { Link, useForm } from '@inertiajs/vue3';
	import GameLayout from '~/layouts/Game.vue';
	import PersonLayout from '~/layouts/Person.vue';
	import Name from '~/components/Person/Name.vue';

	defineOptions({ layout: [GameLayout, PersonLayout] });
	defineProps({ page: Object });

	const addForm = useForm({ action: 'add', name: '', is_ignored: false });
	const removeForm = useForm({ action: 'remove', name: '' });
	const busy = computed(() => addForm.processing || removeForm.processing);

	function add() {
		if (!busy.value) addForm.post('/person/friends', { preserveScroll: true });
	}

	function remove() {
		if (!busy.value) removeForm.post('/person/friends', { preserveScroll: true });
	}
</script>
