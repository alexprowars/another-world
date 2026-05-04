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

		$min = $object->getMinDemands();
		$add = $object->getBonus();
		?>
		<div align='center' class="shopitems">
			<table border='1' background='/images/inman_fon2.gif' class="item" style='padding:5px; border-collapse: collapse' bordercolor='#D8C792' width='100%'>
				<tr>
					<td width='20%' align='center'>
						<small><img src='/images/items/<?=$object->tip ?>/<?= $obj_inf[0] ?>.gif' alt='<?= $obj_inf[1] ?>'><br></small>
					</td>
					<td width='40%'>
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
					<td width='40%'>
						<? foreach ($weapons as $n => $weapon): ?>
							<? $w_inf = $weapon->getInf(); ?>
							<? if (!($n % 5)): ?><br><? endif; ?>
							<a href="javascript:;" onclick="confirmDialog('Подтвердите действие', 'Вы действительно хотите вставить этот камень в <?=$w_inf[1] ?>', 'load(\'/map/?otdel=<?=$otdel ?>&insert=<?=$object->id ?>&weapon=<?=$weapon->id ?>\')')"><img src="/images/items/<?=$w_inf[0] ?>.gif" alt=""></a>
						<? endforeach; ?>
					</td>
				</tr>
			</table>
		</div>
		<br>
	<?
	}
}
?>