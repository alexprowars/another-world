<template>
	<div v-if="users.left?.length && users.right?.length" class="text-center mb-3 flex justify-center w-full" id="usersContent">
		<div class="w-1/2 align-top">
			<div><b>Союзники</b></div>
			<BattleUserLink v-for="user in users.left" :key="user.id" :user="user"/>
		</div>
		<div class="w-1/2 align-top">
			<div><b>Противники</b></div>
			<BattleUserLink v-for="user in users.right" :key="user.id" :user="user"/>
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
