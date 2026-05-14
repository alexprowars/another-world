<template>
	<CityHeader title="Королевская Улица"/>
	<div class="textblock">
		<table class='ltable'>
			<tr>
				<td style="vertical-align: top">
					<div style="text-align:justify; width:98%; padding: 0 10px 10px;" class="small">
						Атмосфера королевской улицы насквозь пропитана запахами величия и важных дел.
						Из Храма, прерываясь на время свадебными словами священника, звучат радостные песни, восторженные голоса и звон бокалов.
						Из-за высоких и основательных стен Городской управы то и дело доносятся отголоски политических споров, которые порой накаляются до такой степени, что о делах государственной важности слышит вся округа.
						Аукцион объявляет о себе голосами, выкрикивающими цены. Торжество и Дело, как бы ни были они противоположны, заполняют собой Королевская улица - пожалуй, одно из самых торжественных и величественных мест в городе.
					</div>

					<ul class="mapHints">
						<li><a href='?room=14'>Замок святой инквизиции</a></li>
						<li><a href='?room=29'>Бутик</a></li>
						<li><a href='/?room=8'>Больница</a></li>
						<li><a href='?room=103'>Торговая площадь</a></li>
					</ul>
				</td>
				<td>
					<div style="position:relative;width: 550px;">
						<img :src="'/assets/images/world/city/' + page.city + '/4_bg.jpg'" alt="" width="550" height="300"/>

						<div style="position:absolute; left:58px; top:36px; width:134px; height:170px;">
							<a href="?room=14"><img :src="'/assets/images/world/city/' + page.city + '/4_castle.gif'" v-tooltip="'Замок святой инквизиции'" class="tooltip text aFilter"></a>
						</div>
						<div style="position:absolute; left:221px; top:134px; width:63px; height:75px;">
							<a href="?room=8"><img :src="'/assets/images/world/city/' + page.city + '/4_shop.gif'" v-tooltip="'Больница'" class="tooltip text aFilter"></a>
						</div>
						<div style="position:absolute; left:289px; top:134px; width:59px; height:78px;">
							<a href="?room=29"><img :src="'/assets/images/world/city/' + page.city + '/4_trade.gif'" v-tooltip="'Бутик'" class="tooltip text aFilter"></a>
						</div>
						<div style="position:absolute; left:61px; top:192px; width:70px; height:77px;">
							<img :src="'/assets/images/world/city/' + page.city + '/4_bkm.gif'" v-tooltip="'Домик знахаря'" class="tooltip text">
						</div>
						<div style="position:absolute; left:358px; top:4px; width:139px; height:206px;">
							<img :src="'/assets/images/world/city/' + page.city + '/4_church.gif'" v-tooltip="'Храм'" class="tooltip text">
						</div>
						<div style="position:absolute; left:24px; top:205px; width:26px; height:24px;">
							<img :src="'/assets/images/world/city/' + page.city + '/4_left.gif'" v-tooltip="'Проход закрыт'" class="tooltip text">
						</div>
						<div style="position:absolute; left:493px; top:215px; width:27px; height:24px;">
							<a href="?room=103"><img :src="'/assets/images/world/city/' + page.city + '/4_right.gif'" v-tooltip="'Торговая площадь'" class="tooltip text aFilter"></a>
						</div>
						<div style="position:absolute; left:145px; top:234px; width:17px; height:19px;">
							<img :src="'/assets/images/world/city/' + page.city + '/4_bottom.gif'" v-tooltip="'Проход закрыт'" class="tooltip text"/>
						</div>
					</div>
				</td>
			</tr>
		</table>
	</div>
</template>

<script setup>
	import CityHeader from '~/components/CityHeader.vue';

	defineProps({
		page: Object,
	})
</script>