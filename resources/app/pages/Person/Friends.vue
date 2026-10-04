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
						<td data-label="Локация">{{ entry.user.location_name }}</td>
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
			<AddFriendForm :busy="busy" @processing="busy = $event" />
			<RemoveFriendForm :busy="busy" @processing="busy = $event" />
		</div>
	</section>
</template>

<script setup>
	import { ref } from 'vue';
	import { Head, Link } from '@inertiajs/vue3';
	import GameLayout from '~/layouts/Game.vue';
	import PersonLayout from '~/layouts/Person.vue';
	import Name from '~/components/Person/Name.vue';
	import AddFriendForm from '~/components/Person/Friends/AddFriendForm.vue';
	import RemoveFriendForm from '~/components/Person/Friends/RemoveFriendForm.vue';

	defineOptions({
		layout: [GameLayout, PersonLayout]
	});

	defineProps({
		page: Object
	});

	const busy = ref(false);
</script>
