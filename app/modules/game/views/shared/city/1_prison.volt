<? $this->view->partial('shared/city_header', Array('title' => 'Тюрьма')); ?>
<div class="textblock">
	<div class="row">
		<div class="col-xs-12 text-xs-right">
			<a href="<?=$this->url->get('map/') ?>"><img src='/images/images/refresh.gif' alt='Обновить'></a>
			<? if ($this->user->t_time < time()): ?>
				<a href="<?=$this->url->get('map/') ?>?refer=666"><img src='/images/images/back.gif' alt='Вернуться'></a>
			<? endif; ?>
		</div>
	</div>

	<? if ($this->user->t_time < time()): ?>
		<table class="table">
			<tr>
				<td align=center>
					<b>Вы пока еще на свободе!<br>Надеемся, Вы сюда не попадёте никогда :)</b>
					<? if (count($list)): ?>
						<br><b>Список заключённых:</b>
					<? endif; ?>
				</td>
			</tr>
		</table>
		<? if (count($list)): ?>
			<table class="table">
				<tr>
					<td bgcolor=#FCFAF3 width=25% align=center><b>Ник</b></td>
					<td bgcolor=#FCFAF3 width=50% align=center><b>Причина</b></td>
					<td bgcolor=#FCFAF3 width=25% align=center><b>Дата освобождения</b></td>
				</tr>
				<? foreach ($list as $item): ?>
					<tr>
						<td bgcolor=#FCFAF3 width=25% align=center><b><?=$item['username'] ?></b></td>
						<td bgcolor=#FCFAF3 width=50% align=center><b><?=$item['theme'] ?></b></td>
						<td bgcolor=#FCFAF3 width=25% align=center><b><?=date('d.m.Y H:i', $item['t_time']) ?></b></td>
					</tr>
				<? endforeach; ?>
			</table>
		<? endif; ?>
	<? else: ?>
		<table class="table">
			<tr>
				<td>
					<table border=0 cellpadding=0 cellspacing=0>
						<tr>
							<td width=180>Оставшийся срок наказания:</td>
							<td id=dt style='FONT-WEIGHT: Bold;'></td>
						</tr>
					</table>
				</td>
				<script src='/img/js/time.js'></script>
				<script>
					ShowTime('dt', <?=$this->user->t_time - time() ?>);
				</script>
			</tr>
			<tr>
				<td style='BORDER-TOP: 1px solid'>
					Причина отправку в тюрьму: <u><font color=red><b><?=$this->user->reason ?></b></font></u>
				</td>
			</tr>
		</table>
	<? endif; ?>
</div>