<script type="text/javascript">
	function present (id, title)
	{
		dialog.dialog("option", "title", 'Подарить "'+title+'"');
		$("#windowDialog").html('<div class="container-fluid">' +
		'<form method="post" action="/map/?otdel=<?=$otdel ?>" class="form-horizontal">' +
		'<input type="hidden" name="gift" value="'+id+'"><div class="form-group">' +
		'<div class="col-xs-12"><input type="text" name="login" placeholder="Введите логин игрока" class="form-control input-sm"></div></div>' +
		'<div class="form-group"><div class="col-xs-12">' +
		'<div class="radio"><label><input type="radio" name="who" value="1" checked>От имени игрока</label></div>' +
		<? if ($this->user->tribe > 0): ?>
			'<div class="radio"><label><input type="radio" name="who" value="2">От имени клана</label></div>' +
		<? endif; ?>
		'<div class="radio"><label><input type="radio" name="who" value="3">Анонимно</label></div>' +
		'</div></div>' +
		'<div class="form-group"><div class="col-xs-12"><textarea name="text" placeholder="Текст пожелания" class="form-control input-sm"></textarea></div>' +
		'</div></form></div>');
		dialog.dialog("open");
	}
</script>

<? if (count($objects)): ?>
	<? foreach ($objects as $object): ?>
	<?
		$obj_inf = $object->getInf();
		$obj_min = $object->getMin();

		$min = $object->getMinDemands();
		$add = $object->getBonus();
	?>


		<div align='center' class="shopitems">
			<table border='1' background='/images/inman_fon2.gif' class="item" style='padding:5px; border-collapse: collapse' bordercolor='#D8C792' width='100%'>
				<tr>
					<td width='30%' align='center'>
						<small><img src='/images/items/<?=$object->tip ?>/<?= $obj_inf[0] ?>.gif' alt='<?= $obj_inf[1] ?>'><br></small>
						<a href="javascript:;" onclick="present(<?=$object->id ?>, '<?=$obj_inf['1'] ?>');"><b>Подарить</b></a>
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
	<? endforeach; ?>
<? endif; ?>