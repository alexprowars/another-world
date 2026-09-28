<template>
	<div class="online-user">
		<button type="button" class="online-user-action" @click="emit('private', user.name)" :title="'Написать приватно: ' + user.name">
			<img :src="user.battle ? '/assets/images/chat/private_b.png' : '/assets/images/chat/private.png'" alt="Приватно" />
		</button>
		<img v-if="user.tribe" :src="'/assets/images/tribe/' + user.tribe.id + '.gif'" :title="'Клан: ' + user.tribe.name" :alt="user.tribe.name" />
		<img v-if="user.rank" :src="'/assets/images/rank/' + user.rank + '.png'" :title="$t('rank.' + user.rank)" :alt="$t('rank.' + user.rank)" />
		<button type="button" class="online-user-name" @click="emit('player', user.name)" :title="'Обратиться к ' + user.name">
			{{ user.name }}
		</button>
		<span class="online-user-level" title="Уровень">[{{ user.level }}]</span>
		<Link :href="'/info/' + user.id" target="_blank" class="online-user-action" :title="'Профиль: ' + user.name">
			<img src="/assets/images/images/inf.png" alt="Профиль" />
		</Link>
		<div class="online-user-statuses">
			<img v-if="user.profession" :src="'/assets/images/guild/' + user.profession + '.png'" :title="$t('profession.' + user.profession)" :alt="$t('profession.' + user.profession)"/>
			<img v-if="user.silence" src="/assets/images/chat/molch.gif" :title="'Молчанка до ' + $formatDate(user.silence, 'DD MMM HH:mm:ss')" alt="Молчанка"/>
			<a v-if="user.battle" :href="'/view_logs.php?log=' + user.battle" target="_blank" class="online-user-action" title="В бою — открыть журнал">
				<img src="/assets/images/chat/noweapon.gif" alt="В бою" />
			</a>
			<img v-if="user.travma" src="/assets/images/chat/travma.gif" title="Травма" alt="Травма" />
		</div>
	</div>
</template>

<script setup>
	import { Link } from '@inertiajs/vue3';

	defineProps({
		user: Object
	});

	const emit = defineEmits(['player', 'private']);
</script>
