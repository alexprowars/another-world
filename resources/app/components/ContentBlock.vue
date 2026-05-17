<template>
	<CityHeader title="Бесплатные образы"/>
	<div class="textblock">
		<div v-if="page.flash.message" class="p-4 mb-4 bg-red-100 text-red-600 text-center">
			{{ page.flash.message }}
		</div>

		<slot/>
	</div>
</template>

<script setup>
	import CityHeader from '~/components/CityHeader.vue';
	import { usePage } from '@inertiajs/vue3';

	defineProps({
		title: {
			type: String,
			default: '',
		}
	});

	const page = usePage();
</script>