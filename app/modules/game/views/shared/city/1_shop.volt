<? $this->view->partial('shared/city_header', Array('title' => 'Государственный Магазин', 'credits' => $this->user->credits)); ?>

<div class="textblock">
	<div class="row">
		<div class="col-xs-9 col-xl-10">
			<? if (!empty($message)): ?>
				<div class="alert alert-danger"><?= $message ?></div>
			<? endif; ?>
			<?
				if ($otdel == 100)
					$this->view->partial('shared/city/shop/sale', Array('objects' => $objects, 'otdel' => $otdel, 'price' => 1));
				elseif ($otdel < 40)
					$this->view->partial('shared/city/shop/buy', Array('objects' => $objects, 'otdel' => $otdel, 'price' => 1));
			?>
		</div>
		<div class="col-xs-3 col-xl-2">
			<div class="shopnav">
				<a href="<?=$this->url->get('map/') ?>?otdel=100"><img src='/images/images/shop_sale.gif' alt='Продать предметы'></a>
				<a href="<?=$this->url->get('map/') ?>?otdel=<?= $otdel ?>"><img src='/images/images/refresh.gif' alt='Обновить'></a>
				<a href="<?=$this->url->get('map/') ?>?refer=7"><img src='/images/images/back.gif' alt='Вернуться'></a>
			</div>
			<div align='center' class="shopotdels">
				<table>
					<tr>
						<td width='100%' colspan='2' align='center'><b>Оружие</b></td>
					</tr>
					<tr>
						<td width='50%' align='center'><a href='<?=$this->url->get('map/') ?>?otdel=1'>Ножи</a></td>
						<td width='50%' align='center'><a href='<?=$this->url->get('map/') ?>?otdel=2'>Мечи</a></td>
					</tr>
					<tr>
						<td width='50%' align='center'><a href='<?=$this->url->get('map/') ?>?otdel=3'>Топоры</a></td>
						<td width='50%' align='center'><a href='<?=$this->url->get('map/') ?>?otdel=4'>Дубины</a></td>
					</tr>
					<tr>
						<td width='100%' colspan='2' align='center'><a href='<?=$this->url->get('map/') ?>?otdel=5'>Луки, Арбалеты</a></td>
					</tr>
				</table>
			</div>
			<div align='center' class="shopotdels">
				<table>
					<tr>
						<td width='100%' colspan='2' align='center'><b>Амуниция</b></td>
					</tr>
					<tr>
						<td width='50%' align='center'><a href='<?=$this->url->get('map/') ?>?otdel=6'>Шлемы</a></td>
						<td width='50%' align='center'><a href='<?=$this->url->get('map/') ?>?otdel=7'>Рубахи</a></td>
					</tr>
					<tr>
						<td width='50%' align='center'><a href='<?=$this->url->get('map/') ?>?otdel=8'>Тяжёлая броня</a></td>
						<td width='50%' align='center'><a href='<?=$this->url->get('map/') ?>?otdel=16'>Браслеты</a></td>
					</tr>
					<tr>
						<td width='50%' align='center'><a href='<?=$this->url->get('map/') ?>?otdel=16'>Нарукавники</a></td>
						<td width='50%' align='center'><a href='<?=$this->url->get('map/') ?>?otdel=10'>Щиты</a></td>
					</tr>
					<tr>
						<td width='50%' align='center'><a href='<?=$this->url->get('map/') ?>?otdel=11'>Пояса</a></td>
						<td width='50%' align='center'><a href='<?=$this->url->get('map/') ?>?otdel=12'>Обувь</a></td>
					</tr>
					<tr>
						<td width='50%' align='center'><a href='<?=$this->url->get('map/') ?>?otdel=9'>Перчатки</a></td>
						<td width='50%' align='center'><a href='<?=$this->url->get('map/') ?>?otdel=17'>Штаны</a></td>
					</tr>
				</table>
			</div>
			<div align='center' class="shopotdels">
				<table>
					<tr>
						<td width='100%' colspan='2' align='center'><b>Ювелирные украшения</b></td>
					</tr>
					<tr>
						<td width='50%' align='center'><a href='<?=$this->url->get('map/') ?>?otdel=13'>Ожерелья</a></td>
						<td width='50%' align='center'><a href='<?=$this->url->get('map/') ?>?otdel=14'>Кольца</a></td>
					</tr>
					<tr>
						<td width='100%' colspan='2' align='center'><a href='<?=$this->url->get('map/') ?>?otdel=15'>Серьги</a></td>
					</tr>
				</table>
			</div>
			<div align='center' class="shopotdels">
				<table>
					<tr>
						<td width='100%' colspan='4' align='center'><b>Работа</b></td>
					</tr>
					<tr>
						<td width='50%' align='center' colspan='2'><a href='<?=$this->url->get('map/') ?>?otdel=20'>Инструменты</a></td>
						<td width='50%' align='center' colspan='2'><a href='<?=$this->url->get('map/') ?>?otdel=32'>Документы</a></td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</div>