<template>
	<div v-if="users.left?.length && users.right?.length" class="battle-teams" id="usersContent">
		<div class="battle-teams__side">
			<h2>Союзники</h2>
			<BattleUserLink v-for="user in users.left" :key="user.id" :user="user" />
		</div>
		<div class="battle-teams__side">
			<h2>Противники</h2>
			<BattleUserLink v-for="user in users.right" :key="user.id" :user="user" />
		</div>
	</div>
</template>

<script setup>
	import BattleUserLink from './BattleUserLink.vue';

	defineProps({
		users: {
			type: Object,
			default: () => ({ team_my: [], team: [] }),
		},
	});
</script>
