{{ partial('shared/city_header', ['title': 'Больница', 'credits': user.credits, 'f_credits': user.f_credits]) }}

<div class="textblock">
	<div class="row">
		<div class="col-xs-12 text-xs-right">
			<a href="{{ url('map/') }}"><img src='/images/images/refresh.gif' alt='Обновить'></a>
			{% if user.r_time == 0 %}
				<a href="{{ url('map/') }}?refer=8"><img src='/images/images/back.gif' alt='Вернуться'></a>
			{% endif %}
		</div>
	</div>
	<div class="clearfix"></div>

	<table border=0 cellspacing=0 cellpadding=5 width=100% bordercolor=silver>
		<tr>
			<td align=center>
				{% if user.r_time == 0 and time != 0 %}
					Вы можете подлечиться в нашей больнице.<br>
					Уровень жизни: <b><u>{{ user.hp_now }}</u></b> ед. из <b><u>{{ user.hp_max }}</u></b> ед.<br>
					Курс лечения займёт займёт: <b>{{ time|pretty_time }}</b>
					<br><br>
					<a href="{{ url('map/') }}?use=Y" class="btn btn-primary">Подлечиться</a>
				{% elseif user.r_time == 0 and time == 0 %}
					<b>Извините, но у нас Вам делать нечего, Вы абсолютно здоровы!</b>
				{% elseif user.r_time > 0 and time > 0 %}
					<table cellspacing=0 cellpadding=3>
						<tr>
							<td>Ещё лечиться:</td>
							<td id=ambulance style='FONT-WEIGHT: Bold'>
								<script language=JavaScript>ShowTime('ambulance', '{{ time }}', 1);</script>
							</td>
						</tr>
					</table>
				{% endif %}
				{% if user.travma > time() %}
					<br><br>Вы можете вылечить свои травмы у нас, конечно маленько дороже чем у лекарей.<br><br>
					<a href="{{ url('map/') }}?puse=Y" class="btn btn-primary">Вылечить травму за 200 зол.</a>
				{% endif %}
			</td>
		</tr>
	</table>
</div>