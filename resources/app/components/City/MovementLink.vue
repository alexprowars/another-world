<template>
	<Link href="/movement" method="post" as="button" :data="{ location: to }">
		<slot />
	</Link>
</template>

<script setup>
	import { Link } from '@inertiajs/vue3';

	defineProps({
		to: { type: String, required: true },
	});
</script>
