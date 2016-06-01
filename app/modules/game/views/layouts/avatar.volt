<? $this->view->partial('shared/city_header', Array('title' => 'Бесплатные образы')); ?>
<table align='center' class='ltable' style='width:100%;'>
	<tr>
		<td valign="top" bgcolor=efdcb8 class="textblock">
			<? if (isset($message) && !empty($message)): ?>
				<div class="alert alert-danger"><?= $message ?></div>
			<? endif; ?>

			<center>
				<small><b>Внимание! Выбрав образ сейчас, Вы более не сможете его сменить!</b></small>
			</center>
			<br>

			<table width="100%">
				<tr>
					<? if ($this->user->level < 8): ?>
						<? for ($g = 1; $g < 6; $g++): ?>
							<? if (!($g % 6)): ?>
								</tr><tr>
							<? endif; ?>
							<td class="text-xs-center">
								<a href="javascript:;" onclick="confirmDialog('Подтвердите действие', 'Применить это образ?', 'load(\'/avatar/?setimg=<?=$g ?>\')')">
								<img src="/images/avatar/obraz/<?=$this->user->sex ?>/<?=$g ?>.png">
								</a>
							</td>
						<? endfor; ?>
					<? else: ?>
						<td><center><b>У вас уже установлен образ. Сменить его вы сможете только в здании администрации.</b></center></td>
					<? endif; ?>
				</tr>
			</table>
		</td>
	</tr>
</table>