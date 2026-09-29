<template>
	<Head title="Комплекты экипировки" />
	<section class="person-inventory">
		<header class="person-page-heading">
			<div>
				<h1>Комплекты экипировки</h1>
				<p>Сохраняйте сочетания вещей и меняйте экипировку одним действием.</p>
			</div>
			<Link href="/person/inventory?unset=all" class="ui-button ui-button--compact ui-button--secondary" preserve-scroll>Снять всё</Link>
		</header>
		<InventoryNavigation active="sets" />
		<Sets :sets="page.sets" />
	</section>
</template>

<script setup>
	import { Head, Link } from '@inertiajs/vue3';
	import GameLayout from '~/layouts/Game.vue';
	import PersonLayout from '~/layouts/Person.vue';
	import InventoryNavigation from '~/components/Person/InventoryNavigation.vue';
	import Sets from '~/components/Person/Sets.vue';

	defineOptions({
		layout: [GameLayout, PersonLayout]
	});

	defineProps({
		page: Object
	});
</script>
