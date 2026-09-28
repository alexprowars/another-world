<template>
	<section class="arena-lobby">
		<header class="arena-lobby-heading">
			<div>
				<h1>Арена</h1>
				<p>Выберите зал для поединка или тренировки.</p>
			</div>
			<Link href="/map?room=23" class="ui-button ui-button--compact ui-button--secondary">
				<GameIcon name="back" />
				В город
			</Link>
		</header>

		<div class="arena-lobby-rooms">
			<article v-for="room in rooms" :key="room.id" class="ui-panel arena-lobby-room">
				<header class="arena-lobby-room-heading">
					<div class="ui-emblem"><GameIcon :name="room.icon" /></div>
					<div>
						<h2>{{ room.title }}</h2>
						<span class="arena-lobby-level">Уровни 0–15</span>
					</div>
				</header>
				<p class="arena-lobby-description">{{ room.description }}</p>
				<footer class="arena-lobby-room-footer">
					<span class="arena-lobby-population" :class="{ 'arena-lobby-population--occupied': page[room.members] > 0 }">
						<GameIcon name="users" />
						<template v-if="page[room.members] > 0">
							Воинов в зале:
							<strong>{{ page[room.members] }}</strong>
						</template>
						<template v-else>В зале пока никого</template>
					</span>
					<Link :href="'/map?room=' + room.id" class="ui-button">
						Войти в зал
						<GameIcon name="forward" />
					</Link>
				</footer>
			</article>
		</div>

		<section class="arena-lobby-hospital">
			<div class="ui-emblem"><GameIcon name="health" /></div>
			<div class="arena-lobby-hospital-copy">
				<h2>Больница</h2>
				<p>Восстановите здоровье и вылечите травмы после боя.</p>
			</div>
			<Link href="/map?room=8" class="ui-button ui-button--compact ui-button--secondary">
				Перейти в больницу
				<GameIcon name="forward" />
			</Link>
		</section>
	</section>
</template>

<script setup>
	import GameLayout from '~/layouts/Game.vue';
	import PersonLayout from '~/layouts/Person.vue';
	import GameIcon from '~/components/Layout/GameIcon.vue';
	import { Link } from '@inertiajs/vue3';

	defineOptions({
		layout: [GameLayout, PersonLayout],
	});

	defineProps({
		page: Object,
	});

	const rooms = [
		{
			id: 1,
			title: 'Боевая комната',
			description: 'Сражайтесь с другими игроками: создайте заявку на бой или примите вызов соперника.',
			icon: 'swords',
			members: 'room_1_members',
		},
		{
			id: 2,
			title: 'Тренировочный зал',
			description: 'Освойте удары и блоки в тренировочных поединках с ботами.',
			icon: 'character',
			members: 'room_2_members',
		},
	];
</script>
