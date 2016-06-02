{{ partial('shared/city_header', ['title': 'Список Друзей / Врагов']) }}
<div class="textblock">
	<div class="row">
		<div class="col-xs-12 text-xs-right">
			<a href="/pers/friends/"><img src='/images/images/refresh.gif' alt='Обновить'></a>
			<a href='/pers/'><img src='/images/images/back.gif' alt='Вернуться'></a>
		</div>
	</div>

	{% if list|length > 0 %}
		<h4>Список ваших Друзей/Врагов</h4>
		<table class="table">
			<thead>
				<tr>
					<th>Ник</th>
					<th>Комната</th>
					<th class="text-xs-center">Кем является</th>
					<th class="text-xs-center">Статус</th>
				</tr>
			</thead>
			<tbody>
				{% for user in list %}
					<tr>
						<td>
							<b>
								<img src="/images/rank/{{ user['rank'] }}.gif" alt="">
								{% if user['tribe'] %}
									<img src="/images/tribe/{{ user['tribe'] }}.gif" alt="">
								{% endif %}
								{{ user['username'] }}
							</b>
								[{{ user['level'] }}] <a href="/info/?id={{ user['friend_id'] }}" target="_blank"><img src='/images/images/inf.gif' alt="Информация о {{ user['username'] }}"></a>
						</td>
						<td>
							{{ _text('rooms', user['room']) }}
						</td>
						<td class="text-xs-center">
							{% if user['ignor'] == 0 %}
								<b><font color='green'>Друг</font></b>
							{% elseif user['ignor'] == 1 %}
								<b><font color='red'>Враг (игнор)</font></b>
							{% endif %}
						</td>
						<td class="text-xs-center">
							{% if user['rank'] == 100 %}
								<b><font color='red'>OffLine</font></b>
							{% else %}
								{% if time() - user['onlinetime'] <= 180 or user['rank'] == 60 %}
									<b><font color='green'>OnLine</font></b>
								{% else %}
									<b><font color='red'>OffLine</font></b>
								{% endif %}
							{% endif %}
						</td>
					</tr>
				{% endfor %}
			</tbody>
		</table>
		<br><br>
	{% endif %}

	{% if message is not empty %}
		<p class="message bg-danger">{{ message }}</p>
	{% endif %}
	<div class="row">
		<div class="col-xs-5">
			<form method="POST" action="/pers/friends/?act=add" class="form-horizontal">
				<fieldset>
					<legend>Добавить</legend>
					<div class="form-group row">
						<label class="col-xs-6 control-label">Введите ник:</label>
						<div class="col-xs-6">
							<input type="text" name="name" value="{{ request.getPost('name') }}" class="form-control input-sm">
						</div>
					</div>
					<div class="form-group row">
						<label class="col-xs-6 control-label">Кем будет являться:</label>
						<div class="col-xs-6">
							<select name="dr" class="form-control input-sm">
								<option value="0">Друг</option>
								<option value="1">Враг</option>
							</select>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-6 push-xs-6">
							<button type="submit" class="btn btn-primary">Добавить</button>
						</div>
					</div>
				</fieldset>
			</form>
		</div>
		<div class="col-xs-5 push-xs-2">
			<form method="POST" action="/pers/friends/?act=del" class="form-horizontal">
				<fieldset>
					<legend>Удалить</legend>
					<div class="form-group row">
						<label class="col-xs-6 control-label">Введите ник:</label>
						<div class="col-xs-6">
							<input type="text" name="name" value="{{ request.getPost('name') }}" class="form-control input-sm">
						</div>
					</div>
					<div class="row">
						<div class="col-xs-6 push-xs-6">
							<button type="submit" class="btn btn-primary">Удалить</button>
						</div>
					</div>
				</fieldset>
			</form>
		</div>
	</div>
</div>
