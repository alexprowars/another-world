{{ partial('shared/city_header', ['title': 'Тренировочный зал для новичков']) }}
<div class="textblock">
	{% if message is not empty %}
		<p class="message alert-danger">{{ message }}</p>
	{% endif %}
	<table width=100%>
		<tr>
			<td width=600 valign=top>
				<TABLE cellpadding=0>
					<tr>
						<TD valign=middle style="padding:5px 5px 5px 0;">
							<SCRIPT language=JavaScript>
								show_inf('{{ user.username }}', '{{ user.id }}', '{{ user.level }}', '{{ user.rank }}', '{{ user.tribe }}');
							</SCRIPT>
						</TD>
						<TD valign=middle>
							<table align=center>
								<tr>
									<td width="200" title="Уровень жизни: {{ user.hp_now }}/{{ user.hp_max }}" align="left" valign="bottom">
										<img src=/images/images/vault/navigation/hp/_helth.gif width='10' height=10 alt=''><img src=/images/images/vault/navigation/hp/helth.gif height='10' width="{{ (getWidth(user.hp_now, user.hp_max) / 100 * 170)|ceil }}" alt='Уровень жизни: {{ user.hp_now }}/{{ user.hp_max }}'><img src="/images/images/vault/navigation/hp/_helth_.gif" alt="">
									</td>
								</tr>
							</table>
						</TD>
						<TD valign=middle>
							<FONT COLOR=RED><B>{{ user.hp_now }} / {{ user.hp_max }}</B></FONT>
						</TD>
					</TR>
				</TABLE>
			</td>
			<td align=right valign=top>
				<a href="/map/"><img src='/images/images/refresh.gif' alt='Обновить'></a>
				{% if user.room == 2 %}
					<a href='/map/?refer=2'><img src='/images/images/back.gif' alt='Вернуться'></a>
				{% endif %}
			</td>
		</tr>
	</table>
	<br>
	{% if bots|length == 0 %}
		<div class="alert alert-danger">Вам больше не нужно тренироваться</div>
	{% else %}
		<div class="row">
			<div class="col-xs-8">
				<b>Как вести себя в поединке.</b><br><br>
				<b>1.</b> Если у вас не стреляющие оружие (не лук) и соперник находится в нескольких ячеек от вас, чтобы подойти, вам необходимо нажать
				<u>правой кнопокй мыши</u> на ближайшую к себе ячейку, после чего выдет контекстное меню, жмите
				<b>идти</b>. . .и так пока не встретитесь с соперником.<br>
				<b>2.</b> Подойдя к сопернику, чтобы нанести удар, нужно нажать кнопку
				<b>карта/бой</b>, в появившемся окне вы расставлете удары и блоки на веше усмотрение, после расстановки блоков и ударов, нужно нажать кнопку
				<b>ударить</b><br>
				<b>3.</b> Если у вас в руках лук, вы можете стрелять на расстоянии 3 ячеек. Чтобы стрельнуть, также нажимаете правой кнопкой мыши на соперника и жмете
				<b>стрельнуть</b>, Если соперник уже подошел к вам вплотную то переходим на кнопку
				<b>карта/бой</b> и уже сражаемся таким способом.<br>
				<b>4.</b> Если же вы в бою против 2 соперников и они оба рядом стоят, можете бить их поочереди, путем нажатия кнопки
				<b>сменить</b>.
			</div>
			<div class="col-xs-4 text-xs-center">
				<b>Тренировочные Боты</b>
				<HR color=silver>
				{% for tl in bots %}
					<a href="javascript:fightTo('{{ tl['id'] }}')"><b>{{ tl['username'] }}</b></a> [{{ tl['level'] }}]<br>
				{% endfor %}
			</div>
		</div>
	{% endif %}
</div>