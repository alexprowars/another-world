<template>
	<div class="personMenu">
		<table>
			<tr>
				<td valign="top" class="tm_p"><a href="/person/edit" class="tm">Рюкзак</a></td>
				<td class="delem"></td>
				<td valign="top" class="tm_p"><a href="/person/settings" class="tm">Настройки</a></td>
				<td class="delem"></td>
				<td valign="top" class="tm_p"><a href="/person/updates" class="tm">Умения</a></td>
				<td class="delem"></td>
				<td valign="top" class="tm_p"><a href="/person/priem" class="tm">Приёмы</a></td>
				<td class="delem"></td>
				<td valign="top" class="tm_p"><a href="/person/friends" class="tm">Друзья</a></td>
				<td class="delem"></td>
				<td valign="top" class="tm_p"><a href="/person/anketa" class="tm">Анкета</a></td>
			</tr>
		</table>
	</div>
</template>

<script setup>
	import AppLayout from '~/layouts/AppLayout.vue';
	import PersonLayout from '~/layouts/PersonLayout.vue';

	defineOptions({
		layout: [AppLayout, PersonLayout]
	});
</script>