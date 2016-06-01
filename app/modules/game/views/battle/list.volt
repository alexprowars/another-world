<? if (isset($alert) && $alert != ''): ?>
	<script type="text/javascript">
		$(document).ready(function()
		{
			alert('<?=$alert ?>');

			load('?set=battle');
		});
	</script>
<? endif; ?>

<? $this->view->partial('shared/city_header', Array('title' => 'Поединки на арене')); ?>
<div class="textblock offers">
	<div class="row">
		<div class="col-xs-12 text-xs-right">
			<a href="<?=$this->url->get('battle/') ?>?battle_type=<?=$battleType ?>"><img src='/images/images/refresh.gif' alt='Обновить'></a>
			<a href="<?=$this->url->get('map/') ?>"><img src='/images/images/back.gif' alt='Вернуться'></a>
		</div>
	</div>

	<? if (isset($message) && !empty($message)): ?>
		<div class="alert alert-danger"><?= $message ?></div>
	<? endif; ?>

	<? if ($this->user->travma > time()): ?>
		<center><b style='COLOR: Red'>Вы не можете драться, т.к. тяжело травмированы!<br>Вам необходим отдых!</b></center>
	<? elseif ($this->user->room != 1 && $this->user->room != 2 && $this->user->room != 3 && $this->user->room != 4): ?>
		<center><b style='COLOR: Red'>Вы выбрали не совсем удачное место для проведения поединка!<br><br><a href="<?=$this->url->get('battle/') ?>?teleport=Y" class="btn btn-primary">Переместиться в Арену!</a></b></center>
	<? else: ?>
		<br>
		<? if ($battleId == 0): ?>
			<table class="type">
				<tr>
					<td class="col-xs-4 text-xs-center">
						<b><a class="<?= ($battleType == 1 ? 'active' : '') ?>" href="<?=$this->url->get('battle/') ?>?battle_type=1">PvP</a></b>
					</td>
					<td class="col-xs-4 text-xs-center">
						<b><a class="<?= ($battleType == 2 ? 'active' : '') ?>" href="<?=$this->url->get('battle/') ?>?battle_type=2">Групповые</a></b>
					</td>
					<td class="col-xs-4 text-xs-center">
						<b><a class="<?= ($battleType == 3 ? 'active' : '') ?>" href="<?=$this->url->get('battle/') ?>?battle_type=3">Хаотические</a></b>
					</td>
				</tr>
			</table>
		<? endif; ?>
		<?= $list ?>
	<? endif; ?>
</div>