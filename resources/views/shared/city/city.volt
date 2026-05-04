<table class="table np">
	<tr>
		{{ user.getPersonBlock() }}
		<td valign="top">
			{% if message is not empty %}
				<p class="message alert-danger">{{ message }}</p>
			{% endif %}
			{{ partial('shared/person_menu') }}
			<div class="textblock">
				<table width=100%>
					<tr>
						<td colspan="2">
							<center><u>Для поединка зайдите внутрь арены</u></center><br>
						</td>
					</tr>
					<tr>
						<td valign=center colspan=2 align=center>
							<a href="{{ url('map/') }}?room=23" class="standbut">Уйти с арены</a>
						</td>
					</tr>
					<tr>
						<td align=center width=50%>
							<b>Боевая комната</b><br>
							<small>[Уровень: <u>0</u>-15]</small>
							<br>
							{% if room_1_members > 0 %}
								Воинов: {{ room_1_members }}
							{% else %}
								Пустует
							{% endif %}
							<br>
							<a href="{{ url('map/') }}?room=1" class="standbut">Вход</a>
						</td>
						<td align=center>
							<b>Тренировочный зал</b><br>
							<small>[Уровень: <u>0</u>-15]</small>
							<br>
							{% if room_2_members > 0 %}
								Воинов: {{ room_2_members }}
							{% else %}
								Пустует
							{% endif %}
							<br>
							<a href="{{ url('map/') }}?room=2" class="standbut">Вход</a>
						</td>
					</tr>
					<tr>
						<td align=center COLSPAN=2>
							<b>Больница</b><br>
							<small>[портал в больницу]</small>
							<br>
							<a href="{{ url('map/') }}?room=8" class="standbut">Вход</a>
						</td>
					</tr>
				</table>
			</div>
		</td>
	</tr>
</table>