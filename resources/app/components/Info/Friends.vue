<template>
	<details>
		<summary class="cursor-pointer font-bold">Списки друзей персонажа</summary>
		<div class="mt-3 flex flex-wrap gap-2">
			<b>Друзья:</b>
			<Link v-for="friend in friends" :key="friend.id" :href="'/info/' + friend.id" class="underline">{{ friend.name }}</Link>
			<span v-if="!friends.length">Нет</span>
		</div>
		<div class="mt-3 flex flex-wrap gap-2">
			<b>В друзьях у:</b>
			<Link v-for="friend in friendOf" :key="friend.id" :href="'/info/' + friend.id" class="underline">{{ friend.name }}</Link>
			<span v-if="!friendOf.length">Ни у кого</span>
		</div>
	</details>
</template>

<script setup>
	import { Link } from '@inertiajs/vue3';

	defineProps({
		friends: Array,
		friendOf: Array,
	});
</script>
