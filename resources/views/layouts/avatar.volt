{{ partial('shared/city_header', ['title': 'Бесплатные образы']) }}
<table align='center' class='ltable' style='width:100%;'>
	<tr>
		<td valign="top" bgcolor=efdcb8 class="textblock">
			{% if message is not empty %}
				<p class="message alert-danger">{{ message }}</p>
			{% endif %}

			<center>
				<small><b>Внимание! Выбрав образ сейчас, Вы более не сможете его сменить!</b></small>
			</center>
			<br>

			<table width="100%">
				<tr>
					{% if user.level < 8 %}
						{% for g in 1..5 %}
							{% if !(g%6) %}
								</tr><tr>
							{% endif %}
							<td class="text-xs-center">
								<a href="javascript:;" onclick="confirmDialog('Подтвердите действие', 'Применить это образ?', 'load(\'/avatar/?setimg={{ g }}\')')">
								<img src="/images/avatar/obraz/{{ user.sex }}/<?=$g ?>.png">
								</a>
							</td>
						{% endfor %}
					{% else %}
						<td><center><b>У вас уже установлен образ. Сменить его вы сможете только в здании администрации.</b></center></td>
					{% endif %}
				</tr>
			</table>
		</td>
	</tr>
</table>