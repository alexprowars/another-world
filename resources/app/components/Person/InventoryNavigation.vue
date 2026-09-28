<template>
	<nav class="ui-tabs ui-tabs--compact inventory-categories">
		<Link
			class="ui-tab"
			v-for="(title, id) in $tm('inventory')"
			:key="id"
			:href="'/person/inventory?item_type=' + id"
			:class="{ 'is-active': Number(id) === active }"
		>
			{{ title }}
		</Link>
		<Link class="ui-tab" href="/person/inventory/sets" :class="{ 'is-active': active === 'sets' }">Комплекты</Link>
	</nav>
</template>

<script setup>
	import { Link } from '@inertiajs/vue3';

	defineProps({
		active: [Number, String],
	});
</script>
