<template>
	<CityHeader title="Тренировочный зал для новичков"/>
	<div class="textblock">
		<p v-if="page.message" class="message alert-danger">{{ message }}</p>
		<table width=100%>
			<tr>
				<td width=600 valign=top>
					<TABLE cellpadding=0>
						<tr>
							<TD valign=middle style="padding:5px 5px 5px 0;">
								<Name :player="user"/>
							</TD>
							<TD valign=middle>
								<HpLine :current="user.hp_now" :max="user.hp_max" color="g_line"/>
							</TD>
						</TR>
					</TABLE>
				</td>
				<td align=right valign=top>
					<a href="/map/"><img src='/assets/images/images/refresh.gif' alt='Обновить'></a>
					<a v-if="user.room == 2" href='/map/?refer=2'><img src='/assets/images/images/back.gif' alt='Вернуться'></a>
				</td>
			</tr>
		</table>
		<br>
		<div v-if="!page.players.length" class="alert alert-danger">Вам больше не нужно тренироваться</div>
		<div v-else class="row">
			<div class="col-xs-8">
				<b>Как вести себя в поединке.</b><br><br>
				<b>1.</b> Если у вас не стреляющие оружие (не лук) и соперник находится в нескольких ячеек от вас, чтобы подойти, вам необходимо нажать
				<u>правой кнопокй мыши</u> на ближайшую к себе ячейку, после чего выдет контекстное меню, жмите
				<b>идти</b>. . .и так пока не встретитесь с соперником.<br>
				<b>2.</b> Подойдя к сопернику, чтобы нанести удар, нужно нажать кнопку
				<b>карта/бой</b>, в появившемся окне вы расставлете удары и блоки на веше усмотрение, после расстановки блоков и ударов, нужно нажать кнопку
				<b>ударить</b><br>
				<b>3.</b> Если у вас в руках лук, вы можете стрелять на расстоянии 3 ячеек. Чтобы стрельнуть, также нажимаете правой кнопкой мыши на соперника и жмете
				<b>стрельнуть</b>, Если соперник уже подошел к вам вплотную то переходим на кнопку
				<b>карта/бой</b> и уже сражаемся таким способом.<br>
				<b>4.</b> Если же вы в бою против 2 соперников и они оба рядом стоят, можете бить их поочереди, путем нажатия кнопки
				<b>сменить</b>.
			</div>
			<div class="col-xs-4 text-xs-center">
				<b>Тренировочные Боты</b>
				<HR color="silver"/>
				<div v-for="player in page.players">
					<a href="" @click.prevent="fightTo(player['id'])"><b>{{ player['name'] }}</b></a> [{{ player['level'] }}]<br>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup>
	import CityHeader from '~/components/CityHeader.vue';
	import useState from '~/composables/useState.js';
	import { computed } from 'vue';
	import Name from '~/components/Person/Name.vue';
	import HpLine from '~/components/Person/HpLine.vue';
	import { router } from '@inertiajs/vue3';

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