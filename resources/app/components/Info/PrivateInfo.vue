<template>
	<section class="person-info-panel person-info-private ui-panel">
		<h2 class="person-info-panel-heading"><GameIcon name="justice" />Личное дело персонажа</h2>
		<dl class="person-info-data">
			<div>
				<dt>Последний IP</dt>
				<dd>{{ info.ip }}</dd>
			</div>
			<div>
				<dt>E-mail</dt>
				<dd>{{ info.email }}</dd>
			</div>
			<div>
				<dt>Опыт</dt>
				<dd>{{ info.exp }}</dd>
			</div>
			<div>
				<dt>Золото</dt>
				<dd>{{ info.gold }}</dd>
			</div>
			<div>
				<dt>Платина</dt>
				<dd>{{ info.credits }}</dd>
			</div>
			<div>
				<dt>Свободные статы</dt>
				<dd>{{ info.updates }}</dd>
			</div>
			<div>
				<dt>Совпадения IP</dt>
				<dd class="person-info-links">
					<Link v-for="person in info.shared_ip_users" :key="person.id" :href="'/info/' + person.id">{{ person.name }}</Link>
					<span v-if="!info.shared_ip_users.length" class="person-info-muted">Не найдены</span>
				</dd>
			</div>
		</dl>
	</section>
</template>

<script setup>
	import { Link } from '@inertiajs/vue3';
	import GameIcon from '~/components/Layout/GameIcon.vue';

	defineProps({
		info: Object,
	});
</script>
