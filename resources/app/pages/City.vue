<template>
	<div class="textblock">
		<table width=100%>
			<tr>
				<td colspan="2" class="text-center">
					<u>Для поединка зайдите внутрь арены</u><br><br>
				</td>
			</tr>
			<tr>
				<td valign=center colspan=2 align=center>
					<Link href="/map?room=23" class="standbut">Уйти с арены</Link>
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
					<Link href="/map?room=1" class="standbut">Вход</Link>
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
					<Link href="/map?room=2" class="standbut">Вход</Link>
				</td>
			</tr>
			<tr>
				<td align=center COLSPAN=2>
					<b>Больница</b><br>
					<small>[портал в больницу]</small>
					<br>
					<Link href="/map?room=8" class="standbut">Вход</Link>
				</td>
			</tr>
		</table>
	</div>
</template>

<script setup>
	import GameLayout from '~/layouts/Game.vue';
	import PersonLayout from '~/layouts/Person.vue';
	import { Link } from '@inertiajs/vue3';

	defineOptions({
		layout: [GameLayout, PersonLayout]
	});

	defineProps({
		page: Object,
	})
</script>