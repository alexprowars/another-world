<template>
	<ContentBlock title="Промышленная Улица">
		<table class='ltable'>
			<tr>
				<td style="vertical-align: top">
					<div style="text-align:justify; width:98%; padding: 0 10px 10px;" class="small">
						С одной стороны промышленной зоны располагаются уходящие вверх и упирающиеся в облака чёрные горы, в которых расположены милые любому добытчику ресурсов и искателю приключений шахты.
						Весьма трудно праздно шататься в этом месте - повсюду кипит работа: вываливают из массивных металлических тележек добытую руду, свалены в штабеля деревянные балки, готовые в любой момент поддержать опасно нависшие над головой своды шахт, невысокими горками высится уголь - топливо всего и вся.
						За всеми делами наблюдают из окон тюрьмы любопытные заключённые, наказанные за самые разные виды прегрешений перед законами.
						Промышленная зона - оплот забот трудовых, мысли об отдыхе здесь неуместны.
					</div>

					<ul class="mapHints">
						<li><Link href='?room=104'>Парк</Link></li>
						<li><Link href='?room=200'>Шахта</Link></li>
						<li><Link href='?room=666'>Тюрьма</Link></li>
					</ul>
				</td>
				<td>
					<div style="position:relative;width: 550px;">
						<img :src="'/assets/images/world/city/' + page.city + '/3_bg.jpg'" alt="" width="550" height="300"/>

						<div style="position:absolute; left:340px; top:108px; width:130px; height:105px;">
							<Link href="?room=666"><img :src="'/assets/images/world/city/' + page.city + '/3_Prison.gif'" v-tooltip="'Тюрьма'" class="tooltip text aFilter"></Link>
						</div>
						<div style="position:absolute; left:72px; top:191px; width:24px; height:23px;">
							<Link href="?room=104"><img :src="'/assets/images/world/city/' + page.city + '/3_Left.gif'" v-tooltip="'Парк'" class="tooltip text aFilter"></Link>
						</div>
						<div style="position:absolute; left:14px; top:134px; width:54px; height:74px;">
							<img :src="'/assets/images/world/city/' + page.city + '/3_Tower.gif'" v-tooltip="'Проход закрыт'" class="tooltip text">
						</div>
						<div style="position:absolute; left:195px; top:206px; width:102px; height:79px;">
							<img :src="'/assets/images/world/city/' + page.city + '/3_Storage.gif'" v-tooltip="'Склад'" class="tooltip text">
						</div>
						<div style="position:absolute; left:504px; top:219px; width:26px; height:25px;">
							<img :src="'/assets/images/world/city/' + page.city + '/3_Right.gif'" v-tooltip="'Проход закрыт'" class="tooltip text">
						</div>
						<div style="position:absolute; left:247px; top:135px; width:30px; height:26px;">
							<Link href="?room=200"><img :src="'/assets/images/world/city/' + page.city + '/3_Mine.gif'" v-tooltip="'Шахта'" class="tooltip text aFilter"></Link>
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