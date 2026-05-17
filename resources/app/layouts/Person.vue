<template>
	<Head title="Персонаж"/>
	<table class="table np mx-2 w-full">
		<tr>
			<td width="245" valign=top>
				<PersonView :person="user"/>
			</td>
			<td width="230" valign="top" style="padding:0 15px;">
				<Parameters :player="user"/>
			</td>
			<td valign="top">
				<div v-if="page.message" class="text-center">
					<span style="color:red"><b v-html="page.message"></b></span>
				</div>
				<div class="personMenu">
					<table>
						<tbody>
						<tr>
							<td valign="top" class="tm_p"><Link href="/person/inventory" class="tm">Рюкзак</Link></td>
							<td class="delem"></td>
							<td valign="top" class="tm_p"><Link href="/person/settings" class="tm">Настройки</Link></td>
							<td class="delem"></td>
							<td valign="top" class="tm_p"><Link href="/person/updates" class="tm">Умения</Link></td>
							<td class="delem"></td>
							<td valign="top" class="tm_p"><Link href="/person/abilities" class="tm">Приёмы</Link></td>
							<td class="delem"></td>
							<td valign="top" class="tm_p"><Link href="/person/friends" class="tm">Друзья</Link></td>
							<td class="delem"></td>
							<td valign="top" class="tm_p"><Link href="/person/anketa" class="tm">Анкета</Link></td>
						</tr>
						</tbody>
					</table>
				</div>
				<slot/>
			</td>
		</tr>
	</table>
</template>

<script setup>
	import { Head, Link } from '@inertiajs/vue3';
	import PersonView from '../components/PersonView.vue';
	import useState from '~/composables/useState.js';
	import { computed } from 'vue';
	import Parameters from '~/components/Person/Parameters.vue';

	defineProps({
		page: Object,
	});

	const state = useState();
	const user = computed(() => state.user);
</script>