<template>
	<ContentBlock title="Банк">
		<template #actions>
			<Link href="/map/change/17" class="ui-icon-button" title="Назад">
				<GameIcon name="back" />
			</Link>
			<Link :href="'/map?section=' + page.section" class="ui-icon-button" title="Обновить">
				<GameIcon name="refresh" />
			</Link>
		</template>

		<p v-if="page.message" class="ui-notice ui-notice--blue" role="status" v-html="page.message"></p>
		<nav class="ui-tabs ui-tabs--stacked">
			<Link class="ui-tab" href="/map?section=1" :class="{ 'is-active': page.section === 1 }">
				<GameIcon name="gift" />
				Пожертвования
			</Link>
			<Link class="ui-tab" href="/map?section=2" :class="{ 'is-active': page.section === 2 }">
				<GameIcon name="transfer" />
				Обмен валюты
			</Link>
		</nav>
		<Donations v-if="page.section === 1" :donations="page.donations" />
		<Exchange v-else :exchange-rate="page.exchange_rate" />
	</ContentBlock>
</template>

<script setup>
	import { Link } from '@inertiajs/vue3';
	import ContentBlock from '~/components/ContentBlock.vue';
	import GameIcon from '~/components/Layout/GameIcon.vue';
	import Donations from '~/components/Bank/Donations.vue';
	import Exchange from '~/components/Bank/Exchange.vue';

	defineProps({
		page: Object
	});
</script>
