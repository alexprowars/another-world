<div class="textblock">
	{% if message is not empty %}
		<p class="message bg-danger">{{ message }}</p>
	{% endif %}

	<div class="row">
		<div class="col-xs-12 text-xs-center">
			<div class="" style="background: #930407;padding:2px;">
				<font color=FFFFFF><b>Физические параметры [<u>{{ user.s_updates }}</u>]</b></font>
			</div>
		</div>
	</div>
	<table class="table vertical">
		<thead>
			<tr>
				<th></th>
				<th>Параметр</th>
				<th class="text-xs-center">Текущая прокачка</th>
				<th class="text-xs-center">Действие</th>
			</tr>
		</thead>
		<tbody>
			{% for code, title in _text('game', 'stats') %}
				<tr>
					<td>
						<img src="/images/help.gif" class="tooltip text" data-content="<b>{{ title }}</b><br>{{ _text('game', 'stats-info', code) }}">
					</td>
					<td class="col-xs-4 text-left">
						<?=$title ?>
					</td>
					<td class="col-xs-4 text-xs-center">
						<b>{{ user.readAttribute(code) }}</b> очков
					</td>
					<td class="col-xs-4 text-xs-center">
						{% if user.s_updates > 0 %}
							<a href="javascript:;" title='Увеличить' onclick="confirmDialog('Подтвердите действие', 'Увеличить физический параметр {{ title }}?', 'load(\'/pers/updates/?update={{ code }}\')')">
								<input class="btn btn-success" type="button" value="прокачать">
							</a>
						{% else %}
							нет очков
						{% endif %}
					</td>
				</tr>
			{% endfor %}
		</tbody>
	</table>
</div>