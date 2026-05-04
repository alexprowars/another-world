@use(App\Facades\Vars)

<td width="245" valign=top>
	@if($user->experience == 0)
		<script>setTimeout("parent.noob_text()", 30000);</script>
	@endif
	<table class="tmain personBlock">
		<tr>
			<td colspan="2" style="width:245px;">
				<div class="personName">
					<div id="person"></div>
					<script type="text/javascript">
						$('#person').html(show_inf('{{ $user->nickname }}', '{{ $user->id }}', '{{ $user->level }}', '{{ $user->rank }}', '{{ $user->tribe_id }}'));
					</script>
				</div>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<div>
					<div class="dlfr">
						<table id="slotable">
							<tr>
								<td>
									<div class="bdg stbox">
										<div id="life" class="g_line" style="width:{{ $parse['w_h'] }}%">
											<img src="/images/main/empty.gif" width="1" height="10"></div>
									</div>
								</td>
								<td align="right" class="fntc"><span id="text_life">{{ $user->hp_now }}</span></td>
								<td class="intf">|</td>
								<td class="minf">{{ $user->hp_max }}</td>
							</tr>
							<tr>
								<td>
									<div class="bdg stbox">
										<div id="mana" class="b_line" style="width:{{ $parse['w_e'] }}%">
											<img src="/images/main/empty.gif" width="1" height="10"></div>
									</div>
								</td>
								<td align="right" class="fntc"><span id="text_mana">{{ $user->energy_now }}</span></td>
								<td class="intf">|</td>
								<td class="minf">{{ $user->energy_max }}</td>
							</tr>
							<tr>
								<td>
									<div class="bdg stbox">
										<div id="ustal" class="h_line" style="width:{{ $parse['w_u'] }}%">
											<img src="/images/main/empty.gif" width="1" height="10"></div>
									</div>
								</td>
								<td align="right" class="fntc">{{ $user->ustal_now }}</td>
								<td class="intf">|</td>
								<td class="minf">{{ $user->ustal_max }}</td>
							</tr>
						</table>
					</div>
					<div>
						<table class="person_slots" style="width:240px; height:260px;border:solid #e1d0b0 1.5pt;" bgcolor=bfbfbf>
							<tr>
								<td width="60" height="260" valign="top" class="left">
									{!! $slots['slot_1']->view($isEdit) !!}
									{!! $slots['slot_21']->view($isEdit) !!}
									{!! $slots['slot_2']->view($isEdit) !!}
									{!! $slots['slot_3']->view($isEdit) !!}
									{!! $slots['slot_4']->view($isEdit) !!}
									{!! $slots['slot_9']->view($isEdit) !!}
									{!! $slots['slot_6']->view($isEdit) !!}
									{!! $slots['slot_7']->view($isEdit) !!}
									{!! $slots['slot_8']->view($isEdit) !!}
								</td>
								<td width="120" valign="top">
									<a href="/avatar/"><img src="/images/avatar/{{ $user->obraz }}.png" width="120" height="220" alt="{{ $user->nickname }}"></a>
									<div style="height:20px;"></div>
									<div class="text-xs-center">
										{!! $slots['slot_17']->view($isEdit) !!}
										&nbsp;&nbsp;&nbsp;
										{!! $slots['slot_18']->view($isEdit) !!}
									</div>
								</td>
								<td width="60" height="260" valign="top" class="right">
									{!! $slots['slot_14']->view($isEdit) !!}
									{!! $slots['slot_15']->view($isEdit) !!}
									{!! $slots['slot_5']->view($isEdit) !!}
									{!! $slots['slot_10']->view($isEdit) !!}
									{!! $slots['slot_11']->view($isEdit) !!}
									{!! $slots['slot_12']->view($isEdit) !!}
									{!! $slots['slot_22']->view($isEdit) !!}
									{!! $slots['slot_13']->view($isEdit) !!}
								</td>
							</tr>
						</table>
					</div>
				</div>
			</td>
		</tr>
	</table>
	<script type="text/javascript">
		var my_proff, my_level, my_strength, my_dex, my_agility, my_vitality, my_razum, type;
		my_proff = {{ $user->profession }};
		my_level = {{ $user->level }};
		my_strength = {{ $user->strength }};
		my_dex = {{ $user->dex }};
		my_agility = {{ $user->agility }};
		my_vitality = {{ $user->vitality }};
		my_razum = {{ $user->razum }};
		type = 1;
	</script>
</td>

