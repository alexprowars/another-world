<template>
	<ContentBlock title="Банк">
		<template #actions>
			<MovementLink :to="location.exit" class="ui-icon-button" title="Назад">
				<GameIcon name="back" />
			</MovementLink>
			<Link :href="location.url + '?section=' + page.section" class="ui-icon-button" title="Обновить">
				<GameIcon name="refresh" />
			</Link>
		</template>

		<p v-if="page.message" class="ui-notice ui-notice--blue" role="status" v-html="page.message"></p>
		<nav class="ui-tabs ui-tabs--stacked">
			<Link class="ui-tab" :href="location.url + '?section=1'" :class="{ 'is-active': page.section === 1 }">
				<GameIcon name="gift" />
				Пожертвования
			</Link>
			<Link class="ui-tab" :href="location.url + '?section=2'" :class="{ 'is-active': page.section === 2 }">
				<GameIcon name="transfer" />
				Обмен валюты
			</Link>
		</nav>
		<Donations v-if="page.section === 1" :donations="page.donations" />
		<Exchange v-else :exchange-rate="page.exchange_rate" />
	</ContentBlock>
</template>

<script setup>
	import MovementLink from '~/components/City/MovementLink.vue';
	import useLocation from '~/composables/useLocation.js';

	import { Link } from '@inertiajs/vue3';
	import ContentBlock from '~/components/ContentBlock.vue';
	import GameIcon from '~/components/Layout/GameIcon.vue';
	import Donations from '~/components/Bank/Donations.vue';
	import Exchange from '~/components/Bank/Exchange.vue';

	const location = useLocation();

	defineProps({
		page: Object
	});
</script>
