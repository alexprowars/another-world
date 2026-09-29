<template>
	<div class="online-panel">
		<div class="online-heading">
			<span class="online-indicator"></span>
			<h2>Игроки онлайн</h2>
			<span class="online-count">{{ users.length }}</span>
			<label class="sr-only" for="player-sort">Сортировка игроков</label>
			<select id="player-sort" v-model="sort" class="online-sort" title="Сортировка игроков">
				<option value="name">А–Я</option>
				<option value="level">Ур. ↓</option>
			</select>
		</div>
		<div class="online-list">
			<p v-if="error" class="online-empty" role="alert">{{ error }}</p>
			<p v-else-if="loading && !users.length" class="online-empty" role="status">Загрузка игроков…</p>
			<template v-else>
				<OnlineUser v-for="item in visibleUsers" :key="item.id" :user="item" @player="emit('player', $event)" @private="emit('private', $event)" />
				<p v-if="!visibleUsers.length" class="online-empty">Пока никого нет</p>
			</template>
		</div>
	</div>
</template>

<script setup>
	import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
	import { useLocalStorage } from '@vueuse/core';
	import OnlineUser from './OnlineUser.vue';
	import { useHttp } from '@inertiajs/vue3';

	const emit = defineEmits(['player', 'private']);
	const users = ref([]);
	const sort = useLocalStorage('game.chat.sort', 'name', { initOnMounted: true });
	const loading = ref(false);
	const error = ref('');

	let refreshTimer;

	const visibleUsers = computed(() => {
		return users.value.toSorted((a, b) => {
			if (sort.value === 'level') {
				const levelDifference = (Number(b.level) || 0) - (Number(a.level) || 0);

				if (levelDifference !== 0) {
					return levelDifference;
				}
			}

			return a.name.localeCompare(b.name, 'ru');
		});
	});

	defineExpose({
		refresh: loadChatList,
		loading
	});

	onMounted(() => {
		loadChatList();
		refreshTimer = setInterval(loadChatList, 60000);
	});

	onBeforeUnmount(() => clearInterval(refreshTimer));

	async function loadChatList() {
		if (loading.value) {
			return;
		}

		loading.value = true;
		error.value = '';

		try {
			const result = await useHttp().get('/chat/online');
			users.value = result.users;
		} catch {
			error.value = 'Не удалось обновить список. Попробуйте ещё раз.';
		} finally {
			loading.value = false;
		}
	}
</script>
