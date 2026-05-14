<template>
	<div id="chatList" class="scrollbox">
		<div class="refresh">
			<a href="javascript:;" onclick="loadChatList()">
				<img src="/assets/images/refresh.png" align="absmiddle" width="16" height="16" title="Обновить список игроков">
			</a>
			<input type="checkbox" value="1" title="Автоматическое обновление списка игроков" v-model.number="autoreload">
		</div>

		<div class="actions">
			<div class="text-left">В игре: 0 | <a href="" @click.prevent="toggleShow">{{ $t('rooms.' + user.room) }}</a> ({{ show === 1 ? 'мир' : 'комната' }})</div>
			<div class="text-xs-center">
				<a href="?sort=1" :class="{ active: false }">а-я</a> |
				<a href="?sort=2" :class="{ active: false }">я-а</a> |
				<a href="?sort=3" :class="{ active: false }">0-10</a> |
				<a href="?sort=4" :class="{ active: false }">10-0</a>
			</div>
		</div>

		<OnlineUser v-for="item in users" :user="item" :key="item.id"/>
	</div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
	import useState from '~/composables/useState.js';
	import OnlineUser from '~/components/Layout/OnlineUser.vue';
	import { useHttp } from '@inertiajs/vue3';

	const state = useState();
	const user = computed(() => state.user);

	const autoreload = ref(0);
	const show = ref(1);
	const users = ref([]);

	function toggleShow() {
		if (show.value === 1) {
			show.value = 2;
		} else {
			show.value = 1;
		}
	}

	onMounted(() => {
		loadChatList();

		setInterval(loadChatList, 60000);
	});

	async function loadChatList () {
		const result = await useHttp()
			.get('/chat/online');

		users.value = result.users;
	}
</script>