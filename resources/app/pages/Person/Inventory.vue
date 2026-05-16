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
									<Link :href="'/person/inventory?item_type=' + id" :class="{ disabled: false }" class="smenu">{{ title }}</Link>
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
					<div class="button"><a href="/person/inventory?item_type=9" class="tm">комплекты</a></div>
					<div class="button"><a href="/person/inventory?unset=all" class="tm">снять все</a></div>
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

		<div v-if="page.items.length" class="flex flex-col gap-2">
			<InventoryItem v-for="item in page.items" :key="item.id" :item="item" :player="user" @wear="wearItem"/>
		</div>
		<div v-else class="text-xs-center">
			<div class="alert alert-info" role="alert">Отдел рюкзака пуст.</div>
		</div>
	</div>
</template>

<script setup>
	import GameLayout from '~/layouts/Game.vue';
	import PersonLayout from '~/layouts/Person.vue';
	import { Link, router } from '@inertiajs/vue3';
	import InventoryItem from '~/components/Person/InventoryItem.vue';
	import useState from '~/composables/useState.js';
	import { computed } from 'vue';

	defineOptions({
		layout: [GameLayout, PersonLayout]
	});

	defineProps({
		page: Object,
	});

	const state = useState();
	const user = computed(() => state.user);

	function wearItem(item) {
		router.get('', {
			onset: item.id
		})
	}
</script>