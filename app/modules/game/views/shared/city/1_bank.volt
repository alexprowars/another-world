<? $this->view->partial('shared/city_header', Array('title' => 'Банк', 'credits' => $this->user->credits)); ?>

<div class="textblock">
	<div class="row">
		<div class="col-xs-12 text-xs-right">
			<a href="<?=$this->url->get('map/') ?>?otdel=<?=$otdel ?>"><img src='/images/images/refresh.gif' alt='Обновить'></a>
			<a href="<?=$this->url->get('map/') ?>?refer=17"><img src='/images/images/back.gif' alt='Вернуться'></a>
		</div>
	</div>
	<div class="clearfix"></div>

	<? if (!empty($message)): ?>
		<div class="alert alert-danger"><?= $message ?></div>
	<? endif; ?>


	<ul class="nav nav-tabs">
	  	<li role="presentation" class="<?=($otdel == 1 ? 'active' : '') ?>"><a href="<?=$this->url->get('map/') ?>?otdel=1">Пожертвования</a></li>
		<li role="presentation" class="<?=($otdel == 2 ? 'active' : '') ?>"><a href="<?=$this->url->get('map/') ?>?otdel=2">Обмен валюты</a></li>
	</ul>

	<div class="tab-content">
		<div id="sectionA" class="tab-pane fade in active">
			<? if ($otdel > 0): ?>
				<? if ($otdel == 1): ?>
					<h4 class="text-xs-center">Пожертвования</h4>
					Отблагодари создателей игры! Вы можете внести добровольние вложения в игру. За самые крупные пожертвования вас ждёт некоторая благодарность от администрации.
					<br><br>
					<div class="row">
						<div class="col-xs-6">
							<form method="POST" action="<?=$this->url->get('map/') ?>?otdel=<?=$otdel ?>" class="form-horizontal">
								<fieldset>
									<legend>Отблагодарить за создание игры</legend>
									<div class="form-group">
										<label class="col-xs-6 control-label">Сумма:</label>
										<div class="col-xs-6">
											<input type="text" name="donation" placeholder="зол." class="form-control input-sm">
										</div>
									</div>
									<div class="form-group">
										<label class="col-xs-6 control-label">Ваше пожелание:</label>
										<div class="col-xs-6">
											<input type="text" name="comment" maxlength="100" class="form-control input-sm">
										</div>
									</div>
									<div class="row">
										<div class="col-xs-6 push-xs-6">
											<button type="submit" class="btn btn-primary">Пожертвовать</button>
										</div>
									</div>
								</fieldset>
							</form>
						</div>
					</div>
					<br><br>
					<center>Последние 20 пожертвований:</center>
					<br>
					<table class="table">
						<thead>
							<tr>
								<th width="200">Игрок</th>
								<th width="200">Сумма</th>
								<th>Комментарий</th>
							</tr>
						</thead>
						<? foreach ($list as $l): ?>
							<tr>
								<td>
									<?=$l['username'] ?>
								</td>
								<td>
									<?=$l['money'] ?> зол.
								</td>
								<td>
									<?=$l['comment'] ?>
								</td>
							</tr>
						<? endforeach; ?>
					</table>
				<? elseif ($otdel == 2): ?>
					<h4 class="text-xs-center">Обмен Валюты</h4>
					Курс обмена: 1 пл. = 20 зол.
					<br><br>
					<div class="row">
						<div class="col-xs-6">
							<form method="POST" action="<?=$this->url->get('map/') ?>?otdel=<?=$otdel ?>" class="form-horizontal">
								<fieldset>
									<legend>Введите сколько платины вы хотели бы обменять</legend>
									<div class="form-group">
										<label class="col-xs-6 control-label">Сумма:</label>
										<div class="col-xs-6">
											<input type="text" name="exchange" placeholder="пл." class="form-control input-sm">
										</div>
									</div>
									<div class="row">
										<div class="col-xs-6 push-xs-6">
											<button type="submit" class="btn btn-primary">Обменять</button>
										</div>
									</div>
								</fieldset>
							</form>
						</div>
					</div>
				<? endif; ?>
			<? endif; ?>
		</div>
		<div class="clearfix"></div>
	</div>
</div>