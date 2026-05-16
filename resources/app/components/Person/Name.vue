<template>
	<div class="personName">
		<img v-if="player.rank" :src="'/assets/images/rank/' + player.rank + '.gif'" height="15" :alt="$t('rank.' + player.rank)" v-tooltip="$t('rank.' + player.rank)">
		<img v-if="player.tribe" :src="'/assets/images/tribe/' + player.tribe.id + '.gif'" height="15" :alt="'Клан ' + player.tribe.name" v-tooltip="'Клан ' + player.tribe.name">
		<a :href="'/info/' + player.id" target="_blank">{{ player.name }}</a> [{{ player.level }}]
	</div>
</template>

<script setup>
	const props = defineProps({
		player: Object,
	})
</script>