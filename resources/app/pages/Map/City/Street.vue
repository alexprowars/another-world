<template>
	<CityLocation
		:title="page.name"
		:description="page.description"
		:image-path="page.image_path"
		:map="page.map"
		:places="page.places"
		:places-title="page.places_title"
		:exits="page.exits"
	/>
</template>

<script setup>
	import CityLocation from '~/components/City/CityLocation.vue';

	defineProps({
		page: { type: Object, required: true },
	});
</script>
