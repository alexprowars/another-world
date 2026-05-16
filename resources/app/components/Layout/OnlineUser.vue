<template>
	<div class="people">
		<a href="" @click.prevent="toPrivate" title="Приватно">
			<img v-if="user['battle']" src="/assets/images/chat/private_b.gif" width="22" height="15">
			<img src="/assets/images/chat/private.gif" width="22" height="15">
		</a>

		<a v-if="user['tribe']" href="" @click.prevent="toTribe">
			<img :src="'/assets/images/tribe/' + pl['tribe'] + '.gif'" width="24" height="15" :title="'Клан ' + user['tribe']">
		</a>

		<img v-if="user['rank']" :src="'/assets/images/rank/' + user['rank'] + '.gif'" :title="$t('rank.' + user['rank'])" width="12" height="15">

		<a href="" @click.prevent="toPlayer" title="Послать сообщение">{{ user['name'] }}</a>
		<b>[{{ user['level'] }}]</b>
		<Link :href="'/info/' + user['id']" target="main" title="Посмотреть инфу" class="chat">
			<img src="/assets/images/images/inf.gif" title="Посмотреть инфу" align="absmiddle">
		</Link>

		<div class="pull-xs-right">
			<img v-if="user['profession']" :src="'/assets/images/guild/' + user['profession'] + '.gif'" :title="$t('proffession.' + user['profession'])">
			<img v-if="user['silence']" src="/assets/images/chat/molch.gif" :title="'Молчанка до ' + $formatDate(user['silence'], 'DD MMM HH:mm:ss')" width="15" height="12">
			<Link v-if="user['battle']" :href="'/view_logs.php?log=' + user['battle']" target="main">
				<img src="/assets/images/chat/noweapon.gif" alt="В бою" height="12" width="15">
			</Link>
			<img v-if="user['travma']" src="/assets/images/chat/travma.gif" title="Травма">
		</div>
	</div>
</template>

<script setup>
	import { Link } from '@inertiajs/vue3';

	defineProps({
		user: Object,
	});

	function toPlayer() {
		//
	}

	function toPrivate() {
		//
	}

	function toTribe() {
		//
	}
</script>