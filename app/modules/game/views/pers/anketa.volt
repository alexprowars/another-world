<div class="textblock">
	<? if (!empty($message)): ?>
		<p class="message bg-danger"><?=$message ?></p>
	<? endif; ?>

	<form method="post" action="/pers/anketa/" class="form-horizontal">
		<input type="hidden" name="changeinfo" value="Y">
		<fieldset>
			<legend>Редактирование анкеты</legend>
			<div class="form-group row">
				<label for="formStatus" class="col-xs-6 control-label">Имя:</label>
				<div class="col-xs-6">
					<input name="realname" value="<?= $anketa['name'] ?>" class="form-control input-sm">
				</div>
			</div>
			<div class="form-group row">
				<label for="formStatus" class="col-xs-6 control-label">Город:</label>
				<div class="col-xs-6">
					<input name="city" value="<?= $anketa['city'] ?>" class="form-control input-sm">
				</div>
			</div>
			<div class="form-group row">
				<label for="formStatus" class="col-xs-6 control-label">Немного о себе:</label>
				<div class="col-xs-6">
					<textarea name="about" rows="6" cols="25" class="form-control input-sm"><?=stripslashes($anketa['about']) ?></textarea>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-6 push-xs-6">
					<button type="submit" class="btn btn-primary">Сохранить изменения</button>
				</div>
			</div>
		</fieldset>
	</form>
	<br>
	<form action=/pers/anketa/?changepass=Y" method="post" class="form-horizontal">
		<input type="hidden" name="changepass" value="Y">
		<fieldset>
			<legend>Сменить пароль</legend>
			<div class="form-group row">
				<label for="formStatus" class="col-xs-6 control-label">Старый пароль:</label>
				<div class="col-xs-6">
					<input type="password" name="old_pass" value="" class="form-control input-sm">
				</div>
			</div>
			<div class="form-group row">
				<label for="formStatus" class="col-xs-6 control-label">Новый пароль:</label>
				<div class="col-xs-6">
					<input type="password"  name="new_pass" value="" class="form-control input-sm">
				</div>
			</div>
			<div class="form-group row">
				<label for="formStatus" class="col-xs-6 control-label">Подтверждение:</label>
				<div class="col-xs-6">
					<input type="password"  name="conf_new_pass" value="" class="form-control input-sm">
				</div>
			</div>
			<div class="row">
				<div class="col-xs-6 push-xs-6">
					<button type="submit" class="btn btn-primary">Изменить</button>
				</div>
			</div>
		</fieldset>
	</form>
	<br>
	<form action="/pers/anketa/" method="post" class="form-horizontal">
		<input type="hidden" name="changemail" value="Y">
		<fieldset>
			<legend>Сменить e-mail</legend>
			<div class="form-group row">
				<label for="formStatus" class="col-xs-6 control-label">Старый e-mail:</label>
				<div class="col-xs-6">
					<input type="text" name="old_email" value="" class="form-control input-sm">
				</div>
			</div>
			<div class="form-group row">
				<label for="formStatus" class="col-xs-6 control-label">Новый e-mail:</label>
				<div class="col-xs-6">
					<input type="text"  name="new_email" value="" class="form-control input-sm">
				</div>
			</div>
			<div class="row row">
				<div class="col-xs-6 push-xs-6">
					<button type="submit" class="btn btn-primary">Изменить</button>
				</div>
			</div>
		</fieldset>
	</form>
</div>