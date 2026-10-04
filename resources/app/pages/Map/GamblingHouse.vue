<template>
	<ContentBlock title="Игорный дом">
		<template #actions>
			<MovementLink :to="location.exit" class="ui-icon-button" title="Вернуться в парк">
				<GameIcon name="back" />
			</MovementLink>
			<Link :href="location.url + '/' + page.game" class="ui-icon-button" title="Обновить">
				<GameIcon name="refresh" />
			</Link>
		</template>

		<header class="gambling-intro">
			<div class="gambling-suits"><span>♦</span><span>♣</span><span>♥</span><span>♠</span></div>
			<div class="gambling-intro-copy">
				<h2>Испытайте свою удачу</h2>
				<p>За резными дверями звенят монеты и стучат кубики. Займите место за столом Тени или купите билет городской лотереи.</p>
			</div>
		</header>

		<nav class="ui-tabs ui-tabs--stacked gambling-tabs">
			<Link :href="location.url + '/dice'" class="ui-tab" :class="{ 'is-active': page.game === 'dice' }">
				<GameIcon name="dice" />
				Кости
			</Link>
			<Link :href="location.url + '/lottery'" class="ui-tab" :class="{ 'is-active': page.game === 'lottery' }">
				<GameIcon name="ticket" />
				Лотерея
			</Link>
		</nav>

		<Dice v-if="page.game === 'dice'" :stakes="page.stakes" :result="page.dice" />
		<Lottery v-else :lottery="page.lottery" />

		<template #footer>
			<GameIcon name="coins" />
			<span>Все ставки и выигрыши — в игровом золоте. Платина в играх не используется.</span>
		</template>
	</ContentBlock>
</template>

<script setup>
	import MovementLink from '~/components/City/MovementLink.vue';
	import useLocation from '~/composables/useLocation.js';

	import { Link } from '@inertiajs/vue3';
	import ContentBlock from '~/components/ContentBlock.vue';
	import GameIcon from '~/components/Layout/GameIcon.vue';
	import Dice from '~/components/GamblingHouse/Dice.vue';
	import Lottery from '~/components/GamblingHouse/Lottery.vue';

	const location = useLocation();

	defineProps({
		page: Object,
	});
</script>
