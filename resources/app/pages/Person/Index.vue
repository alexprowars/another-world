<template>
	<div class="textblock text-center text-sm">
		<br><b><span color=black>Со Сводом Законов игры можно ознакомиться </span> <a href="/person?type=law" target="_blank">здесь</a></b><br/><br/>

		<template v-if="user.level < 3">
			<b><span color=black>Приветствуем Вас! Спасибо Вам за регистрацию в Других Мирах, только у нас оригинальные бои, новый взгляд на мир фэнтези. Для того чтобы начать играть Вам необходимо раскидать умения (сверху кнопка УМЕНИЯ), после того как Вы расставили все умения нажимаем кнопку ГОРОД (меню над чатом) и идем в Тренировочную комнату.<br> Удачной Вам Игры!<br></span></b>
			<br>
		</template>

		<br>
		<br>

		<span color="red"><b>Завербуй друга получи кучу денег - </b></span>
		<b><a :href="'https://aw-game.ru/?' + user.id" target="_blank"><span color=blue>https://aw-game.ru/?{{ user.id }}</span></a></b>
		( <a href="/person/work"><span color="red">подробнее</span></a> )
		<br>
	</div>
</template>

<script setup>
	import GameLayout from '~/layouts/Game.vue';
	import PersonLayout from '~/layouts/Person.vue';
	import useState from '~/composables/useState.js';
	import { computed } from 'vue';

	defineOptions({
		layout: [GameLayout, PersonLayout]
	});

	const state = useState();
	const user = computed(() => state.user);
</script>