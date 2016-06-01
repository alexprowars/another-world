<?

/**
 * @var $object object
 */

if ($this->user->tutorial == 3 && $object->item->id == 817)
{
	$object->item->price = 0;
}

$t_price = round($object->item->price * 0.9, 2);
$vip = round($object->item->price * 0.67, 2);

$min = $object->item->getMinDemands();
$add = $object->item->getBounus();

?>
<div class="shop-item">
	<table class='table item'>
		<tr>
			<td align="center" width="40%" style="vertical-align: middle">
				<img src="/images/items/<?=$object->item->tip ?>/<?=$object->item->name ?>.gif" alt="<?=$object->item->title ?>"><br>
				<? if ($type == 1): ?>
					<a href="javascript:;" onclick="confirmDialog('Подтвердите действие', 'Купить предмет &quot;<?=$object->item->title ?>&quot;?', 'load(\'/map/?otdel=<?=$otdel ?>&buy=<?=$object->shop->id ?>\')')"><b>Купить</b></a>
				<? elseif ($type == 2): ?>
					<a href='javascript:;' onclick="present(<?=$object->shop->id ?>);">Выгравировать надпись за 150 зол.</a><br><br>

					<? if ($this->user->proff == 2): ?>
						<a href="<?=$this->url->get('map/') ?>?otdel=<?=$otdel ?>&act=upgrade&id=<?=$object->shop->id ?>">Увеличить урон предмета за 50 пл.<br>(мин +1 и мах +1)<br>(<i>Максимальная долговечноть -20</i>)</a>
					<? else: ?>
						<font color=red><b>Перековать может только Кузнец</b></font>
					<? endif; ?>
				<? elseif ($type == 3): ?>
					<a href="#" onclick="confirmDialog('Подтвердите действие', 'Починить этот предмет за <?=$t_price ?> зол.?', 'load(\'/map/?otdel=<?=$otdel ?>&iznos=<?=$object->shop->id ?>\')')"><b>Починить весь предмет</b></a><br>
					<a href="#" onclick="confirmDialog('Подтвердите действие', 'Починить за <?=$vip ?> зол.?', 'load(\'/map/?otdel=<?=$otdel ?>&iznos1=<?=$object->shop->id ?>\')')"><b>Починить 1 ед.</b></a>
				<? elseif ($type == 4): ?>
					<a href="#" onclick="confirmDialog('Подтвердите действие', 'Вы действительно хотите огранить &quot;<?=$object->item->title ?>&quot; ?', 'load(\'/map/?otdel=<?=$otdel ?>&ogran=<?=$object->shop->id ?>\')')"><b>Огранить</b></a><br>
				<? endif; ?>
			</td>
			<td>
				<small>
					<b><?=$object->item->title ?></b><br>
					<? if ($object->item->f_price > 0): ?>
						Гос. цена: <b><?=$object->item->f_price ?></b> пл.
					<? else: ?>
						Гос. цена: <b><?=$object->item->price ?></b> зол.
					<? endif; ?>
					<br>
					<? if ($object->item->f_price > 0 && $type == 1 && $this->user->vip == 1): ?>
						VIP. цена: <b><?=$object->item->getVipPrice() ?></b> пл.<br>
					<? endif; ?>

					Долговечность: <b><?=$object->item->iznos ?></b></small><br>
					<small>Тип предмета: <i><?=_getText('weapon', $object->item->tip) ?></i><br>

					<? if ($object->item->use_mana > 0): ?>
						<small>Затраты маны: <i><?=$object->item->use_mana ?></i><br>
					<? endif; ?>
					Остаток на складе: <b><?=$object->shop->cnt ?></b><br>
					<? if ($object->shop->delivery > 0): ?>
						Завоз: <b><?=$object->shop->delivery ?></b>.</small><br>
					<? endif; ?>
				</small>
			</td>
		</tr>
		<tr>
			<td>
				<? if ($min != ''): ?>
					<small><b>Требования:</b><br><?= $min ?></small>
				<? endif; ?>
			</td>
			<td>
				<? if ($add != ''): ?>
					<small><b>Действие предмета:</b><br><?= $add ?></small>
				<? endif; ?>
			</td>
		</tr>
		<?  if ($object->item->magic > 0 || $object->item->about != ''): ?>
			<tr>
				<td colspan="2">
					<?  if ($object->item->magic > 0): ?>
						<div><small><b>Встроенная магия:</b><br><?=$object->item->magic ?></small></div>
					<? endif; ?>
					<? if ($object->item->about != ''): ?>
						<div><small><b>Дополнительная информация:</b><br><?=$object->item->about ?></small></div>
					<? endif; ?>
				</td>
			</tr>
		<? endif; ?>
	</table>
</div>