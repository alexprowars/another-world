<template>
	<CityHeader title="Торговая Площадь"/>
	<div class="textblock">
		<table class='ltable'>
			<tr>
				<td style="vertical-align: top">
					<div style="text-align:justify; width:98%; padding: 0 10px 10px;" class="small">
						На Торговой площади никогда не бывает пусто. Идут, идут сплошным потоком люди.
						Двадцать пять миллионов постоянного населения - пожалуй, самый насыщенный мегаполис в мире.
						Люди общаются, внимают речам ораторов, продают или покупают вещи. Город живет своей жизнью.
						Желаете посетить магазин, открыть счет в банке или обратиться к опытному кузнецу?
					</div>

					<ul class="mapHints">
						<li><a href='?room=101'>Королевская улица</a></li>
						<li><a href='?room=23'>Арена</a></li>
						<li><a href='?room=17'>Банк</a></li>
						<li><a href='?room=7'>Магазин</a></li>
						<li><a href='?room=11'>Кузница</a></li>
						<li><a href='?room=20'>Рынок</a></li>
						<li><a href='?room=104'>Парк</a></li>
					</ul>
				</td>
				<td>
					<div style="position:relative;width: 550px;">
						<img :src="'/assets/images/world/city/' + page.city + '/1_bg.jpg'" alt="" width="550" height="300"/>

						<div style="position:absolute; left:39px; top:201px; width:70px; height:55px;">
							<a href="?room=20"><img :src="'/assets/images/world/city/' + page.city + '/1_market.gif'" v-tooltip="'Рынок'" class="tooltip text aFilter"></a>
						</div>
						<div style="position:absolute; left:311px; top:87px; width:117px; height:117px;">
							<a href="?room=17"><img :src="'/assets/images/world/city/' + page.city + '/1_Bank.gif'" v-tooltip="'Банк'" class="tooltip text aFilter"></a>
						</div>
						<div style="position:absolute; left:452px; top:141px; width:93px; height:77px;">
							<a href="?room=7"><img :src="'/assets/images/world/city/' + page.city + '/1_Store.gif'" v-tooltip="'Магазин'" class="tooltip text aFilter"></a>
						</div>
						<div style="position:absolute; left:17px; top:26px; width:278px; height:170px;">
							<a href="?room=23"><img :src="'/assets/images/world/city/' + page.city + '/1_Arena.gif'" v-tooltip="'Арена'" class="tooltip text aFilter"></a>
						</div>
						<div style="position:absolute; left:421px; top:237px; width:85px; height:59px;">
							<img :src="'/assets/images/world/city/' + page.city + '/1_Steads.gif'" v-tooltip="'Почта'" class="tooltip text">
						</div>
						<div style="position:absolute; left:249px; top:210px; width:149px; height:80px;">
							<a href="?room=11"><img :src="'/assets/images/world/city/' + page.city + '/1_Smith.gif'" v-tooltip="'Кузница'" class="tooltip text aFilter"></a>
						</div>
						<div style="position:absolute; left:6px; top:195px; width:23px; height:32px;">
							<a href="?room=101"><img :src="'/assets/images/world/city/' + page.city + '/1_Left.gif'" v-tooltip="'Королевская улица'" class="tooltip text aFilter"></a>
						</div>
						<div style="position:absolute; left:520px; top:231px; width:27px; height:32px;">
							<a href="?room=104"><img :src="'/assets/images/world/city/' + page.city + '/1_Right.gif'" v-tooltip="'Парк'" class="tooltip text aFilter"></a>
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