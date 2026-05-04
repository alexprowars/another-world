<? if ($message != ""): ?>
	<table width='100%' align='center'>
		<tr>
			<td align='center'><b style='COLOR: Red'><?=$message ?></b></td>
		</tr>
	</table>
<? endif; ?>
<? if ($result != ""): ?>
	<table width='100%' align='center'>
		<tr>
			<td align='center'><b style='COLOR: Green'><?=$result ?></b></td>
		</tr>
	</table>
<? else: ?>
	<script src='//www.google.com/recaptcha/api.js'></script>
	<style>
		.input {
			border: solid 1pt #B0B0B0;
			font-family: Verdana;
			font-size: 10px;
			color: #191970;
			MARGIN-BOTTOM: 2px;
			MARGIN-TOP: 1px;
		}
	</style>

	<form action='' method="post">
		<table width='100%' class="padding">
			<tr>
				<td width=50%>Никнейм персонажа: (<b style='COLOR: Red'>*</b>)</td>
				<td align=center>
					<input name='login' class=input maxlength=10 value='<?=$this->request->getPost('login') ?>' size='20'>
				</td>
			</tr>
			<tr>
				<td>Пароль: (<b style='COLOR: Red'>*</b>)</td>
				<td align=center>
					<input name='psw' type=password class=input maxlength=30 value='' size='20'></td>
			</tr>
			<tr>
				<td>Пароль повторно: (<b style='COLOR: Red'>*</b>)</td>
				<td align=center>
					<input name='conf_pass' type=password class=input value='' size='20'></td>
			</tr>

			<tr>
				<td>E-mail: (<b style='COLOR: Red'>*</b>)</td>
				<td align=center>
					<input name='email' class=input maxlength=40 value='<?=$this->request->getPost('email') ?>' size='20'>
				</td>
			</tr>

			<tr>
				<td>Реальное имя: (<b style='COLOR: Red'>*</b>)</td>
				<td align=center>
					<input name='name' class=input maxlength=11 value='<?=$this->request->getPost('name') ?>' size='20'>
				</td>
			</tr>
			<tr>
				<td>Дата рождения: (<b style='COLOR: Red'>*</b>)</td>
				<td align=center>
					<select name="day">
						<? for ($i = 1; $i <= 31; $i++): ?>
							<option value="<?=$i ?>" <?=($i == $this->request->getPost('day') ? 'selected' : '') ?>><?=$i ?></option>
						<? endfor; ?>
					</select>
					<select name="month">
						<? for ($i = 1; $i <= 12; $i++): ?>
							<option value="<?=$i ?>" <?=($i == $this->request->getPost('month') ? 'selected' : '') ?>><?=$i ?></option>
						<? endfor; ?>
					</select>
					<select name="year">
						<? for ($i = 1970; $i <= 2005; $i++): ?>
							<option value="<?=$i ?>" <?=($i == $this->request->getPost('year') ? 'selected' : '') ?>><?=$i ?></option>
						<? endfor; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Пол: (<b style='COLOR: Red'>*</b>)</td>
				<td align=center>
					<select name=sex>
						<option value=1 <?=($this->request->getPost('sex') == 1 ? "selected" : "") ?>>Мужской
						<option value=2 <?=($this->request->getPost('sex') == 2 ? "selected" : "") ?>>Женский
					</select>
				</td>
			</tr>
			<tr>
				<td>Девиз:</td>
				<td align=center>
					<input name='deviz' class=input style='WIDTH: 130px' value='<?=$this->request->getPost('deviz') ?>' size='20'>
				</td>
			</tr>
			<tr>
				<td>Город: (желательно указать)</td>
				<td align=center>
					<input name='city' class=input style='WIDTH: 130px' maxlength=11 value='<?=$this->request->getPost('city') ?>' size='20'>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center"><div class="g-recaptcha" data-sitekey="6LfllwUTAAAAAOOfDwaj2jiJYl8KvdgT8ZA6q1lc"></div></td>
			</tr>
			<tr>
				<td colspan=2><input type=hidden name=law value=0><input type=checkbox name=law value=1> Я обязуюсь соблюдать
					<a href='index.php?type=law' target=_blank>законы</a> онлайн игры <b>Another World</b></td>
			</tr>
			<tr>
				<td colspan=2><input type=hidden name=soglash value=0><input type=checkbox name=soglash value=1> Я прочитал
					<a href='index.php?type=soglas' target=_blank>Пользовательское соглашение</a> и согласен с условиями
				</td>
			</tr>
			<tr>
				<td colspan=2>Игровой мир может быть изменен или дополнен без вашего согласия.</td>
			</tr>
			<tr>
				<td align=center colspan='2'><input type=submit name=register class=input value='Регистрация'></td>
			</tr>
		</table>
	</form>
<? endif; ?>