<template>
	<div class="textblock">
		<table  style="width:100%">
			<tr>
				<td><img src="/assets/images/main/ltmenu1.jpg" width="37" height="29" style="vertical-align: middle" alt=""></td>
				<td align="center" class="submenufon">
					<table>
						<tr>
							<template v-for="(title, id) in $tm('inventory')">
								<td v-if="id > 1" style="padding: 0 10px;">
									<img src="/assets/images/main/sm.jpg" width="8" height="29"  style="vertical-align: middle" alt="">
								</td>
								<td>
									<Link :href="'/person/inventory?item_type=' + id" :class="{ disabled: Number(id) === page.item_type }" class="smenu">{{ title }}</Link>
								</td>
							</template>
						</tr>
					</table>
				</td>
				<td><img src="/assets/images/main/rtmenu1.jpg" width="37" height="29" style="vertical-align: middle" alt="" ></td>
			</tr>
		</table>

		<table style="width:100%">
			<tr>
				<td style="width:50%">
					<div align="right" class="hline"></div>
				</td>
				<td nowrap>
					<div class="button"><Link href="/person/inventory?item_type=9" class="tm">комплекты</Link></div>
					<div class="button"><Link href="/person/inventory?unset=all" class="tm">снять все</Link></div>
				</td>
				<td style="width:50%">
					<div class="hline"></div>
				</td>
			</tr>
		</table>
		<br>

		<table width=100% cellspacing=0 cellpadding=0 border=0>
			<tr>
				<td id=menu align=center style='position: absolute; right: 50px'>&nbsp;</td>
			</tr>
		</table>

		<div v-if="Object.keys(dropForm.errors).length" class="mb-4 text-red-700" role="alert">
			<p v-for="(error, field) in dropForm.errors" :key="field">{{ error }}</p>
		</div>
		<Sets v-if="page.item_type === 9" :sets="page.sets"/>
		<div v-else-if="page.items.length" class="flex flex-col gap-2">
			<InventoryItem v-for="item in page.items" :key="item.id" :item="item" :player="user" :dropping="dropForm.processing" @wear="wearItem" @drop="dropItem"/>
		</div>
		<div v-else class="text-xs-center">
			<div class="alert alert-info" role="alert">Отдел рюкзака пуст.</div>
		</div>
	</div>
</template>

<script setup>
	import GameLayout from '~/layouts/Game.vue';
	import PersonLayout from '~/layouts/Person.vue';
	import { Link, router, useForm } from '@inertiajs/vue3';
	import InventoryItem from '~/components/Person/InventoryItem.vue';
	import Sets from '~/components/Person/Sets.vue';
	import useState from '~/composables/useState.js';
	import { computed } from 'vue';

	defineOptions({
		layout: [GameLayout, PersonLayout]
	});

	const props = defineProps({
		page: Object,
	});

	const state = useState();
	const user = computed(() => state.user);

	const dropForm = useForm({
		id: null,
		item_type: null
	});

	function dropItem(item) {
		if (dropForm.processing) {
			return;
		}

		dropForm.id = item.id;
		dropForm.item_type = props.page.item_type;
		dropForm.post('/person/inventory/drop', { preserveScroll: true });
	}

	function wearItem(item) {
		router.get('', {
			onset: item.id
		})
	}
</script>