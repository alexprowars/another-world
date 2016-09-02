{{ partial('shared/city_header', ['title': 'Центр Занятости', 'credits': user.credits]) }}

<div class="textblock">
	<div class="row">
		<div class="col-xs-12 text-xs-right">
			<a href="{{ url('map/') }}"><img src='/images/images/refresh.gif' alt='Обновить'></a>
			<a href="{{ url('map/') }}?refer=16"><img src='/images/images/back.gif' alt='Вернуться'></a>
		</div>
	</div>

	{% if message is not empty %}
		<p class="message bg-danger">{{ message }}</p>
	{% endif %}

	{% if user.r_time > time() %}
		<div class="row">
			<div class="col-xs-12 text-xs-center">
				<font color=red><b>Оставшееся время работы:</b></font>
				<span id=know style='COLOR: red; FONT-WEIGHT: Bold; TEXT-DECORATION: Underline'></span>
			</div>
		</div>
		<script>ShowTime('know', {{ user.r_time - time() }});</script>
	{% else %}
		{% for type in types %}
			<fieldset>
				<legend>{{ type['name'] }} (требует {{ type['name'] }} уровень и {{ type['activity'] }} ед. активности за час)</legend>
				<table class="table">
					<thead>
						<tr>
							<th width="18" class="text-xs-center">№</th>
							<th>Наименование</th>
							<th width="150" class="text-xs-center">Срок работы</th>
							<th width="160" class="text-xs-center">Зарплата</th>
							<th class="text-xs-center" width="120">Действие</th>
						</tr>
					</thead>
					<tbody>
						{% set i = 0 %}
						{% for work in works if work['type_id'] == type['id'] %}
							<tr>
								<td class="text-xs-center"><b>{{ i + 1 }}</b></td>
								<td><b>{{ work['title'] }}</b></td>
								<td class="text-xs-center"><b>{{ (work['time'] / 60)|round(1) }} мин.</b></td>
								<td class="text-xs-center"><b>{{ work['price'] }} зол.</b></td>
								<td class="text-xs-center">
									{% if user.r_time < time() %}
										<a href="javascript:;" class="btn btn-primary" onclick="confirmDialog('Подтвердите действие', 'Вы действительно хотите получить данную работу?', 'load(\'{{ url('map/') }}?get={{ work['id'] }}\')')">Работать</a>
									{% endif %}
								</td>
							</tr>
							{% set i = i + 1 %}
						{% endfor %}
					</tbody>
				</table>
			</fieldset>
			<br>
		{% endfor %}
	{% endif %}
</div>