<table width="100%" align="center" class="battle">
	<tr>
		<td align="left" valign="top" width="240">
			<div id="leftContent"></div>
		</td>
		<td align="center" valign="top" style="padding:0 10px;">
			<table width="100%" class="impackForm">
				<tr>
					<th class="text-xs-center">Бой</th>
				</tr>
				<tr>
					<td class="text-xs-center">
						<div id="msg"></div>
						<div id="centerContent"></div>
						<div id="indicatorLoad" style="display:none;"><img src="/images/refresh.gif" alt=""></div>
						<table class="table actions">
							<tr>
								<td class="text-xs-center"><input type="button" value='Ударить' class="standbut" onClick="gofight()"></td>
								<td class="text-xs-center"><input type="button" value='Сменить' onclick='Spisok();' class="standbut"></td>
								<td class="text-xs-center" id="refresh_b"><input type="button" value='Обновить' onclick='loaderRefresh();' class="standbut"></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<div id="usersContent"></div>
			<div id="centerInfo" style="display:block;"></div>
			<div class="text-xs-center timeout">
				До тайм-аута: <b id='timeout'></b>
				<HR color=e2e0e0>
				<b>Полный лог боя <a href="{{ url('logs/') }}?log={{ user.battle }}" target="_blank"> тут</a></b>
				<HR color=e2e0e0>
			</div>
		</td>
		<td align="right" valign="top" width="240">
			<div id="rightContent"></div>
		</td>
	</tr>

</table>
<table class="table">
	<tr>
		<td colspan="3">
			<div id="logsContent" align="left"></div>
		</td>
	</tr>
</table>

<input type="hidden" id="lastLogId" value="-1"/>
<input type="hidden" id="logsCount" value="0"/>
<input type="hidden" id="enemyId" value="0"/>
<input type="hidden" id="userId" value="0"/>
<input type="hidden" id="rightData" value="0"/>
<input type="hidden" id="last_action" value=""/>

<script type="text/javascript">
	loaderRefresh();
</script>