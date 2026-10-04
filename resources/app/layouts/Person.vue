<template>
	<Head title="Персонаж" />
	<div class="person-layout">
		<aside class="person-equipment-panel">
			<PersonView :person="user" can-use @use="useItem" />
			<Link href="/person/inventory" class="person-equipment-link">Управление экипировкой</Link>
		</aside>
		<aside class="person-parameters-panel">
			<Parameters :player="user" />
		</aside>
		<section class="person-workspace">
			<nav class="ui-tabs ui-tabs--compact person-navigation">
				<Link
					v-for="item in navigation"
					:key="item.href"
					:href="item.href"
					class="ui-tab"
					:class="{
						'is-active': currentPath === item.href || (item.href !== '/person' && currentPath.startsWith(item.href + '/')),
					}"
				>
					{{ item.label }}
					<span v-if="item.href === '/person/updates' && user.updates" class="person-nav-count">{{ user.updates }}</span>
				</Link>
			</nav>
			<div v-if="page.flash?.message" class="ui-notice ui-notice--blue ui-notice--compact" role="status">{{ page.flash.message }}</div>
			<div class="person-content"><slot /></div>
		</section>
	</div>
</template>

<script setup>
	import { Head, Link, router, usePage } from '@inertiajs/vue3';
	import { computed } from 'vue';
	import PersonView from '~/components/PersonView.vue';
	import Parameters from '~/components/Person/Parameters.vue';
	import useState from '~/composables/useState.js';
	import { openPopupModal } from '~/composables/useModals.js';
	import UseMagic from '~/components/Dialogs/UseMagic.vue';

	const page = usePage();
	const state = useState();
	const user = computed(() => state.user);
	const currentPath = computed(() => page.url.split('?')[0]);
	const navigation = [
		{ href: '/person', label: 'Обзор' },
		{ href: '/person/inventory', label: 'Рюкзак' },
		{ href: '/person/updates', label: 'Умения' },
		{ href: '/person/abilities', label: 'Приёмы' },
		{ href: '/person/friends', label: 'Друзья' },
		{ href: '/person/settings', label: 'Настройки' },
	];

	function useItem(item) {
		openPopupModal(UseMagic, {
			title: 'Использовать магию',
			item,
			selfName: user.value.name,
			onUsed: () => router.reload(),
		});
	}
</script>
