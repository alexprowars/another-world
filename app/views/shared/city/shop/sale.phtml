<?
/**
 * @var $objects \App\Models\Objects[]
 */
if (count($objects))
{
	foreach ($objects as $object)
	{
		$info = $object->getInf();

		if (SHOP_ID == 2 && ($object->tip == 12 || $object->tip == 22 || $info[5] != 1))
			continue;
		elseif ($object->tip == 12 || $object->tip == 14 || $object->tip == 22)
			continue;

		$obj_min = $object->getMin();

		if (SHOP_ID == 2)
		{
			$sale_price = round($info[2] * 0.5, 2);
		}
		else
		{
			if ($object->tip < 12)
				$sale_price = round(($info[2] * (1 - ($info[6] / ($info[7] + 0.01)))) * 0.5, 2);
			else
				$sale_price = round($info[2] * 0.5, 2);
		}

		$min = $object->getMinDemands();
		$add = $object->getBonus();

		?>
		<div align='center' class="shopitems">
			<table border='1' background='/images/inman_fon2.gif' class="item" style='padding:5px; border-collapse: collapse' bordercolor='#D8C792' width='100%'>
				<tr>
					<td width='30%' align='center'>
						<small><img src='/images/items/<?=$object->tip ?>/<?= $info[0] ?>.gif' alt='<?= $info[1] ?>'><br></small>
						<a href="javascript:;" onclick="confirmDialog('Подтвердите действие', 'Продать предмет &quot;<?= $info[1] ?>&quot; за &quot;<?= $sale_price ?>&quot; зол.?', 'load(\'/map/?otdel=<?= $otdel ?>&sale=<?= $object->id ?>\')')"><b>Продать за <?= $sale_price ?> <?=(SHOP_ID == 2 ? 'е' : '') ?>зол.</b></a>
					</td>
					<td width='70%'>
						<small>
							<b><?= $info[1] ?></b><br>
							Гос. цена: <b><?= $info[2] ?></b> зол.<br>
							Долговечность предмета: <b><?= $info[6] ?></b>/<b><?= $info[7] ?></b><br>
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