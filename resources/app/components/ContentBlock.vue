<template>
	<section class="location-card">
		<CityHeader :title="title">
			<template v-if="$slots.actions" #actions><slot name="actions" /></template>
		</CityHeader>
		<div class="location-card-body">
			<div v-if="page.flash.message" class="ui-notice ui-notice--blue" role="alert" v-html="page.flash.message"></div>
			<slot />
		</div>
		<footer v-if="$slots.footer" class="location-card-footer"><slot name="footer" /></footer>
	</section>
</template>

<script setup>
	import { usePage } from '@inertiajs/vue3';
	import CityHeader from '~/components/CityHeader.vue';

	defineProps({
		title: { type: String, default: '' },
	});

	const page = usePage();
</script>
