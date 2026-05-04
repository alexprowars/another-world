<?
/**
 * @var $objects \Game\Models\Objects[]
 */
if (count($objects))
{
	foreach ($objects as $object)
	{
		$info = $object->getInf();

		if ($info[5] == 1)
			continue;

		$obj_min = $object->getMin();

		$sale_price = round($info[2] * 0.62, 2);

		$min = $object->getMinDemands();
		$add = $object->getBonus();

		?>
		<div align='center' class="shopitems">
			<table border='1' background='/images/inman_fon2.gif' class="item" style='padding:5px; border-collapse: collapse' bordercolor='#D8C792' width='100%'>
				<tr>
					<td width='30%' align='center'>
						<small><img src='/images/items/<?=$object->tip ?>/<?= $info[0] ?>.gif' alt='<?= $info[1] ?>'><br></small>
						<br>
						<form action="/map/?otdel=100&sale=<?=$object->id ?>" method="post">
							<input name="credits" class="search" placeholder="Укажите цену"><br>
							<input type="submit" value="Сдать на коммиссию" class="standbut">
						</form>
					</td>
					<td width='70%'>
						<small>
							<b><?= $info[1] ?></b><br>
							Гос. цена: <b><?= $info[2] ?></b> зол.<br>
							Долговечность предмета: <b><?= $info[6] ?></b>/<b><?= $info[7] ?></b><br>
							Тип предмета: <i><?=_getText('weapon', $object->tip) ?></i><br>
						</small>

						<? if ($min != ''): ?>
							<br><small><b>Минимальные требования:</b><br><?= $min ?></small>
						<? endif; ?>
						<? if ($add != ''): ?>
							<br><small><b>Действие предмета:</b><br><?= $add ?></small>
						<? endif; ?>
						<? if ($object->about != ''): ?>
							<small><br><b>Дополнительная информация:</b><br><?= $object->about ?></small>
						<? endif; ?>
					</td>
				</tr>
			</table>
		</div>
		<br>
		<?
	}
}
else
	echo 'Нет вещей для продажи';