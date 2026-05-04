<? if (count($objects)): ?>
	<? foreach ($objects as $object): ?>
		<?

		$min = $object->item->getMinDemands();
		$add = $object->item->getBounus();

		?>
		<div align='center' class="shopitems">
			<table border='1' background='/images/inman_fon2.gif' class="item" style='padding:5px; border-collapse: collapse' bordercolor='#D8C792' width='100%'>
				<tr>
					<td width='30%' align='center'>
						<small><img src='/images/items/<?= $object->item->tip ?>/<?= $object->item->name ?>.gif' alt='<?= $object->item->title ?>'><br></small>
						<a href="javascript:;" onclick="confirmDialog('Подтвердите действие', 'Купить предмет &quot;<?= $object->item->title ?>&quot; за &quot;<?= $object->item->price ?>&quot; зол.?', 'load(\'/map/?otdel={{ otdel }}&buy=<?= $object->shop->id ?>\')')"><b>Купить за <?= $object->item->price ?> зол.</b></a>
					</td>
					<td width='70%'>
						<small>
							<b><?= $object->item->title ?></b><br>
							Гос. цена: <b><?= $object->item->price ?></b> зол.<br>
							<? if ($object->item->iznos > 0): ?>
								Долговечность предмета: <b><?= $object->item->iznos ?></b><br>
							<? endif; ?>
							<b><small style='COLOR: Red'>Предмет не подлежит ремонту</small></b><br>
							Срок жизни: <b STYLE='COLOR: Red'><?=($object->item->life / 86400) ?> дн.</b><br>
							Количество: <u><?=$object->shop->cnt ?></u> шт.<br>
						</small>

						<? if ($min != ''): ?>
							<br><small><b>Минимальные требования:</b><br><?= $min ?></small>
						<? endif; ?>
						<? if ($add != ''): ?>
							<br><small><b>Действие предмета:</b><br><?= $add ?></small>
						<? endif; ?>
						<? if ($object->item->about != ''): ?>
							<small><br><b>Дополнительная информация:</b><br><?= $object->item->about ?></small>
						<? endif; ?>
					</td>
				</tr>
			</table>
		</div>
		<br>
	<? endforeach; ?>
<? endif; ?>