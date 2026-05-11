<template>
	<div>
		<table class="table np">
			<tr>
				<PersonView :person="page.person"/>
				<td valign="top">
					<div v-if="page.message">
						<center><span style="color:red"><b v-html="page.message"></b></span></center><br>
					</div>
					<slot/>
				</td>
			</tr>
		</table>
	</div>
</template>

<script setup>
	import { usePage } from '@inertiajs/vue3';
	import PersonView from '../components/PersonView.vue';

	const props = defineProps({
		page: Object,
	});

	console.log(props.page)
</script>