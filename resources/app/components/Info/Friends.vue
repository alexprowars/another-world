<template>
	<details class="person-info-panel person-info-friends ui-panel" open>
		<summary class="person-info-panel-heading">
			<GameIcon name="users" />
			Друзья персонажа
			<GameIcon name="chevron" class="person-info-chevron" />
		</summary>
		<div class="person-info-friend-group">
			<h3>Друзья <span class="ui-badge">{{ friends.length }}</span></h3>
			<div class="person-info-links">
				<Link v-for="friend in friends" :key="friend.id" :href="'/info/' + friend.id">{{ friend.name }}</Link>
				<span v-if="!friends.length" class="person-info-muted">Друзей пока нет.</span>
			</div>
		</div>
		<div class="person-info-friend-group">
			<h3>В друзьях у <span class="ui-badge">{{ friendOf.length }}</span></h3>
			<div class="person-info-links">
				<Link v-for="friend in friendOf" :key="friend.id" :href="'/info/' + friend.id">{{ friend.name }}</Link>
				<span v-if="!friendOf.length" class="person-info-muted">Ни у кого</span>
			</div>
		</div>
	</details>
</template>

<script setup>
	import { Link } from '@inertiajs/vue3';
	import GameIcon from '~/components/Layout/GameIcon.vue';

	defineProps({
		friends: Array,
		friendOf: Array,
	});
</script>
