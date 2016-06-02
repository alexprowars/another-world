{% if alert is defined and alert is not empty %}
	<script type="text/javascript">
		$(document).ready(function()
		{
			alert('{{ alert }}');

			load('?set=battle');
		});
	</script>
{% endif %}

{{ partial('shared/city_header', ['title': 'Поединки на арене']) }}
<div class="textblock offers">
	<div class="row">
		<div class="col-xs-12 text-xs-right">
			<a href="{{ url('battle/') }}?battle_type={{ battleType }}"><img src='/images/images/refresh.gif' alt='Обновить'></a>
			<a href="{{ url('map/') }}"><img src='/images/images/back.gif' alt='Вернуться'></a>
		</div>
	</div>

	{% if message is not empty %}
		<p class="message alert-danger">{{ message }}</p>
	{% endif %}

	{% if user.travma > time() %}
		<center><b style='COLOR: Red'>Вы не можете драться, т.к. тяжело травмированы!<br>Вам необходим отдых!</b></center>
	{% elseif user.room != 1 and user.room != 2 and user.room != 3 and user.room != 4 %}
		<center><b style='COLOR: Red'>Вы выбрали не совсем удачное место для проведения поединка!<br><br><a href="{{ url('battle/') }}?teleport=Y" class="btn btn-primary">Переместиться в Арену!</a></b></center>
	{% else %}
		<br>
		{% if battleId == 0 %}
			<table class="type">
				<tr>
					<td class="col-xs-4 text-xs-center">
						<b><a class="{{ battleType == 1 ? 'active' : '' }}" href="{{ url('battle/') }}?battle_type=1">PvP</a></b>
					</td>
					<td class="col-xs-4 text-xs-center">
						<b><a class="{{ battleType == 2 ? 'active' : '' }}" href="{{ url('battle/') }}?battle_type=2">Групповые</a></b>
					</td>
					<td class="col-xs-4 text-xs-center">
						<b><a class="{{ battleType == 3 ? 'active' : '' }}" href="{{ url('battle/') }}?battle_type=3">Хаотические</a></b>
					</td>
				</tr>
			</table>
		{% endif %}
		{{ list }}
	{% endif %}
</div>