<?
/**
 * @var $objects \Game\Models\Objects[]
 */
if (count($objects))
{
	foreach ($objects as $object)
	{
		$obj_inf = $object->getInf();
		$obj_min = $object->getMin();

		if ($obj_inf[6] <= 0)
			continue;

		$price = (0.05 * ($obj_min[0] * $obj_inf[6]));
		$priceOne = (0.05 * $obj_min[0]);

		if ($obj_inf[5] == 1)
		{
			$price *= 10;
			$priceOne *= 10;
		}

		$min = $object->getMinDemands();
		$add = $object->getBonus();
		?>
		<div align='center' class="shopitems">
			<table border='1' background='/images/inman_fon2.gif' class="item" style='padding:5px; border-collapse: collapse' bordercolor='#D8C792' width='100%'>
				<tr>
					<td width='30%' align='center'>
						<small><img src='/images/items/<?=$object->tip ?>/<?= $obj_inf[0] ?>.gif' alt='<?= $obj_inf[1] ?>'><br></small>
						<a href="#" onclick="confirmDialog('Подтвердите действие', 'Починить этот предмет за <?=$price ?> зол.?', 'load(\'/map/?otdel=<?=$otdel ?>&repair=<?=$object->id ?>&full=Y\')')"><b>Починить весь предмет</b></a><br>
						<a href="#" onclick="confirmDialog('Подтвердите действие', 'Починить за <?=$priceOne ?> зол.?', 'load(\'/map/?otdel=<?=$otdel ?>&repair=<?=$object->id ?>&full=N\')')"><b>Починить 1 ед.</b></a>
					</td>
					<td width='70%'>
						<small>
							<b><?= $obj_inf[1] ?></b><br>
							Гос. цена: <b><?= $obj_inf[2] ?></b> зол.<br>
							Долговечность предмета: <b><?= $obj_inf[6] ?></b>/<b><?= $obj_inf[7] ?></b><br>
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
?>