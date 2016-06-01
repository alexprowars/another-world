<? $this->view->partial('shared/city_header', Array('title' => 'Центр Занятости', 'credits' => $this->user->credits)); ?>

<div class="textblock">
	<div class="row">
		<div class="col-xs-12 text-xs-right">
			<a href="<?=$this->url->get('map/') ?>"><img src='/images/images/refresh.gif' alt='Обновить'></a>
			<a href="<?=$this->url->get('map/') ?>?refer=16"><img src='/images/images/back.gif' alt='Вернуться'></a>
		</div>
	</div>

	<? if (!empty($message)): ?>
		<p class="message bg-danger"><?=$message ?></p>
	<? endif; ?>

	<? if ($this->user->r_time > time()): ?>
		<div class="row">
			<div class="col-xs-12 text-xs-center">
				<font color=red><b>Оставшееся время работы:</b></font>
				<span id=know style='COLOR: red; FONT-WEIGHT: Bold; TEXT-DECORATION: Underline'></span>
			</div>
		</div>
		<script>ShowTime('know', <?=($this->user->r_time - time()) ?>);</script>
	<? else: ?>
		<? foreach ($types as $type): ?>
			<fieldset>
				<legend><?=$type['name'] ?> (требует <?=$type['name'] ?> уровень и <?=$type['activity'] ?> ед. активности за час)</legend>
				<table class="table">
					<thead>
						<tr>
							<th width="18" class="text-xs-center">№</th>
							<th>Наименование</th>
							<th width="150" class="text-xs-center">Срок работы</th>
							<th width="160" class="text-xs-center">Зарплата</th>
							<th class="text-xs-center" width="120">Действие</th>
						</tr>
					</thead>
					<tbody>
						<? $i = 0; foreach ($works as $work): if ($work['type_id'] != $type['id']) continue; ?>
							<tr>
								<td class="text-xs-center"><b><?=($i+1) ?></b></td>
								<td><b><?=$work['title'] ?></b></td>
								<td class="text-xs-center"><b><?=(round($work['time']/60, 1)) ?> мин.</b></td>
								<td class="text-xs-center"><b><?=$work['price'] ?> зол.</b></td>
								<td class="text-xs-center">
									<? if ($this->user->r_time < time()): ?>
										<a href="javascript:;" class="btn btn-primary" onclick="confirmDialog('Подтвердите действие', 'Вы действительно хотите получить данную работу?', 'load(\'<?=$this->url->get('map/') ?>?get=<?=$work['id'] ?>\')')">Работать</a>
									<? endif; ?>
								</td>
							</tr>
						<? $i++; endforeach; ?>
					</tbody>
				</table>
			</fieldset>
			<br>
		<? endforeach; ?>
	<? endif; ?>
</div>