<template>
	<header class="game-header">
		<Link href="/person" class="game-player" title="Открыть персонажа">
			<span class="game-player-identity">
				<img class="game-player-avatar" :src="user.avatar" alt="" width="36" height="36" />
				<strong class="game-player-name">{{ user.name }}</strong>
				<span class="game-player-level">[{{ user.level }}]</span>
			</span>
			<div class="game-player-resources">
				<HpLine :current="user.hp_now" :max="user.hp_max" :regeneration="user.hp_regeneration" color="g_line" title="Здоровье">
					<template #icon><HealthIcon class="resource-icon" /></template>
				</HpLine>
				<HpLine :current="user.energy_now" :max="user.energy_max" color="b_line" title="Мана">
					<template #icon><ManaIcon class="resource-icon" /></template>
				</HpLine>
				<HpLine :current="user.stamina_now" :max="user.stamina_max" color="h_line" title="Силы">
					<template #icon><StaminaIcon class="resource-icon" /></template>
				</HpLine>
			</div>
		</Link>
		<nav class="game-navigation">
			<Link v-for="item in navigation" :key="item.href" :href="item.href" class="game-nav-link" :class="{ 'is-active': isActive(item.href) }">
				<GameIcon :name="item.icon" />
				<span>{{ item.label }}</span>
			</Link>
		</nav>
		<div class="game-header-actions">
			<div class="game-wallet">
				<span title="Золото">
					<img src="/assets/images/currencies/gold.png" alt="" />
					<b>{{ user.gold }}</b>
					<small>зол.</small>
				</span>
				<Link href="/pay" title="Платина">
					<img src="/assets/images/currencies/platinum.png" alt="" />
					<b>{{ user.credits }}</b>
					<small>пл.</small>
				</Link>
			</div>
			<button type="button" class="ui-button ui-button--compact game-logout" title="Выйти из игры" :disabled="logoutForm.processing" @click="logout">
				<GameIcon name="logout" />
				<span>Выход</span>
			</button>
		</div>
	</header>
</template>

<script setup>
	import { Link, useForm, usePage } from '@inertiajs/vue3';
	import { computed } from 'vue';
	import useState from '~/composables/useState.js';
	import HpLine from '~/components/Person/HpLine.vue';
	import GameIcon from './GameIcon.vue';
	import HealthIcon from '~/icons/resources/health.svg';
	import ManaIcon from '~/icons/resources/mana.svg';
	import StaminaIcon from '~/icons/resources/stamina.svg';

	const state = useState();
	const user = computed(() => state.user);
	const page = usePage();

	const logoutForm = useForm({});
	const navigation = [
		{ href: '/map', label: 'Город', icon: 'city' },
		{ href: '/person', label: 'Персонаж', icon: 'character' },
		{ href: '/arena', label: 'Поединки', icon: 'swords' },
		{ href: '/person/work', label: 'Заработок', icon: 'coins' },
		{ href: '/library', label: 'Библиотека', icon: 'book' },
	];

	function logout() {
		if (logoutForm.processing) {
			return;
		}

		logoutForm.post('/logout');
	}

	function isActive(href) {
		const path = page.url.split('?')[0];

		if (href === '/person' && path === '/person/work') {
			return false;
		}

		return path === href || path.startsWith(href + '/');
	}
</script>
