<template>
	<div class="battle-member" :class="{ 'battle-member--finished': user.finished }">
		<a href="#" @click.prevent="toUser" @contextmenu.prevent="toPrivate">
			<span>{{ user.name }} [{{ user.level }}]</span>
		</a>
		<span class="battle-member__health">
			<small>{{ user.hp }} HP</small>
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
