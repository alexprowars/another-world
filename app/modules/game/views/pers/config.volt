<div class="textblock">
	<form action="<?=$this->url->get('pers/config/') ?>" method="post" class="form-horizontal">
		<fieldset>
			<legend>Настройки статусов</legend>
			<div class="form-group row">
				<label for="formStatus" class="col-xs-6 control-label">Ваш статус</label>
				<div class="col-xs-6">
					<select name="update_status" class="form-control input-sm">
						<option value=0 <? if ($this->user->status == 0) print"selected"; ?>>Онлайн</option>
						<option value=1 <? if ($this->user->status == 1) print"selected"; ?>>Подерусь</option>
						<option value=2 <? if ($this->user->status == 2) print"selected"; ?>>Занят</option>
						<option value=3 <? if ($this->user->status == 3) print"selected"; ?>>Отошел</option>
						<option value=4 <? if ($this->user->status == 4) print"selected"; ?>>Не беспокоить</option>
					</select>
				</div>
			</div>
			<!--<div class="form-group">
				<label for="formStatus" class="col-xs-6 control-label">Показывать список онлайна</label>
				<div class="col-xs-6">
					<select name="showonline" class="form-control input-sm">
						<option value=1 <? if ($this->cookies->get('chat_showonline')->getValue() == 1) print"selected"; ?>>Да
						<option value=0 <? if ($this->cookies->get('chat_showonline')->getValue() == 0) print"selected"; ?>>Нет
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="formStatus" class="col-xs-6 control-label">Показывать статусы игроков</label>
				<div class="col-xs-6">
					<select name="showonline" class="form-control input-sm">
						<option value=1 <? if ($this->cookies->get('chat_showonline')->getValue() == 1) print"selected"; ?>>Да
						<option value=0 <? if ($this->cookies->get('chat_showonline')->getValue() == 0) print"selected"; ?>>Нет
					</select>
				</div>
			</div>-->
			<div class="row">
				<div class="col-xs-6 push-xs-6">
					<button type="submit" class="btn btn-primary" name="mystat">Применить</button>
				</div>
			</div>
		</fieldset>
	</form>
	<!--<br>
	<form action="" method="post" class="form-horizontal">
		<fieldset>
			<legend>Настройки чата</legend>
			<div class="form-group">
				<label for="formStatus" class="col-xs-6 control-label">Транслит сообщений</label>
				<div class="col-xs-6">
					<select name="translit" class="form-control input-sm">
						<option value=1 <? if ($this->cookies->get('translit')->getValue() == 1) print"selected"; ?>>Да
						<option value=0 <? if (!$this->cookies->get('translit')->getValue()) print"selected"; ?>>Нет
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="formStatus" class="col-xs-6 control-label">Показывать сообщения адресованные только мне</label>
				<div class="col-xs-6">
					<select name="sysmsg" class="form-control input-sm">
						<option value=1 <? if ($this->cookies->get('sysmsg')->getValue() == 1) print"selected"; ?>>Да
						<option value=0 <? if ($this->cookies->get('sysmsg')->getValue() == 0) print"selected"; ?>>Нет
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="formStatus" class="col-xs-6 control-label">Показывать системные сообщения</label>
				<div class="col-xs-6">
					<select name="sysmsg1" class="form-control input-sm">
						<option value=1 <? if ($this->cookies->get('sysmsg1')->getValue() == 1) print"selected"; ?>>Да
						<option value=0 <? if ($this->cookies->get('sysmsg1')->getValue() == 0) print"selected"; ?>>Нет
					</select>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-6 col-xs-push-6">
					<button type="submit" class="btn btn-primary" name="mychat">Применить</button>
				</div>
			</div>
		</fieldset>
	</form>-->
</div>