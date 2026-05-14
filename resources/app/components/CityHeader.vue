<template>
	<table width="100%">
		<tr>
			<td><img src='/assets/images/main/lp.jpg' width='40' height='28' alt=''></td>
			<td class='nmenus' style='white-space: nowrap; width:50%'>
				<div style="width: 250px">
					У вас с собой денег:
					<font color='#EDE2BE'>{{ user.moneys }} зол.</font>
					|
					<font color='#EDE2BE'>{{ user.credits }} пл.</font>
				</div>
			</td>
			<td align='center' valign='top' class='nmenus'>
				<table>
					<tr>
						<td><img src='/assets/images/main/lpl.jpg' width='40' height='28' alt=''></td>
						<td class='l_z_f'>{{ title }}</td>
						<td><img src='/assets/images/main/rpr.jpg' width='40' height='28' alt=''></td>
					</tr>
				</table>
			</td>
			<td class='nmenus' style='white-space: nowrap; width:50%; text-align:right;'><div style="width: 250px"></div></td>
			<td><img src='/assets/images/main/rp.jpg' width='40' height='28' alt=''></td>
		</tr>
	</table>
</template>

<script setup>
	import useState from '~/composables/useState.js';
	import { computed } from 'vue';

	defineProps({
		title: {
			type: String,
			default: '',
		}
	});

	const state = useState();
	const user = computed(() => state.user);
</script>