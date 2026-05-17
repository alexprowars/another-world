<template>
	<CityHeader title="Прихожая"/>
	<div class="textblock">
		<table class='ltable'>
			<tr>
				<td style="vertical-align: top">
					<ul class="mapHints">
						<li><Link href='/map/city/street/4?room=1'>Вход на арену</Link></li>
						<li><Link href='/map/city/street/4?room=9'>Академия</Link></li>
						<li><Link href='/map/city/street/4?room=103'>Выход в город</Link></li>
					</ul>
				</td>
				<td align="center" valign="middle" width="690">
					<div style="position:relative;width:690px;" class="mapImage">
						<img src="/assets/images/world/arena.jpg" alt="">

						<div style="position:absolute; left:320px; top:30px;">
							<Link href="/map/city/street/4?room=1"><img src="/assets/images/world/0.gif" width="141" height="185" v-tooltip="'Вход на арену'" class="aFilter"></Link>
						</div>
						<div style="position:absolute; left:0; top:90px;">
							<Link href="/map/city/street/4?room=103"><img src=/assets/images/world/0.gif width="60" height="165" v-tooltip="'Выход в город'" class="aFilter"></Link>
						</div>
						<div style="position:absolute; left:100px; top:75px;">
							<Link href="/map/city/street/4?room=9"><img src=/assets/images/world/0.gif width="70" height="165" v-tooltip="'Академия'" class="aFilter"></Link>
						</div>
						<div style="position:absolute; left:610px; top:80px;">
							<img src=/assets/images/world/0.gif width="70" height="165" alt='Склад' class="aFilter">
						</div>
					</div>
				</td>
			</tr>
		</table>
	</div>
</template>

<script setup>
	import CityHeader from '~/components/CityHeader.vue';
	import { Link } from '@inertiajs/vue3';
</script>