<? $this->view->partial('shared/city_header', Array('title' => 'Академия', 'credits' => $this->user->credits, 'f_credits' => $this->user->f_credits)); ?>

<div class="textblock">
	<div class="row">
		<div class="col-xs-12 text-xs-right">
			<a href="<?=$this->url->get('map/') ?>"><img src='/images/images/refresh.gif' alt='Обновить'></a>
			<a href='<?=$this->url->get('map/') ?>?refer=9'><img src='/images/images/back.gif' alt='Вернуться'></a>
		</div>
	</div>
	<br>
	<table width=100%>
		<tr>
			<td align=center>
				<? if ($this->user->r_time > time()): ?>
					<script src='/img/js/time.js'></script>
					<center>
						<table cellspacing=0 cellpadding=3>
							<tr>
								<td><font color=red><b>Оставшееся время обучения:</b></font></td>
								<td id=know style='COLOR: red; FONT-WEIGHT: Bold; TEXT-DECORATION: Underline'></td>
							</tr>
						</table>
					</center>
					<script>ShowTime('know', "<?=($this->user->r_time - time()) ?>");</script>
				<? endif; ?>

				<? if (!empty($message)): ?>
					<p class="message bg-danger"><?=$message ?></p>
				<? endif; ?>

				<? if ($this->user->r_time == 0): ?>
					<table width=100%>
						<tr>
							<td align=center>
								<b>В нашем заведении Вы можете стать высококвалифицированным специалистом. Ниже приведён список предлагаемых Вам профессий:</b><br><br>
								<table class="table vertical">
									<thead>
										<tr>
											<th width=18 align=center><b>№</b></th>
											<th><b>Наименование</b></th>
											<th width=150 class="text-xs-center">Уровень</th>
											<th width=150 class="text-xs-center">Срок обучения</th>
											<th width=160 class="text-xs-center">Стоимость обучения</th>
											<th class="text-xs-center" width=120>Действие</th>
										</tr>
									</thead>
									<tbody>
										<? foreach ($professions as $i => $item): ?>
										<tr>
											<td class="text-xs-center"><b><?=($i + 1) ?></b></td>
											<td><b><?=$item['title'] ?></b></td>
											<td class="text-xs-center"><b><?=$item['level'] ?></b></td>
											<td class="text-xs-center"><b><?=(round($item['srok']/60,1)) ?> мин.</b></td>
											<td class="text-xs-center"><b><?=$item['price'] ?> зол.</b></td>
											<td class="text-xs-center">
												<? if ($this->user->r_time == 0): ?>
													<a href="javascript:;" class="btn btn-primary" onclick="confirmDialog('Подтвердите действие', 'Вы действительно хотите получить данную профессию?', 'load(\'<?=$this->url->get('map/') ?>?learn=<?=$item['id'] ?>\')')">Обучаться</a>
												<? endif; ?>
											</td>
										</tr>
										<? endforeach; ?>
									</tbody>
								</table>
							</td>
						</tr>
					</table>
				<? endif; ?>
			</td>
		</tr>
	</table>
</div>