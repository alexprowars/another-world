<template>
	<div>
		<a href="#" @click.prevent="toUser" @contextmenu.prevent="toPrivate">
			<span style="color: #CFA87A">{{ user.name }} [{{ user.level }}]</span>
		</a>
		<span :style="{ color: user.finished ? 'red' : 'black' }">
			<small> [{{ user.hp }} HP]</small>
		</span>
	</div>
</template>

<script setup>
	const props = defineProps({
		user: Object,
	});

	function toUser() {
		//
	}

	function toPrivate() {
		//
	}
</script>
