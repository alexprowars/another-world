<template>
	<ContentBlock title="Больница">
		<div class="w-full text-right">
			<Link href="/map"><img src='/assets/images/images/refresh.gif' alt='Обновить'></Link>
			<Link v-if="!user.r_date" href="/map/change/8"><img src='/assets/images/images/back.gif' alt='Вернуться'></Link>
		</div>

		<table border=0 cellspacing=0 cellpadding=5 width=100% bordercolor="silver">
			<tr>
				<td align=center>
					<div v-if="!user.r_date && page.time > 0">
						Вы можете подлечиться в нашей больнице.<br>
						Уровень жизни: <b><u>{{ user.hp_now }}</u></b> ед. из <b><u>{{ user.hp_max }}</u></b> ед.<br>
						Курс лечения займёт займёт: <b>{{ $formatTime(page.time) }}</b>
						<br><br>
						<Link href="" @click.prevent="healAction()" class="btn btn-primary">Подлечиться</Link>
					</div>
					<div v-else-if="!user.r_date && page.time === 0">
						<b>Извините, но у нас Вам делать нечего, Вы абсолютно здоровы!</b>
					</div>
					<div v-else-if="user.r_date && page.time > 0">
						<div class="mr-2">Ещё лечиться:</div>
						<Timer :value="user.r_date" class="font-bold" :callback="onTimeout"/>
					</div>
					<div v-if="user.injury">
						<br><br>Вы можете вылечить свои травмы у нас, конечно маленько дороже чем у лекарей.<br><br>
						<a href="" @click.prevent="injuryAction()" class="btn btn-primary">Вылечить травму за 200 зол.</a>
					</div>
				</td>
			</tr>
		</table>
	</ContentBlock>
</template>

<script setup>
	import ContentBlock from '~/components/ContentBlock.vue';
	import useState from '~/composables/useState.js';
	import { computed } from 'vue';
	import { Link, router } from '@inertiajs/vue3';
	import Timer from '~/components/Timer.vue';

	defineProps({
		page: Object,
	});

	const state = useState();
	const user = computed(() => state.user);

	function healAction() {
		router.post('', {
			heal: 'Y',
		});
	}

	function injuryAction() {
		router.post('', {
			injury: 'Y',
		});
	}

	function onTimeout() {
		router.reload();
	}
</script>