<td width="200" valign="top" style="padding:0 10px;">
	<table class="parameters" style="width:100%;border:solid #e1d0b0 1.5pt;" bgcolor=efdcb8>
		<tr>
			<td class="tc_b">Уровень:</td>
			<td class="tc_b" align="right">{{ $user->level }} [{{ $up->up ?? 0 }}]</td>
		</tr>
		<tr>
			<td class="tc_b" width="30%">Опыт:</td>
			<td class="tc_b" align="right" width="70%">
				{{ $user->experience }}
			</td>
		</tr>
		<tr>
			<td class="tc_b" width="30%">До уровня:</td>
			<td class="tc_b" align="right" width="70%" title="Осталось {{ ($up->exp ?? 0) - $user->experience }} очков опыта">
				{{ $up->exp ?? 0 }}
			</td>
		</tr>
		<tr>
			<td class="tc_b">Профессия:</td>
			<td class="tc_b" align="right">
				<b>{{ $user->profession }}</b>
			</td>
		</tr>
		@if($user->tribe)
			<tr>
				<td class="tc_b">Клан:</td>
				<td class="tc_b" align="right">
					<small>{{ $user->tribe->name }}</small>
				</td>
			</tr>
		@endif
		<tr>
			<td colspan="2">
				<hr/>
			</td>
		</tr>
		<tr>
			<td class="tc_r">Золото:</td>
			<td class="tc_r" align="right">{{ $user->moneys }}</td>
		</tr>
		<tr>
			<td class="tc_r">Платина:</td>
			<td class="tc_r" align="right">{{ $user->credits }}</td>
		</tr>
		<tr>
			<td colspan="2">
				<hr/>
			</td>
		</tr>
		@foreach(Vars::getStats() as $stat)
			<tr>
				<td class="tc_dbl">{{ __('main.stats.' .$stat) }}</td>
				<td align="right">
					<a href="#" style="font-size:11px;color:#E03504;" class="tooltip text" data-content="<table width=120><tr><td width=50% align=left><b>Сила:</b></td><td width=50% align=right> parse[code] </td></tr><tr><td width=50% align=left><b>Своя:</b></td><td width=50% align=right> parse['~'~code] </td></tr><tr><td width=50% align=left><b>Эффекты:</b></td><td width=50% align=right>{{ $user->getAttribute($stat) }} - parse['~'~code] </td></tr></table>">{{ $user->{$stat} }}</a>
				</td>
			</tr>
		@endforeach
		@if($user->updates)
			<tr>
				<td colspan="2" height="25" align="center">
					<a href='{{ url("pers/updates/") }}'>
						<small><font color=red>Свободные статы!</font></small>
					</a>
				</td>
			</tr>
		@endif
		{% if parse['otravl'] > 0 %}
			<tr>
				<td colspan="2"><hr/></td>
			</tr>
			<tr>
				<td>Отравление</td>
				<td align="right"><font color="{% if parse['otravl'] < 25 %}green{% elseif parse['otravl'] < 50 %}yellow{% else %}red{% endif %}">{ $parse['otravl'] }%</font></td>
			</tr>
		{% endif %}
		@if($isEdit)
			<tr>
				<td colspan="2">
					<hr/>
				</td>
			</tr>
			<tr>
				<td>Физ.урон</td>
				<td align="right">
					<nobr>
						<small>{{ ($user->strength / 3 + $user->min) }} - {{ (1 + $user->strength / 1.5 + $user->max) }}</small>
					</nobr>
				</td>
			</tr>
			<tr>
				<td>Маг.урон</td>
				<td align="right">
					<nobr>
						<small>{{ ($user->razum / 1.5) }} - {{ (1 + $user->razum) }}</small>
					</nobr>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center">Броня:</td>
			</tr>
			<tr>
				<td colspan="2" align="center">
					<nobr>
						<small>
							<b title="Броня головы">{{ $user->br1 }}</b>/
							<b title="Броня груди">{{ $user->br2 }}</b>/
							<b title="Броня живота">{{ $user->br3 }}</b>/
							<b title="Броня пояса">{{ $user->br4 }}</b>/
							<b title="Броня ног">{{ $user->br5 }}</b>/
						</small>
					</nobr>
				</td>
			</tr>
			<tr>
				<td width="40">Крит:</td>
				<td align="right">
					<small><b>{{ $user->krit }}</b></small>
				</td>
			</tr>
			<tr>
				<td width="40">Мощн.крита:</td>
				<td align="right">
					<small><b>{{ $user->mkrit }}</b></small>
				</td>
			</tr>
			<tr>
				<td width="40">Антикрит:</td>
				<td align="right">
					<small><b>{{ $user->unkrit }}</b></small>
				</td>
			</tr>
			<tr>
				<td width="40">Уворот:</td>
				<td align="right">
					<small><b>{{ $user->uv }}</b></small>
				</td>
			</tr>
			<tr>
				<td width="40">Антиуворот:</td>
				<td align="right">
					<small><b>{{ $user->unuv }}</b></small>
				</td>
			</tr>
			<tr>
				<td width="40">Проб.блока:</td>
				<td align="right">
					<small><b>{{ $user->pblock }}</b></small>
				</td>
			</tr>
			<tr>
				<td width="40">Мощн.блока:</td>
				<td align="right">
					<small><b>{{ $user->mblock }}</b></small>
				</td>
			</tr>
			<tr>
				<td width="40">Проб.брони:</td>
				<td align="right">
					<small><b>{{ $user->pbr }}</b></small>
				</td>
			</tr>
			<tr>
				<td width="40">Крепк.брони:</td>
				<td align="right">
					<small><b>{{ $user->kbr }}</b></small>
				</td>
			</tr>
		@endif
		{ $parse['actions'] }
	</table>
</td>