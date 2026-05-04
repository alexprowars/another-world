<div class="error"><?=$message ?></div>
<form action="/index/reminder/" method="post" id="lostForm" class="form">
	<table class="table">
		<tr>
			<th>Введите ваш Email, который вы указали при регистрации. При нажатии на кнопку "Получить пароль" на ваш e-mail будет выслана ссылка на новый пароль.</th>
		</tr>
		<tr>
			<th>Ваш Email: <input type="text" name="email"/></th>
		</tr>
		<tr>
			<th><input name="submit" type="submit" value="Выслать пароль"/></th>
		</tr>
	</table>
</form>