<template>
	<Head title="Друзья" />
	<section class="person-friends">
		<header class="person-page-heading">
			<div>
				<h1>Друзья и враги</h1>
				<p>Следите за друзьями и управляйте списком игнорирования.</p>
			</div>
			<Link href="/person/friends" class="ui-button ui-button--compact ui-button--secondary" preserve-scroll>Обновить</Link>
		</header>

		<div class="person-section-heading-row">
			<h2 class="person-section-heading">Ваш список</h2>
			<span class="person-section-count">{{ page.friends.length }}</span>
		</div>
		<div v-if="page.friends.length" class="ui-table-wrap person-friends-table-wrap">
			<table class="ui-table person-friends-table">
				<thead>
					<tr>
						<th scope="col">Персонаж</th>
						<th scope="col">Комната</th>
						<th scope="col">Отношение</th>
						<th scope="col">Статус</th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="entry in page.friends" :key="entry.id">
						<td class="person-friend-name"><Name :player="entry.user" /></td>
						<td data-label="Комната">{{ $t('rooms.' + entry.user.room) }}</td>
						<td data-label="Отношение">
							<span class="person-relation" :class="{ 'is-ignored': entry.is_ignored }">
								{{ entry.is_ignored ? 'Враг · игнор' : 'Друг' }}
							</span>
						</td>
						<td data-label="Статус">
							<span class="person-online-status" :class="{ 'is-online': entry.user.is_online }">
								{{ entry.user.is_online ? 'В сети' : 'Не в сети' }}
							</span>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div v-else class="person-empty-state">
			<b>В вашем списке пока никого нет</b>
			<p>Добавьте персонажа по нику с помощью формы ниже.</p>
		</div>

		<div class="person-form-grid person-friend-forms">
			<form class="person-form-panel" @submit.prevent="add">
				<header class="person-form-heading">
					<h2>Добавить персонажа</h2>
					<p>Выберите, кем он будет в вашем списке.</p>
				</header>
				<div v-if="Object.keys(addForm.errors).length" class="ui-notice ui-notice--red ui-notice--compact" role="alert">
					<p v-for="(error, field) in addForm.errors" :key="field">{{ error }}</p>
				</div>
				<label class="person-form-field">
					<span>Ник персонажа</span>
					<input
						class="ui-input ui-input--compact"
						v-model.trim="addForm.name"
						type="text"
						maxlength="100"
						required
						placeholder="Введите ник"
						:class="{ 'is-invalid': addForm.errors.name }"
					/>
				</label>
				<label class="person-form-field">
					<span>Отношение</span>
					<select class="ui-input ui-input--compact" v-model="addForm.is_ignored">
						<option :value="false">Друг</option>
						<option :value="true">Враг (игнор)</option>
					</select>
				</label>
				<footer class="person-form-actions">
					<button type="submit" class="ui-button ui-button--compact" :disabled="busy">
						{{ addForm.processing ? 'Добавление…' : 'Добавить' }}
					</button>
				</footer>
			</form>

			<form class="person-form-panel" @submit.prevent="remove">
				<header class="person-form-heading">
					<h2>Удалить из списка</h2>
					<p>Укажите ник друга или игнорируемого персонажа.</p>
				</header>
				<div v-if="Object.keys(removeForm.errors).length" class="ui-notice ui-notice--red ui-notice--compact" role="alert">
					<p v-for="(error, field) in removeForm.errors" :key="field">{{ error }}</p>
				</div>
				<label class="person-form-field">
					<span>Ник персонажа</span>
					<input
						class="ui-input ui-input--compact"
						v-model.trim="removeForm.name"
						type="text"
						maxlength="100"
						required
						placeholder="Введите ник"
						:class="{ 'is-invalid': removeForm.errors.name }"
					/>
				</label>
				<footer class="person-form-actions">
					<button type="submit" class="ui-button ui-button--compact ui-button--secondary" :disabled="busy">
						{{ removeForm.processing ? 'Удаление…' : 'Удалить' }}
					</button>
				</footer>
			</form>
		</div>
	</section>
</template>

<script setup>
	import { computed } from 'vue';
	import { Head, Link, useForm } from '@inertiajs/vue3';
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
