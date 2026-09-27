<template>
	<section>
		<h2 class="mb-3 font-bold">Личное дело персонажа</h2>
		<dl class="grid grid-cols-[auto_1fr] gap-x-4 gap-y-2">
			<dt>Последний IP</dt><dd>{{ info.ip }}</dd>
			<dt>E-mail</dt><dd>{{ info.email }}</dd>
			<dt>Опыт</dt><dd>{{ info.exp }}</dd>
			<dt>Золото</dt><dd>{{ info.gold }}</dd>
			<dt>Платина</dt><dd>{{ info.credits }}</dd>
			<dt>Свободные статы</dt><dd>{{ info.updates }}</dd>
			<dt>Совпадения IP</dt>
			<dd class="flex flex-wrap gap-2">
				<Link v-for="person in info.shared_ip_users" :key="person.id" :href="'/info/' + person.id" class="underline">{{ person.name }}</Link>
				<span v-if="!info.shared_ip_users.length">Не найдены</span>
			</dd>
		</dl>
	</section>
</template>

<script setup>
	import { Link } from '@inertiajs/vue3';

	defineProps({
		info: Object,
	});
</script>
