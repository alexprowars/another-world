<template>
	<ContentBlock title="Тренировочный зал для новичков">
		<div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
			<div class="flex max-w-150 flex-col gap-2 sm:flex-row sm:items-center">
				<div class="shrink-0 sm:py-1 sm:pr-1">
					<Name :player="user"/>
				</div>
				<div class="min-w-55 flex-1">
					<HpLine :current="user.hp_now" :max="user.hp_max" color="g_line"/>
				</div>
			</div>
			<div class="flex justify-end gap-1">
				<a href="/map" title="Обновить">
					<img src="/assets/images/images/refresh.gif" alt="Обновить">
				</a>
				<a v-if="user.room === 2" href="/map/change/2" title="Вернуться">
					<img src="/assets/images/images/back.gif" alt="Вернуться">
				</a>
			</div>
		</div>

		<div v-if="!page.players.length" class="mb-4 bg-red-100 p-2 text-red-700">
			Вам больше не нужно тренироваться
		</div>
		<div v-else class="grid gap-6 md:grid-cols-[minmax(0,2fr)_minmax(180px,1fr)]">
			<div class="space-y-2">
				<div class="font-bold">Как вести себя в поединке:</div>
				<p>
					<b>1.</b> Если у вас не стреляющие оружие (не лук) и соперник находится в нескольких ячеек от вас, чтобы подойти, вам необходимо нажать
					<u>правой кнопокй мыши</u> на ближайшую к себе ячейку, после чего выдет контекстное меню, жмите
					<b>идти</b>. . .и так пока не встретитесь с соперником.
				</p>
				<p>
					<b>2.</b> Подойдя к сопернику, чтобы нанести удар, нужно нажать кнопку
					<b>карта/бой</b>, в появившемся окне вы расставлете удары и блоки на веше усмотрение, после расстановки блоков и ударов, нужно нажать кнопку
					<b>ударить</b>
				</p>
				<p>
					<b>3.</b> Если у вас в руках лук, вы можете стрелять на расстоянии 3 ячеек. Чтобы стрельнуть, также нажимаете правой кнопкой мыши на соперника и жмете
					<b>стрельнуть</b>, Если соперник уже подошел к вам вплотную то переходим на кнопку
					<b>карта/бой</b> и уже сражаемся таким способом.
				</p>
				<p>
					<b>4.</b> Если же вы в бою против 2 соперников и они оба рядом стоят, можете бить их поочереди, путем нажатия кнопки
					<b>сменить</b>.
				</p>
			</div>
			<div class="text-center">
				<div class="font-bold">Тренировочные Боты</div>
				<hr class="my-2 border-gray-300">
				<div class="space-y-1">
					<div v-for="player in page.players" :key="player['id']">
						<a href="" @click.prevent="fightTo(player['id'])"><b>{{ player['name'] }}</b></a> [{{ player['level'] }}]
					</div>
				</div>
			</div>
		</div>
	</ContentBlock>
</template>

<script setup>
	import useState from '~/composables/useState.js';
	import { computed } from 'vue';
	import Name from '~/components/Person/Name.vue';
	import HpLine from '~/components/Person/HpLine.vue';
	import { router } from '@inertiajs/vue3';
	import ContentBlock from '~/components/ContentBlock.vue';

	defineProps({
		page: Object,
	});

	const state = useState();
	const user = computed(() => state.user);

	function fightTo(id) {
		router.post('', {
			fight: id,
		})
	}
</script>
