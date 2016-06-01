<? $this->view->partial('shared/city_header', Array('title' => 'Торговая Площадь')); ?>
<div class="textblock">
	<table class='ltable'>
		<tr>
			<td style="vertical-align: top">
				<div style="text-align:justify; width:98%; padding: 0 10px 10px;" class="small">На Торговой площади никогда не бывает пусто. Идут, идут сплошным потоком люди. Двадцать пять миллионов постоянного населения - пожалуй, самый насыщенный мегаполис в мире. Люди общаются, внимают речам ораторов, продают или покупают вещи. Город живет своей жизнью. Желаете посетить магазин, открыть счет в банке или обратиться к опытному кузнецу?</div>

				<ul class="mapHints">
					<li><a href='<?=$this->url->get('map/') ?>?room=101'>Королевская улица</a>
					<li><a href='<?=$this->url->get('map/') ?>?room=23'>Арена</a>
					<li><a href='<?=$this->url->get('map/') ?>?room=17'>Банк</a>
					<li><a href='<?=$this->url->get('map/') ?>?room=7'>Магазин</a>
					<li><a href='<?=$this->url->get('map/') ?>?room=11'>Кузница</a>
					<li><a href='<?=$this->url->get('map/') ?>?room=20'>Рынок</a>
					<li><a href='<?=$this->url->get('map/') ?>?room=104'>Парк</a>
				</ul>
			</td>
			<td>
				<div style="position:relative;width: 550px;">
					<img src="/images/world/city/<?= $city ?>/1_bg.jpg" alt="" width="550" height="300"/>

					<div style="position:absolute; left:39px; top:201px; width:70px; height:55px;">
						<a href="<?=$this->url->get('map/') ?>?room=20"><img src="/images/world/city/<?= $city ?>/1_market.gif" data-content="Рынок" class="tooltip text aFilter"></a>
					</div>
					<div style="position:absolute; left:311px; top:87px; width:117px; height:117px;">
						<a href="<?=$this->url->get('map/') ?>?room=17"><img src="/images/world/city/<?= $city ?>/1_Bank.gif" data-content="Банк" class="tooltip text aFilter"></a>
					</div>
					<div style="position:absolute; left:452px; top:141px; width:93px; height:77px;">
						<a href="<?=$this->url->get('map/') ?>?room=7"><img src="/images/world/city/<?= $city ?>/1_Store.gif" data-content="Магазин" class="tooltip text aFilter"></a>
					</div>
					<div style="position:absolute; left:17px; top:26px; width:278px; height:170px;">
						<a href="<?=$this->url->get('map/') ?>?room=23"><img src="/images/world/city/<?= $city ?>/1_Arena.gif" data-content="Арена" class="tooltip text aFilter"></a>
					</div>
					<div style="position:absolute; left:421px; top:237px; width:85px; height:59px;">
						<img src="/images/world/city/<?= $city ?>/1_Steads.gif" data-content="Почта" class="tooltip text">
					</div>
					<div style="position:absolute; left:249px; top:210px; width:149px; height:80px;">
						<a href="<?=$this->url->get('map/') ?>?room=11"><img src="/images/world/city/<?= $city ?>/1_Smith.gif" data-content="Кузница" class="tooltip text aFilter"></a>
					</div>
					<div style="position:absolute; left:6px; top:195px; width:23px; height:32px;">
						<a href="<?=$this->url->get('map/') ?>?room=101"><img src="/images/world/city/<?= $city ?>/1_Left.gif" data-content="Королевская улица" class="tooltip text aFilter"></a>
					</div>
					<div style="position:absolute; left:520px; top:231px; width:27px; height:32px;">
						<a href="<?=$this->url->get('map/') ?>?room=104"><img src="/images/world/city/<?= $city ?>/1_Right.gif" data-content="Парк" class="tooltip text aFilter"></a>
					</div>
				</div>
			</td>
		</tr>
	</table>
</div>