{{ partial('shared/city_header', ['title': 'Реферальная программа']) }}
<div class="textblock">
	<table cellspacing='0' cellpadding='0' class='ltable'>
		<tr>
			<td valign="top">
				<font color="red">Ваша реферальная ссылка (для регистраций) - </font>
				<font color="blue">
					<b><a href="http://{{ _SERVER['SERVER_NAME'] }}/?{{ user.id }}" target="_blank">http://{{ _SERVER['SERVER_NAME'] }}/?{{ user.id }}</a></b>
				</font>
				<br>
				{% if refers|length > 0 %}
					<font color=navy><b>Привлеченные вами игроки:</b></font>
					<table class="table">
						<thead>
							<tr>
								<th>Имя</th>
								<th>Уровень</th>
								<th>Статус</th>
							</tr>
						</thead>
						<tbody>
							{% for user in refers %}
								<tr>
									<td class="text-xs-center">{{ user['username'] }}</td>
									<td class="text-xs-center">{{ user['level'] }}</td>
									<td class="text-xs-center">
										{% if (time() - user['lpv']) < 180 %}
											<font color="green">OnLine</font>
										{% else %}
											<font color="red">OffLine</font>
										{% endif %}
									</td>
								</tr>
							{% endfor %}
						</tbody>
					</table>
				{% endif %}
			</td>
		</tr>
	</table>
</div>