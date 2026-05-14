<template>
	<div class="textblock">
		<table width=100%>
			<tr>
				<td colspan="2">
					<center><u>Для поединка зайдите внутрь арены</u></center><br>
				</td>
			</tr>
			<tr>
				<td valign=center colspan=2 align=center>
					<a href="/map?room=23" class="standbut">Уйти с арены</a>
				</td>
			</tr>
			<tr>
				<td align=center width=50%>
					<b>Боевая комната</b><br>
					<small>[Уровень: <u>0</u>-15]</small>
					<br>
					<template v-if="page.room_1_members">
						Воинов: {{ page.room_1_members }}
					</template>
					<template v-else>
						Пустует
					</template>
					<br>
					<a href="/map?room=1" class="standbut">Вход</a>
				</td>
				<td align=center>
					<b>Тренировочный зал</b><br>
					<small>[Уровень: <u>0</u>-15]</small>
					<br>
					<template v-if="page.room_2_members">
						Воинов: {{ page.room_2_members }}
					</template>
					<template v-else>
						Пустует
					</template>
					<br>
					<a href="/map?room=2" class="standbut">Вход</a>
				</td>
			</tr>
			<tr>
				<td align=center COLSPAN=2>
					<b>Больница</b><br>
					<small>[портал в больницу]</small>
					<br>
					<a href="/map?room=8" class="standbut">Вход</a>
				</td>
			</tr>
		</table>
	</div>
</template>

<script setup>
	import GameLayout from '~/layouts/Game.vue';
	import PersonLayout from '~/layouts/Person.vue';

	defineOptions({
		layout: [GameLayout, PersonLayout]
	});

	defineProps({
		page: Object,
	})
</script>