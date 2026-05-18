<template>
	<ContentBlock title="Парк">
		<table class='ltable'>
			<tr>
				<td style="vertical-align: top">
					<div style="text-align:justify; width:98%; padding: 0 10px 10px" class="small">
						Красивая, ровно постриженная и высокая трава, резные и весьма удобные деревянные лавочки, чистый воздух, тихая, приятная атмосфера - что может быть лучше для полноценного отдыха?
						Большинство посетителей парка приходят сюда для расслабления. Остальные же хотят развлечений - испытать свою удачу в казино или лотерее, а временами и выбрать подарок для близких и уважаемых людей.
						Это любимое место путешественников, ведь по выстеленным ровной брусчаткой парковым дорожкам, разбегающимся во все четыре стороны, можно уйти очень далеко…
					</div>
	
					<ul class="mapHints">
						<li><Link href='?room=103'>Торговая площадь</Link></li>
						<li><Link href='?room=10'>Башня магов</Link></li>
						<li><Link href='?room=16'>Центр занятости</Link></li>
						<li><Link href='?room=13'>Сувениры</Link></li>
						<li><Link href='?room=105'>Промышленная зона</Link></li>
					</ul>
				</td>
				<td>
					<div style="position:relative;width: 550px;">
						<img :src="'/assets/images/world/city/' + page.city + '/2_bg.jpg'" alt="" width="550" height="300"/>
	
						<div style="position:absolute; left:37px; top:29px; width:187px; height:193px;">
							<img :src="'/assets/images/world/city/' + page.city + '/2_LV.gif'" v-tooltip="'Игорный дом'" class="tooltip text">
						</div>
						<div style="position:absolute; left:246px; top:174px; width:91px; height:69px;">
							<Link href="?room=16"><img :src="'/assets/images/world/city/' + page.city + '/2_Gift.gif'" v-tooltip="'Центр занятости'" class="tooltip text aFilter"></Link>
						</div>
						<div style="position:absolute; left:348px; top:204px; width:71px; height:49px;">
							<Link href='?room=13'><img :src="'/assets/images/world/city/' + page.city + '/2_Loto.gif'" v-tooltip="'Сувениры'" class="tooltip text aFilter"></Link>
						</div>
						<div style="position:absolute; left:28px; top:197px; width:96px; height:100px;">
							<img :src="'/assets/images/world/city/' + page.city + '/2_Tavern.gif'" v-tooltip="'Таверна'" class="tooltip text">
						</div>
						<div style="position:absolute; left:7px; top:230px; width:25px; height:24px;">
							<Link href="?room=103"><img :src="'/assets/images/world/city/' + page.city + '/2_Left.gif'" v-tooltip="'Торговая площадь'" class="tooltip text aFilter"></Link>
						</div>
						<div style="position:absolute; left:502px; top:265px; width:29px; height:26px;">
							<Link href="?room=105"><img :src="'/assets/images/world/city/' + page.city + '/2_Right.gif'" v-tooltip="'Промышленная зона'" class="tooltip text aFilter"></Link>
						</div>
						<div style="position:absolute; left:118px; top:273px; width:19px; height:20px;">
							<img :src="'/assets/images/world/city/' + page.city + '/2_Bottom.gif'" v-tooltip="'Проход закрыт'" class="tooltip text aFilter">
						</div>
						<div style="position:absolute; left:283px; top:20px; width:32px; height:101px;">
							<Link href="?room=10"><img :src="'/assets/images/world/city/' + page.city + '/2_Mage.gif'" v-tooltip="'Башня Магов'" class="tooltip text aFilter"></Link>
						</div>
					</div>
				</td>
			</tr>
		</table>
	</ContentBlock>
</template>

<script setup>
	import { Link } from '@inertiajs/vue3';
	import ContentBlock from '~/components/ContentBlock.vue';

	defineProps({
		page: Object,
	})
</script>