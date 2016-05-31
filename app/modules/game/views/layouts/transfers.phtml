<? $this->view->partial('shared/city_header', Array('title' => 'Передача предметов и денег')); ?>

<div class="textblock text-xs-center">
	<? if (!empty($message)): ?>
		<p class="message bg-danger"><?=$message ?></p>
	<? endif; ?>

	<? if (!$this->request->has('login')): ?>
		<script type="text/javascript">
			$(document).ready(function()
			{
				ShowForm('Передача предметов и денег', '<?=$this->url->get('transfers/') ?>','','');
			});
		</script>
	<? endif; ?>
	<center>
		<? if ($this->request->has('login')): ?>
			<? if (!isset($info['id'])): ?>
				<tr><td align=center colspan=2 bgcolor=e2e0e0>Персонаж <b><?=$this->request->get('login', 'string') ?></b> не существует!</td></tr>
			<? elseif ($info['id'] == $this->user->getId()): ?>
				<tr><td align=center colspan=2 bgcolor=e2e0e0>Вы не можете передать что-либо самому себе!</td></tr>
			<? else: ?>

				<table width="50%" border=1 cellspacing=0 cellpadding=2 bordercolor=#C7C7C7>
					<tr>
						<td bgcolor=e2e0e0 align=center width=80>
							<input type=button value='Сменить' onclick="ShowForm('Передача предметов и денег', '<?=$this->url->get('transfers/') ?>','','');" class=standbut>
						</td>
						<td bgcolor=e2e0e0 align=center>
							<div id="person"></div>
							<script type="text/javascript">$('#person').html(show_inf('<?=$info['username'] ?>', '<?=$info['id'] ?>', '<?=$info['level'] ?>', '<?=$info['rank'] ?>', '<?=$info['tribe'] ?>'));</script>
						</td>
					</tr>
				</table>
				<br>
				<form action='<?=$this->url->get('transfers/') ?>' method=post>
					<input type="hidden" name="login" value="<?=$info['id'] ?>">
					<table width="50%" border=1 cellspacing=0 cellpadding=2 bordercolor=#C7C7C7>
						<tr>
							<td bgcolor=e2e0e0 align=center>
								Передать кредиты: <input name="credits" class="search" size="5">
								Причина: <input name="comment" class="search" size="10">
								<input type="submit" value="Передать" class="standbut">
							</td>
						</tr>
					</table>
				</form>
				<br>
					<?
					/**
					 * @var $objects \App\Models\Objects[]
					 */
					?>
					<? foreach ($objects as $object): ?>
						<?
						$oInfo = $object->getInf();
						?>
						<table border='1' background='/images/inman_fon2.gif' class="item" style='padding:5px; border-collapse: collapse' bordercolor='#D8C792' width='100%'>
							<tr>
								<td align="center" valign="middle">
									<b><?=$oInfo[1] ?></b><br>
									<b>Гос. цена: <?=$oInfo[2] ?> зол.</b><br>
									Долговечность предмета: <?=$oInfo[6] ?> [<?=$oInfo[7] ?>]<br>
									Тип предмета: <i><?=_getText('weapon', $object->tip) ?></i><br>
								</td>
								<td align="center" width="200">
									<img src='/images/items/<?=$object->tip ?>/<?= $oInfo[0] ?>.gif' alt='<?= $oInfo[1] ?>'><br>
									<a href='<?=$this->url->get('transfers/') ?>?transfer=<?=$object->id ?>&login=<?=$info['id'] ?>'>Передать</a>
								</td>
							</tr>
						</table>
					<div style="height: 5px"></div>
					<? endforeach; ?>
			<? endif; ?>
		<? else: ?>
			Введите ник персонажа
		<? endif; ?>
	</center>
</div>