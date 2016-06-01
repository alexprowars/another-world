<div class="textblock">
	<? if (!empty($message)): ?>
		<div class="alert alert-danger"><?=$message ?></div>
	<? endif; ?>
	<? $c = 0; for ($i = 1; $i < 11; $i++): ?>
		<? if (isset($priems[$i]) && $priems[$i] != 0) $c++; ?>
	<? endfor; ?>
	<? if ($c > 0): ?>
	<h5><b>Список активных приёмов:</b></h5>
	<table class="table table-striped vertical">
		<thead>
			<tr>
				<th width="50" class="text-xs-center">№</th>
				<th width="300" class="text-xs-center">Название</th>
				<th class="text-xs-center">Действие</th>
			</tr>
		</thead>
		<tbody>
			<? for ($i = 1; $i < 11; $i++): ?>
				<? if (isset($priems[$i]) && $priems[$i] != 0): ?>
					<tr>
						<td class="text-xs-center"><?=$i ?></td>
						<td class="text-xs-center"><?=$priem_full[$priems[$i]]['name'] ?></td>
						<td class="text-xs-center">
							<a class="btn btn-danger" href="<?=$this->url->get("pers/priem/") ?>?unset_priem=<?=$i ?>">Убрать</a>
						</td>
					</tr>
				<? endif; ?>
			<? endfor; ?>
		</tbody>
	</table>
	<br>
	<? endif; ?>
	<h5><b>Список доступных приёмов:</b></h5>
	<table class="table table-striped vertical">
		<thead>
			<tr>
				<th width="120" class="text-xs-center">Название</th>
				<th width="40%">Информация</th>
				<th width="20" class="text-xs-center">lvl</th>
				<th width="20" class="text-xs-center"><img src='/images/battle/priem/block.gif'></th>
				<th width="20" class="text-xs-center"><img src='/images/battle/priem/hit.gif'></th>
				<th width="20" class="text-xs-center"><img src='/images/battle/priem/krit.gif'></th>
				<th width="20" class="text-xs-center"><img src='/images/battle/priem/parry.gif'></th>
				<th width="20" class="text-xs-center"><img src='/images/battle/priem/hp.gif'></th>
				<th width="20" class="text-xs-center"><img src='/images/battle/priem/spirit.gif'></th>
				<th class="text-xs-center">Действие</th>
			</tr>
		</thead>
		<tbody>
			<? if (is_array($priem_full)): ?>
				<? foreach ($priem_full as $id => $info): if (isset($info['onset'])) continue; ?>
					<tr>
						<td class="text-xs-center">
							<small><?= $info['name'] ?></small>
						</td>
						<td class="text-xs-center">
							<small><?= $info['about'] ?></small>
						</td>
						<td class="text-xs-center"><?= $info['level'] ?></td>
						<td class="text-xs-center"><?= $info['block'] ?></td>
						<td class="text-xs-center"><?= $info['hit'] ?></td>
						<td class="text-xs-center"><?= $info['krit'] ?></td>
						<td class="text-xs-center"><?= $info['parry'] ?></td>
						<td class="text-xs-center"><?= $info['dam'] ?></td>
						<td class="text-xs-center"><?= $info['mag'] ?></td>
						<td class="text-xs-center"><a class="btn btn-primary" href='<?=$this->url->get("pers/priem/") ?>?onset_priem=<?= $id ?>'>Использовать</a></td>
					</tr>
				<? endforeach; ?>
			<? endif; ?>
		</tbody>
	</table>
	<br>
	<h5>Легенда:</h5>
	<table class="table">
		<tr>
			<td width="30"><img src='/images/battle/priem/block.gif'></td>
			<td>Очки блока</td>
		</tr>
		<tr>
			<td><img src='/images/battle/priem/hit.gif'></td>
			<td>Очки пробоя</td>
		</tr>
		<tr>
			<td><img src='/images/battle/priem/krit.gif'></td>
			<td>Очки крита</td>
		</tr>
		<tr>
			<td><img src='/images/battle/priem/parry.gif'></td>
			<td>Очки уворота</td>
		</tr>
		<tr>
			<td><img src='/images/battle/priem/hp.gif'></td>
			<td>Очки повреждений</td>
		</tr>
		<tr>
			<td><img src='/images/battle/priem/spirit.gif'></td>
			<td>Очки магии</td>
		</tr>
	</table>
</div>