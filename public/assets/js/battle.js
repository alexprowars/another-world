var impact_kick = 0;
var impact_block = 0;
var is_user = '';
var time;
var priems;
var auto_udar = 0;
var bat_users;
var spis;
var smena = 0;
var b_finish = 0;

var loader;

function loaderRefresh()
{
	refresh();
	clearTimeout(loader);
	loader = setTimeout(loaderRefresh, 45000);
}


function refresh()
{
	if (b_finish == 1)
		return;

	$('#indicatorLoad').show();
	$('#refresh_b').hide();

	var lastLogId = $('#lastLogId').val();

	if (!lastLogId)
		lastLogId = 0;

	$.ajax({
		'url': '/battle/?mode=ajax&lastLogId=' + lastLogId + '&smena=' + smena,
		'type': 'get',
		'dataType': 'json',
		'cache': false,
		'success': function(data)
		{
			actionRefresh(data);
		},
		error: function()
		{
			alert('Произошла ошибка при получении ответа от сервера');
			window.location.href = '/battle/';
		}
	});
}

function UsePriem(id)
{
	$.ajax({
		'url': '/battle/?mode=ajax&use_priem=' + id,
		'type': 'get',
		'dataType': 'json',
		'cache': false,
		'success': function(data)
		{
			actionRefresh(data);

			clearTimeout(loader);
			loader = setTimeout(loaderRefresh, 45000);
		},
		error: function()
		{
			alert('Произошла ошибка при получении ответа от сервера');
			window.location.href = '/battle/';
		}
	});
}

function UseMagic()
{
	$.ajax({
		'url': '/battle/?mode=ajax',
		'type': 'post',
		'data': $('form[name=mains]').serialize(),
		'dataType': 'json',
		'cache': false,
		'success': function(data)
		{
			actionRefresh(data);

			clearTimeout(loader);
			loader = setTimeout(loaderRefresh, 45000);
		},
		error: function()
		{
			alert('Произошла ошибка при получении ответа от сервера');
			window.location.href = '/battle/';
		}
	});
}

function actionRefresh(res)
{
	var lastLogId = $('#lastLogId');
	var enemyId = $('#enemyId');
	var userId = $('#userId');
	var rightData = $('#rightData');
	var centerContent = $('#centerContent');
	var battle_action = $('#battle_action');
	var last_action = $('#last_action');

	if (res.center.action == 'refresh')
	{
		loaderRefresh();
		return;
	}

	is_user = res.left.user.data.login;

	if (res.m)
		$('#msg').html('<center><font color=red><b>' + res.m + '</b></font></center>');
	else
		$('#msg').html('');

	$('#leftContent').html(DrawUser(res.left.user));

	userId.val(res.left.user.data.user_id);

	if (res.right['action'] == 'getAdvertising' && res.center.action != 'finishBattle')
	{
		$('#rightContent').html('<table width=100%><tr align=center><td><b>Нет противника в зоне досягаемости...</b><br></td></tr><tr align=center><td><img src=/images/battle/1.gif width=210 height=277></tr></td></table>');
		enemyId.val('0');
	}

	if (res.right['action'] == 'getAllEnemy')
	{
		$('#rightContent').html(DrawUser(res.right.enemy));
		enemyId.val(res.right.enemy.data.user_id);
		rightData.val(0);
	}

	printLogs(res.logs);

	if (res.center.action == 'finishBattle')
	{
		$('#refresh_b').hide();
		centerContent.html('<font color=Red><b>' + finishBattle(res.center) + '</b></font>');
		$('#rightContent').html('<table width=100%><tr align=center><td><img src=/images/battle/1.gif width=210 height=230></tr></td></table>');
		enemyId.val('0');
		b_finish = 1;

		$('#centerInfo, #usersContent, .battle .timeout, .battle .actions').hide();

		clearTimeout(loader);
		clearTimeout(time);
	}
	else
	{
		b_finish = 0;
		priems = res.priems;
		spis = res.smena;
		printBattleInfo(res.info);
		printUsers(res.action_users);

		bat_users = res.action_users;
		impact_kick = res.center.impact.stat_kick;
		impact_block = res.center.impact.stat_block;

		centerContent.html(showInpactForm(impact_block, impact_kick));
		centerContent.append(DrawAutoUdar());

		if (res.center.action == 'waitImpact')
		{
			centerContent.html('<font color="Red"><b>Ожидаем хода противника...</b></font>');
			$('#rightContent').html('<table width=100%><tr align=center><td><img src=/images/battle/1.gif width=210 height=230></tr></td></table>');
			b_finish = 2;
		}
		else if (res.center.action == 'userDead')
		{
			centerContent.html('<font color=Red><b>Для вас бой окончен, подождите пока остальные игроки закончат поединок</b></font>');
			$('#rightContent').html('<table width=100%><tr align=center><td><img src=/images/battle/1.gif width=210 height=230></tr></td></table>');
			enemyId.val('0');
			b_finish = 2;
		}

	}
	last_action.value = res.center.action;

	if (res.center.action != 'finishBattle')
	{
		$('#indicatorLoad').hide();
		$('#refresh_b').show();
	}
	else
	{
		$('#indicatorLoad').hide();
		$('#refresh_b').hide();
	}

}

function finishBattle(res)
{
	var result = 'Ваш бой окончен. ';
	
	if (res.is_win == 'yes')
	{
		result += 'Победа за вами!';
	}
	else if (res.is_win == 'draw')
		result += 'Ничья!';
	else
		result += 'Вы проиграли!';
	
	result += '<br><input type="button" onclick="window.location.href=\'/pers/\'" class="standbut" value="Вернуться">';

	return result;
}

function printBattleInfo(info)
{
	var centerInfo = $('#centerInfo');
	centerInfo.html("<table width=100%><tr align='center'><td width=50%>Нанесено урона: <u>" + info['damage'] + "</u> HP</td><td>Тайм-аут: <u>" + info['timeout'] + "</u> мин.</tr></TABLE>");
	clearTimeout(time);
	ShowTimeBattle('timeout', info['timebattle'], 1);
}

function printUsers(users)
{
	var usersContent = $('#usersContent');

	var html = '<TABLE class="table"><TR><TD width=50% valign=top>';

	if (users['team_my'].length > 0 && users['team'].length > 0)
	{
		html += '<div><B>Союзники</B></div>';

		for (var i = 0; i < users['team_my'].length; i++)
		{
			color = users['team_my'][i]['timeout'] == "1" ? "red" : "black";
			html += '<a href="javascript:parent.to(\'' + users['team_my'][i]['login'] + '\')" oncontextmenu="parent.pp(\'' + users['team_my'][i]['login'] + '\'); return false;"><font color=CFA87A>' + users['team_my'][i]['login'] + '[' + users['team_my'][i]['level'] + ']</font></a> <font color=' + color + '><SMALL>';
			html += ' [' + users['team_my'][i]['hp'] + ' HP]</SMALL></font>';
		}

		html += '</td><TD width=50% valign=top><div><B>Противники</B></div>';

		for (i = 0; i < users['team'].length; i++)
		{
			color = users['team'][i]['timeout'] == "1" ? "red" : "black";
			html += '<a href="javascript:parent.to(\'' + users['team'][i]['login'] + '\')" oncontextmenu="parent.pp(\'' + users['team'][i]['login'] + '\'); return false;"><font color=CFA87A>' + users['team'][i]['login'] + '[' + users['team'][i]['level'] + ']</font></a> <font color=' + color + '><SMALL>';
			html += ' [' + users['team'][i]['hp'] + ' HP]</SMALL></font>';
		}

		html += '</td></tr></table>';
	}

	usersContent.html(html);
}

function GetW(now, max)
{
	var width = Math.ceil((now / max) * 100);

	return width;

}

var shw = 0;
function Spisok()
{

	if (b_finish > 0) return;

	var el, x, y;
	el = $('#oMen');
	if (shw == 0)
	{
		x = window.event.clientX + document.documentElement.scrollLeft + document.body.scrollLeft + 15;
		y = window.event.clientY + document.documentElement.scrollTop + document.body.scrollTop - 200;
		if (window.event.clientY + 72 > document.body.clientHeight)
		{
			y -= 34
		}
		else
		{
			y -= 2
		}
		el.append('<table width=\"200\"><tr><td align=center>Выберите противника:</td></tr>');

		for (var i = spis.length - 1; i >= 0; i--)
		{
			el.append('<tr><td><A class=menuItem HREF="#" onclick="smena=' + spis[i]['id'] + '; Spisok(); refresh();">' + spis[i]['n'] + '</a></td></td>');
		}

		el.append('</table>');
		el.css('left', x);
		el.css('top', y);
		el.show();
		shw = 1;
	}
	else
	{
		el.hide();
		el.html('');
		shw = 0;
	}
}

function DrawItemInfo(res)
{
	if (res.name === undefined)
		return '';

	var html = '';

	html += ' class="tooltip text" data-content="<table width=130 cellspacing=0 cellpadding=0 border=0>';
	html += '<tr><td align=center class=it><small><b>' + res.name + '</b></small></td></tr>';

	if (res.grav && res.grav.length > 0 && res.grav != 0)
		html += '<tr><td class=it>&bull; <small>Выгравирована надпись: <b>' + res.grav + '</b></small></td></tr>';
	if (res.iznos && res.iznos.length > 0)
		html += '<tr><td class=it>&bull; <small>Долговечность: <b>' + res.iznos + '</b></small></td></tr>';
	if (res.tip && res.tip.length > 0)
		html += '<tr><td class=it>&bull; <small>Класс: <b>' + res.tip + '</b></small></td></tr>';
	if (res.min && res.max && res.min > 0 || res.max > 0)
		html += '<tr><td class=it>&bull; <small>Удар: <b>' + res.min + ' - ' + res.max + '</b></small></td></tr>';
	if (res.hp && res.hp > 0)
		html += '<tr><td class=it>&bull; <small>Уровень жизни: +<b>' + res.hp + ' HP</b></small></td></tr>';
	if (res.energy && res.energy > 0)
		html += '<tr><td class=it>&bull; <small>Уровень энергии: +<b>' + res.energy + ' EP</b></small></td></tr>';

	html += '</table>"';

	return html;
}

function DrawUser(res)
{

	var html = '';

	html += '<table border="0" cellspacing="0" cellpadding="0" width=100% class="personBlock">';
	html += '<tr align=left>';
	html += '<td colspan="2" style="width:25px">';
	html += '<div class="personName">';
	html += show_inf(res.data.login, res.data.user_id, res.data.level, res.data.orden, res.data.rang, 1);
	html += '</div>';
	html += '</td>';
	html += '</tr>';
	html += '<tr align=center>';
	html += '<td valign="top">';
	html += '<div class="avtf">';
	html += '<div class="dlfr">';
	html += '<table width="100%" id="slotable">';
	html += '<tr>';
	html += '<td><div class="bdg stbox"><div id="life" class="g_line" style="width:' + GetW(res.data.hp, res.data.hp_all) + '%"><img src="/images/main/empty.gif" width="1" height="10"></div></div></td><td align="right" class="fntc">' + res.data.hp + '</td><td class="intf">|</td><td class="minf">' + res.data.hp_all + '</td>';
	html += '</tr><tr>';
	html += '<td><div class="bdg stbox"><div id="mana" class="b_line" style="width:' + GetW(res.data.energy, res.data.energy_all) + '%"><img src="/images/main/empty.gif" width="1" height="10"></div></div></td><td align="right" class="fntc">' + res.data.energy + '</td><td class="intf">|</td><td class="minf">' + res.data.energy_all + '</td>';
	html += '</tr>';
	html += '</table>';
	html += '</div>';
	html += '<table style="width:240px;" border="0" cellspacing="0" cellpadding="0">';
	html += '<tr>';
	html += '<td style="border:solid windowtext 1.5pt; border-color: #e1d0b0;" bgcolor=bfbfbf>';
	html += '<table style="width:240px; height:285px;" border="0" cellspacing="0" cellpadding="0">';
	html += '<tr>';
	html += '<td width="60" height="260" valign="top">';

	html += '<img src="/images/items/' + (res.items.s_1.type !== undefined ? res.items.s_1.type+'/' : '') + res.items.s_1.i + '.gif" width="60" height="58" '+ DrawItemInfo(res.items.s_1)+'>';
	html += '<img src="/images/items/' + (res.items.s_21.type !== undefined ? res.items.s_21.type+'/' : '') + res.items.s_21.i + '.gif" width="60" height="19" '+ DrawItemInfo(res.items.s_21)+'>';
	html += '<img src="/images/items/' + (res.items.s_2.type !== undefined ? res.items.s_2.type+'/' : '') + res.items.s_2.i + '.gif" width="60" height="19" '+ DrawItemInfo(res.items.s_2)+'>';
	html += '<img src="/images/items/' + (res.items.s_3.type !== undefined ? res.items.s_3.type+'/' : '') + res.items.s_3.i + '.gif" width="60" height="58" '+ DrawItemInfo(res.items.s_3)+'>';
	html += '<img src="/images/items/' + (res.items.s_4.type !== undefined ? res.items.s_4.type+'/' : '') + res.items.s_4.i + '.gif" width="60" height="78" '+ DrawItemInfo(res.items.s_4)+'>';
	html += '<img src="/images/items/' + (res.items.s_9.type !== undefined ? res.items.s_9.type+'/' : '') + res.items.s_9.i + '.gif" width="60" height="28" '+ DrawItemInfo(res.items.s_9)+'>';
	html += '<img src="/images/items/' + (res.items.s_6.type !== undefined ? res.items.s_6.type+'/' : '') + res.items.s_6.i + '.gif" width="20" height="20" '+ DrawItemInfo(res.items.s_6)+'>';
	html += '<img src="/images/items/' + (res.items.s_7.type !== undefined ? res.items.s_7.type+'/' : '') + res.items.s_7.i + '.gif" width="20" height="20" '+ DrawItemInfo(res.items.s_7)+'>';
	html += '<img src="/images/items/' + (res.items.s_8.type !== undefined ? res.items.s_8.type+'/' : '') + res.items.s_8.i + '.gif" width="20" height="20" '+ DrawItemInfo(res.items.s_8)+'>';

	html += '</td>';
	html += '<td width="120" height="240" align="center" valign="top">';
	html += '<img src="/images/avatar/' + res.data.obraz + '.png" width="120" height="240"><div style="height:10px"></div>';

	if (is_user == res.data.login)
	{
		if (res.items.s_17.i != "w17")
			html += '<img src="/images/items/' + res.items.s_17.type + '/' + res.items.s_17.i + '.gif" width="40" height="25" style="CURSOR: pointer" onclick="ShowForm(\'' + res.items.s_17.t + '\',\'/battle/\',\'\',\'\',\'1\',\'' + res.items.s_17.name + '\',\'' + res.items.s_17.id + '\',\'17\',\'\');">';
		else
			html += '<img src="/images/items/' + res.items.s_17.i + '.gif" width="40" height="25">';

		if (res.items.s_18.i != "w18")
			html += '<img src="/images/items/' + res.items.s_18.type + '/' + res.items.s_18.i + '.gif" width="40" height="25" style="CURSOR: pointer" onclick="ShowForm(\'' + res.items.s_18.t + '\',\'/battle/\',\'\',\'\',\'1\',\'' + res.items.s_18.name + '\',\'' + res.items.s_18.id + '\',\'18\',\'\');">';
		else
			html += '<img src="/images/items/' + res.items.s_18.i + '.gif" width="40" height="25">';
	}

	html += '</td>';
	html += '<td width="60" height="260" valign="top">';

	html += '<img src="/images/items/' + (res.items.s_14.type !== undefined ? res.items.s_14.type+'/' : '') + res.items.s_14.i + '.gif" width="60" height="40" '+ DrawItemInfo(res.items.s_14)+'>';
	html += '<img src="/images/items/' + (res.items.s_15.type !== undefined ? res.items.s_15.type+'/' : '') + res.items.s_15.i + '.gif" width="60" height="40" '+ DrawItemInfo(res.items.s_15)+'>';
	html += '<img src="/images/items/' + (res.items.s_5.type !== undefined ? res.items.s_5.type+'/' : '') + res.items.s_5.i + '.gif" width="60" height="60" '+ DrawItemInfo(res.items.s_5)+'>';
	html += '<img src="/images/items/' + (res.items.s_10.type !== undefined ? res.items.s_10.type+'/' : '') + res.items.s_10.i + '.gif" width="20" height="20" '+ DrawItemInfo(res.items.s_10)+'>';
	html += '<img src="/images/items/' + (res.items.s_11.type !== undefined ? res.items.s_11.type+'/' : '') + res.items.s_11.i + '.gif" width="20" height="20" '+ DrawItemInfo(res.items.s_11)+'>';
	html += '<img src="/images/items/' + (res.items.s_12.type !== undefined ? res.items.s_12.type+'/' : '') + res.items.s_12.i + '.gif" width="20" height="20" '+ DrawItemInfo(res.items.s_12)+'>';
	html += '<img src="/images/items/' + (res.items.s_22.type !== undefined ? res.items.s_22.type+'/' : '') + res.items.s_22.i + '.gif" width="60" height="80" '+ DrawItemInfo(res.items.s_22)+'>';
	html += '<img src="/images/items/' + (res.items.s_13.type !== undefined ? res.items.s_13.type+'/' : '') + res.items.s_13.i + '.gif" width="60" height="40" '+ DrawItemInfo(res.items.s_13)+'>';

	html += '</td></tr>';
	html += '</table></td></tr>';
	html += '</table></div></td></td></tr>';
	html += '</table></td></tr></table>';

	return html;
}

function printLogs(logs)
{
	var logsContent = $('#logsContent');
	var logsCount = $('#logsCount');
	var lastLogId = $('#lastLogId');
	var HTML = '';

	if (logs.length > 0)
	{
		var last_uid = "";
		var last_id = 0;
		var style;

		for (var i = logs.length - 1; i >= 0; i--)
		{
			var uid 	= logs[i]['uid'];
			var id 		= logs[i]['id'];

			if (last_uid != uid)
			{
				logsCount.val(parseInt(logsCount.val()) + 1);

				style = logs[i]['my'] == "y22" ? ' border: 1px solid #CAB569; background-color:#DED1A2;' : "";
				HTML += '</div></div><div><div style="padding: 3px; ' + style + '">';
			}

			HTML += SC(uid, logs[i]['date'], id, logs[i]['a'], logs[i]['at'], logs[i]['ah'], logs[i]['ad'], logs[i]['ab'], logs[i]['d'], logs[i]['dh'], logs[i]['dd'], logs[i]['db'], logs[i]['c']);

			if (logs[i]['hr'] == "1") HTML += '<HR>';

			last_uid = uid;

			if (id > last_id)
				last_id = id;
		}

		lastLogId.val(last_id);
		logsContent.html(HTML + '</div><HR></div>');
	}
}

function showInpactForm(colblocks, colImpacts)
{
	var HTML = '';

	HTML += '<input type="hidden" id="colImpacts" name="colImpacts" value="' + colImpacts + '">';
	HTML += '<input type="hidden" id="headImpact" name="headImpact" value="no">';
	HTML += '<input type="hidden" id="caseImpact" name="caseImpact" value="no">';
	HTML += '<input type="hidden" id="stomachImpact" name="stomachImpact" value="no">';
	HTML += '<input type="hidden" id="beltImpact" name="beltImpact" value="no">';
	HTML += '<input type="hidden" id="legsImpact" name="legsImpact" value="no">';
	HTML += '<input type="hidden" id="colBlocks" name="colBlocks" value="' + colblocks + '">';
	HTML += '<input type="hidden" id="headBlock" name="headBlock" value="no">';
	HTML += '<input type="hidden" id="caseBlock" name="caseBlock" value="no">';
	HTML += '<input type="hidden" id="stomachBlock" name="stomachBlock" value="no">';
	HTML += '<input type="hidden" id="beltBlock" name="beltBlock" value="no">';
	HTML += '<input type="hidden" id="legsBlock" name="legsBlock" value="no">';

	HTML += '<table>';
	HTML += '<tr><td align="center" style="border-right: 1px solid #858585"><b><small><a href="#" onclick="randAction(1);">Атака</a> (<span id="colImp">' + colImpacts + '</span>)</small></b></td>';
	HTML += '<td align="center" style="border-left: 1px solid #858585"><b><small><a href="#" onclick="randAction(2);">Защита</a> (<span id="colbl">' + colblocks + '</span>)</small></b></td></tr>';
	HTML += '<tr><td style="border-right: 1px solid #858585" class="text-center" width="80">';
	HTML += '<table width="49" cellpadding="0" cellspacing="0">';
	HTML += '<tr><td height="27" width="49" background="/images/battle/f_head.gif" onclick="impactAction(\'head\');" align="center" style="cursor: pointer;"><img src="/images/battle/impact_action_false.gif" id="headImpactImg"><td></tr>';
	HTML += '<tr><td height="25" width="49" background="/images/battle/f_grud.gif" onclick="impactAction(\'case\');" align="center" style="cursor: pointer;"><img src="/images/battle/impact_action_false.gif" id="caseImpactImg"><td></tr>';
	HTML += '<tr><td height="24" width="49" background="/images/battle/f_zhiv.gif" onclick="impactAction(\'stomach\');" align="center" style="cursor: pointer;"><img src="/images/battle/impact_action_false.gif" id="stomachImpactImg"><td></tr>';
	HTML += '<tr><td height="27" width="49" background="/images/battle/f_poyas.gif" onclick="impactAction(\'belt\');" align="center" style="cursor: pointer;"><img src="/images/battle/impact_action_false.gif" id="beltImpactImg"><td></tr>';
	HTML += '<tr><td height="27" width="49" background="/images/battle/f_nogi.gif" onclick="impactAction(\'legs\');" align="center" style="cursor: pointer;"><img src="/images/battle/impact_action_false.gif" id="legsImpactImg"><td></tr>';
	HTML += '</table></td>';
	HTML += '<td style="border-left: 1px solid #858585" class="text-center" width="80">';
	HTML += '<table width="49" cellpadding="0" cellspacing="0">';
	HTML += '<tr><td height="27" width="49" background="/images/battle/f_head.gif" onclick="blockAction(\'head\');" align="center" style="cursor: pointer;"><img src="/images/battle/block_action_false.gif" id="headBlockImg"><td></tr>';
	HTML += '<tr><td height="25" width="49" background="/images/battle/f_grud.gif" onclick="blockAction(\'case\');" align="center" style="cursor: pointer;"><img src="/images/battle/block_action_false.gif"  id="caseBlockImg"><td></tr>';
	HTML += '<tr><td height="24" width="49" background="/images/battle/f_zhiv.gif" onclick="blockAction(\'stomach\');" align="center" style="cursor: pointer;"><img src="/images/battle/block_action_false.gif"  id="stomachBlockImg"><td></tr>';
	HTML += '<tr><td height="27" width="49" background="/images/battle/f_poyas.gif" onclick="blockAction(\'belt\');" align="center" style="cursor: pointer;"><img src="/images/battle/block_action_false.gif"  id="beltBlockImg"><td></tr>';
	HTML += '<tr><td height="27" width="49" background="/images/battle/f_nogi.gif" onclick="blockAction(\'legs\');" align="center" style="cursor: pointer;"><img src="/images/battle/block_action_false.gif"  id="legsBlockImg"><td></tr>';
	HTML += '</table>';
	HTML += '</td></tr></table>';

	return HTML;
}


function impactAction(impact)
{
	var impactInput = $('#'+impact + 'Impact');
	var impactCount = $('#colImpacts');

	if (impactInput.val() == 'no')
	{
		if (impactCount.val() > 0)
		{
			$('#'+impact + 'Impact').val('yes');
			$('#'+impact + 'ImpactImg').attr('src', '/images/battle/impact_action_true.gif');
			$('#colImpacts').val(parseInt($('#colImpacts').val()) - 1);
			$('#colImp').html($('#colImpacts').val());
		}
	}
	else
	{
		$('#'+impact + 'Impact').val('no');
		$('#'+impact + 'ImpactImg').attr('src', '/images/battle/impact_action_false.gif');
		$('#colImpacts').val(parseInt($('#colImpacts').val()) + 1);
		$('#colImp').html($('#colImpacts').val());
	}

	auto_go_check();
}

function blockAction(block)
{
	var blockInput = $('#'+block + 'Block');
	var blockCount = $('#colBlocks');

	if (blockInput.val() == 'no')
	{
		if (blockCount.val() > 0)
		{
			$('#'+block + 'Block').val('yes');
			$('#'+block + 'BlockImg').attr('src', '/images/battle/block_action_true.gif');
			$('#colBlocks').val(parseInt($('#colBlocks').val()) - 1);
			$('#colbl').html($('#colBlocks').val());
		}
	}
	else
	{
		$('#'+block + 'Block').val('no');
		$('#'+block + 'BlockImg').attr('src', '/images/battle/block_action_false.gif');
		$('#colBlocks').val(parseInt($('#colBlocks').val()) + 1);
		$('#colbl').html($('#colBlocks').val());
	}

	auto_go_check();
}

function randAction(t)
{
	var arr = ['head', 'case', 'stomach', 'belt', 'legs'];

	if (t == 1)
	{
		while ($('#colImpacts').val() > 0)
		{
			impactAction(arr[Math.floor(Math.random() * 5)]);
		}
	}
	if (t == 2)
	{
		while ($('#colBlocks').val() > 0)
		{
			blockAction(arr[Math.floor(Math.random() * 5)]);
		}
	}
}

function auto_go_check()
{
	var impactCount = $('#colImpacts');
	var blockCount = $('#colBlocks');

	if ($('input[name=auto_go]').is(':checked'))
	{
		auto_udar = 1;

		if (impactCount.val() == 0 && blockCount.val() == 0)
			gofight();
	}
	else if (auto_udar == 1)
		auto_udar = 0;
}

function DrawRes(SP_BLK, SP_HIT, SP_KRT, SP_PRY, SP_HP, SP_SPR)
{
	var html = '<div class="priem-res">';
	html += "<span><img title='Блокирование удара' width=7 height=8 src='/images/battle/priem/block.gif'><br>" + SP_BLK + "</span>";
	html += "<span><img title='Удар' width=7 height=8 src='/images/battle/priem/hit.gif'><br>" + SP_HIT + "</span>";
	html += "<span><img title='Критический удар' width=7 height=8 src='/images/battle/priem/krit.gif'><br>" + SP_KRT + "</span>";
	html += "<span><img title='Успешное парирование' width=8 height=8 src='/images/battle/priem/parry.gif'><br>" + SP_PRY + "</span>";
	html += "<span><img title='Нанесенный урон' width=8 height=8 src='/images/battle/priem/hp.gif'><br>" + SP_HP + "</span>";
	html += "<span><img title='Магическая мощь' width=7 height=8 src='/images/battle/priem/spirit.gif'><br>" + SP_SPR + "</span>";
	html += '</div>';

	return html;
}

function DrawAutoUdar()
{
	var result = '';

	result += '<br><table><tr><td align=center>';
	result += '<input type="checkbox" name="auto_go" id="autofight"';

	if (auto_udar == 1)
		result += ' checked';

	result += '><label for="autofight"> - автоматический ход, если выбран удар и блок</label>';
	result += '</td></tr><tr><td align=center>';

	result += DrawRes(priems['p']['points']['b'], priems['p']['points']['h'], priems['p']['points']['k'], priems['p']['points']['p'], priems['p']['points']['hp'], priems['p']['points']['m']);

	result += '</td></tr>';
	result += '<tr><td align=center>';

	if (priems['p']['pa']['w'] == 0)
	{
		for (var i = 1; i <= 8; i++)
		{
			var txt = priems['p_' + i];

			if (txt['id'] != 0)
			{
				if (txt['w'] == 1)
					result += '<img  width=40 height=25 src="/images/battle/priem/' + txt['id'] + 'n.gif" class="tooltip text" data-content="<table width=200><tr><td><font color=blue><b>' + txt['n'] + '</td></tr><tr><td><font color=red size=1>Мин. треб:<br></font><font size=1>Блокирование: ' + txt['b'] + '<br><font size=1>Удар: ' + txt['h'] + '<br><font size=1>Крит: ' + txt['k'] + '<br>Парирование: ' + txt['p'] + '<br>Урон: ' + txt['d'] + '<br>Магия: ' + txt['m'] + '<br></font><font color=red size=1>Описание:<br></font><font size=1>' + txt['a'] + '</font></b></td></tr></table>">';
				else
					result += '<img style="CURSOR: pointer;" width=40 height=25 src="/images/battle/priem/' + txt['id'] + '.gif" title="Нажмите для использования" class="tooltip text" data-content="<table width=200><tr><td><font color=blue><b>' + txt['n'] + '</td></tr><tr><td><font color=red size=1>Мин. треб:<br></font><font size=1>Блокирование: ' + txt['b'] + '<br><font size=1>Удар: ' + txt['h'] + '<br><font size=1>Крит: ' + txt['k'] + '<br>Парирование: ' + ['p'] + '<br>Урон: ' + txt['d'] + '<br>Магия: ' + txt['m'] + '<br></font><font color=red size=1>Описание:<br></font><font size=1>' + txt['a'] + '</font></b></td></tr></table>" onclick="UsePriem(' + txt['id'] + ')">';
			}
			else
				result += '<img  width=40 height=25 src="/images/battle/priem/clear.gif" title="Пустой слот приёма">';
		}
	}
	else
		result += '<tr><td align=center>Выбран приём <b>' + priems['p']['pa']['n'] + '</b><br>Ожидание/Действие: <b>' + priems['p']['pa']['w'] + '/' + priems['p']['pa']['t'] + '</b> ходов.</td></tr>';

	result += '</td></tr></table>';

	return result;
}

// Комментарии
function SC(HitID, dates, HitStatus, attacker, side, AttackerHitType, AttackerDamage, AttackerBlock, defender, DefenderHitType, DefenderDamage, DefenderBlock, cm)
{

	var final_text;
	var show_str, show_str2, show_str5;
	var stat_img = '';
	var damage;
	var stat_kick;
	var stat_block;
	var opponent_kick;
	var opponent_block;

	var stat_k = String(AttackerHitType);
	var stat_b = String(AttackerBlock);
	var opponent_k = String(DefenderHitType);
	var opponent_b = String(DefenderBlock);

	stat_kick = stat_k.split(",");
	stat_block = stat_b.split(",");
	opponent_kick = opponent_k.split(",");
	opponent_block = opponent_b.split(",");

	if (is_user == attacker || is_user == defender)
		dates = '<B class=date2>' + dates + '</B>';
	else
		dates = '<B class=date1>' + dates + '</B>';

	var img_hint = [];
	img_hint[1] = 'Голова';
	img_hint[2] = 'Грудь';
	img_hint[3] = 'Живот';
	img_hint[4] = 'Пах';
	img_hint[5] = 'Ноги';

	if (attacker != "" && defender != "")
	{
		if (side == 0)
		{
			for (var i = 1; i < 6; i++)
			{
				if (opponent_block['0'] == i || opponent_block['1'] == i || opponent_block['2'] == i)
				{
					if (stat_kick['0'] == i || stat_kick['1'] == i)
						stat_img += '<img src="/images/battle/log/33.gif" title="' + img_hint[i] + '">';
					else
						stat_img += '<img src="/images/battle/log/31.gif" title="' + img_hint[i] + '">';
				}
				else
				{
					if (stat_kick['0'] == i || stat_kick['1'] == i)
						stat_img += '<img src="/images/battle/log/32.gif" title="' + img_hint[i] + '">';
					else
						stat_img += '<img src="/images/battle/log/30.gif" title="' + img_hint[i] + '">';

				}
			}
		}
		else
		{
			for (i = 1; i < 6; i++)
			{
				if (opponent_block['0'] == i || opponent_block['1'] == i || opponent_block['2'] == i)
				{
					if (stat_kick['0'] == i || stat_kick['1'] == i)
						stat_img += '<img src="/images/battle/log/43.gif" title="' + img_hint[i] + '">';
					else
						stat_img += '<img src="/images/battle/log/41.gif" title="' + img_hint[i] + '">';
				}
				else
				{
					if (stat_kick['0'] == i || stat_kick['1'] == i)
						stat_img += '<img src="/images/battle/log/42.gif" title="' + img_hint[i] + '">';
					else
						stat_img += '<img src="/images/battle/log/40.gif" title="' + img_hint[i] + '">';

				}
			}
		}
	}

	switch (parseInt(side))
	{
		case 0:
			attacker = '<B style="COLOR: #CFA87A">' + attacker + '</B>';
			defender = '<B style="COLOR: #142F98">' + defender + '</B>';
			final_text = '<B style="COLOR: #CFA87A">';
			break;
		case 1:
			attacker = '<B style="COLOR: #142F98">' + attacker + '</B>';
			defender = '<B style="COLOR: #CFA87A">' + defender + '</B>';
			final_text = '<B style="COLOR: #142F98">';
			break;
	}

	switch (stat_kick['0'])
	{
		case '1':
			show_str = 'в голову';
			break;
		case '2':
			show_str = 'в грудь';
			break;
		case '3':
			show_str = 'в живот';
			break;
		case '4':
			show_str = 'в пах';
			break;
		case '5':
			show_str = 'по ногам';
			break;
		default:
			show_str = '';
			break;
	}
	switch (stat_kick['1'])
	{
		case '1':
			show_str2 = ' и в голову';
			break;
		case '2':
			show_str2 = ' и в грудь';
			break;
		case '3':
			show_str2 = ' и в живот';
			break;
		case '4':
			show_str2 = ' и в пах';
			break;
		case '5':
			show_str2 = ' и по ногам';
			break;
		default:
			show_str2 = '';
			break;
	}
	switch (stat_kick['1'])
	{
		case '1':
			show_str5 = 'в голову';
			break;
		case '2':
			show_str5 = 'в грудь';
			break;
		case '3':
			show_str5 = 'в живот';
			break;
		case '4':
			show_str5 = 'в пах';
			break;
		case '5':
			show_str5 = 'по ногам';
			break;
		default:
			show_str5 = '';
			break;
	}

	if (cm >= 21 && cm <= 30)
		damage = '<B style="COLOR: Red">- ' + AttackerDamage + '</B>';
	else
		damage = '<B>- ' + AttackerDamage + '</B>';

	var comments = [];

	// Обычный удар
	comments[1] = attacker + ' ударил ' + show_str + '' + show_str2 + ', хотя ' + defender + ' пытался уйти от удара: ' + damage;
	comments[2] = attacker + ' саданул точный удар ' + show_str + '' + show_str2 + ', несмотря на то, что наглый ' + defender + ' хотел уйти от удара: ' + damage;
	comments[3] = attacker + ' влепил мощный удар ' + show_str + '' + show_str2 + ', несмотря на все усилия ' + defender + ' избежать этого: ' + damage;
	comments[4] = defender + ' явно недооценил силы противника... Как результат: ' + attacker + ' нанёс тяжелейший удар ' + show_str + '' + show_str2 + ': ' + damage;
	comments[5] = 'Почувствовав нерешительность ' + defender + ', разъярённый ' + attacker + ' со всего размаху ударил ' + show_str + ': ' + damage + ', но тот успел заблокировать удар ' + show_str5;
	comments[6] = defender + ' совершил роковую ошибку, подойдя вплотную к ' + attacker + ', на что тот ответил незамедлительным ударом ' + show_str + ': ' + damage + ', но ' + defender + ' героически заблокировал удар' + show_str5;
	comments[7] = defender + ' предпринял неудачную попытку заблокировать удар, за что и поплатился. Яростный ' + attacker + ' нанес точнейший удар ' + show_str + ': ' + damage + ', но тот успел заблокировать удар ' + show_str5;
	comments[8] = attacker + ', увидев страх в глазах противника, незамедлительно нанёс сокрушительный удар ' + show_str5 + ' ' + defender + ': ' + damage + ', на что тот ответил блокированием удара ' + show_str;
	comments[9] = 'Самоуверенный ' + attacker + ', подпрыгнув, нанёс точнейший удар ' + show_str5 + ' ' + defender + ': ' + damage + ', но от отчаяния противник успел заблокировать удар ' + show_str;
	comments[10] = 'Несмотря на корыстные планы ' + defender + ', непоколебимый ' + attacker + ', собравшись, ударил ' + show_str5 + ': ' + damage + ', но тот успел заблокировать удар ' + show_str;

	// Блок
	comments[11] = attacker + ' хотел вломить ' + show_str + '' + show_str2 + ', но ' + defender + ', не напрягаясь, заблокировал удар';
	comments[12] = attacker + ' изо всех сил пытался вломить, но ' + defender + ' увел удар ' + show_str + '' + show_str2;
	comments[13] = attacker + ' призадумался, благодаря чему сообразительный ' + defender + ', сменив тактику, заблокировал удар ' + show_str + '' + show_str2;
	comments[14] = 'Силы потраченные ' + attacker + ' для удара ' + show_str + '' + show_str2 + ' не принесли ему успеха, и как следствие ' + defender + ' заблокировал удар';
	comments[15] = defender + ' ушел в глухую оборону и как следствие заблокировал удар ' + attacker + ' ' + show_str + '' + show_str2;
	comments[16] = 'Замысел ' + attacker + ' легко читался и прозорливый ' + defender + ' увел удар ' + show_str + '' + show_str2;
	comments[17] = 'Силы были равны... Но обороняющийся ' + defender + ' оказался немного хитрее и поэтому заблокировал удар ' + attacker + ' ' + show_str + '' + show_str2;
	comments[18] = 'Атакующий ' + attacker + ' размахнулся, но всё было сделано настолько медленно, что ' + defender + ' заблокировал удар ' + show_str + '' + show_str2;
	comments[19] = 'Каким бы грозным не казался ' + attacker + ', это было не так, самовлюбленный ' + defender + ' увел удар ' + show_str + '' + show_str2;
	comments[20] = attacker + ' представил себе, каков вкус победы, но не тут то было, продуманный ' + defender + ' парировал удар ' + show_str + '' + show_str2;

	// Крит
	comments[21] = 'Видимо, бывают в жизни чудеса... Взбешенный ' + attacker + ' изо всех сил саданул ' + defender + ' ' + show_str + '' + show_str2 + ': ' + damage;
	comments[22] = 'Разъяренный ' + attacker + ' нанес тяжелейший удар ' + show_str + '' + show_str2 + ' противника, в результате чего ' + defender + ' получил тяжелейшие увечия: ' + damage;
	comments[23] = 'Разъяренный ' + attacker + ' вмочил со всей силы ' + show_str + '' + show_str2 + ' противника, в результате чего у ' + defender + ' аж глаза на лоб вылезли: ' + damage;

	// Уворот
	comments[31] = attacker + ' попытался нанести жестокий удар ' + show_str + '' + show_str2 + ', но ловкий ' + defender + ' увернулся от удара';
	comments[32] = attacker + ' размахнулся и ударил ' + show_str + '' + show_str2 + ', но ловкий ' + defender + ', показав язык, увернулся от удара';
	comments[33] = attacker + ' попытался вмочить со всей силы ' + show_str + '' + show_str2 + ', но ловкий ' + defender + ' обладал даром предвидения и увернулся от удара';

	// Пробой блока 
	comments[41] = attacker + ' размахнулся и с тупой улыбкой на лице вмачил со всей дури ' + defender + ' ' + show_str + '' + show_str2 + ' и ослабленный ' + defender + ' не смог сдержать удар ' + show_str + ' за что и поплатился кровью: ' + damage;
	comments[42] = attacker + ' размахнулся и набрав чит ударил бедного ' + defender + ' ' + show_str + '' + show_str2 + ' и пробил его второй блок: ' + damage;
	comments[43] = attacker + ' взял в руки меч и сплесал тектоник что привело ' + defender + ' в замешательство, как результат у ' + defender + ' выпало оружие от удивления и он не смог отбить летящее на него оружие: ' + damage;

	comments[70] = attacker + ' повержен!';
	comments[71] = 'Часы показывали <B class=date2>' + dates + '</B>, когда завязался бой!';

	if (side == 0)
		comments[72] = 'Поединок закончен. Ничья';
	else
		comments[72] = 'Поединок закончен. Победа за ' + final_text + '</B>';

	comments[73] = 'Поединок закончен по таймауту. Победа за ' + final_text + '</B>';

	comments[74] = attacker + ' со словами: \"Я изменю исход боя!\" вмешался в поединок!';

	comments[75] = attacker + ', не без помощи богов, воскресил ' + final_text + '</B>';

	// маг удар
	comments[76] = 'Персонаж ' + attacker + ' ударил в ' + defender + ' магией из своего оружия ' + damage;
	comments[77] = 'Персонаж ' + attacker + ' попытался противостоять магии ' + defender + ' нанеся некоторые повреждения ' + damage;

	comments[78] = 'Персонаж ' + attacker + ' решил слить бой и поэтому пропускает ход';
	comments[79] = 'Персонаж ' + attacker + ' решил схитрить и слить бой, но злобные судьи выкинули его из боя';

	// Приёмы
	comments[80] = 'Персонаж ' + attacker + ' использовал приём <b>uu</b>';

	final_text = dates + ' ' + stat_img + ' ' + comments[cm] + '<BR>';

	return final_text;
}

function ShowTimeBattle(fname, lefttime, type, fr)
{
	lefttime--;

	if (lefttime <= 0)
	{
		document.all('' + fname).innerText = '';
		refresh();
		clearTimeout(loader);
		loader = setTimeout(loaderRefresh, 45000);
	}
	else
	{
		var sec = lefttime % 60;

		var min = Math.floor(lefttime / 60);
		var day = Math.floor(lefttime / 86400);

		var hour = Math.floor((lefttime / 3600) - (day * 86400 / 3600));

		if (sec < 10)
			sec = "0" + sec;
		if (min > 60)
			min -= (Math.floor(min / 60) * 60);
		if (min == 60)
			min = 0;

		if (type != 1)
		{
			if (min < 10)
				min = "0" + min;
		}

		if (type == 1)
		{
			document.all('' + fname).innerText = min + " мин. " + sec + " сек.";
		}
		else
		{
			if (day > 0)
				document.all('' + fname).innerText = day + " д. " + hour + " ч. " + min + " мин.";
			else
				document.all('' + fname).innerText = hour + " ч. " + min + " мин.";
		}
		clearTimeout(time);
		time = setTimeout("ShowTimeBattle('" + fname + "'," + lefttime + "," + type + "," + fr + ")", 1000);
	}
}

function gofight()
{
	if (b_finish > 0)
		return;

	var impactCount = $('#colImpacts');
	var blockCount = $('#colBlocks');

	if (impactCount.val() > 0)
	{
		$.toast({
			text: 'Поставьте удары',
			icon: 'warning'
		});

		return false;
	}

	if (blockCount.val() > 0)
	{
		$.toast({
			text: 'Поставьте блоки',
			icon: 'warning'
		});

		return false;
	}

	$.ajax({
		'url': '/battle/?mode=ajax&opponent=' + $('#enemyId').val() + '&headImpact=' + $('#headImpact').val() + '&caseImpact=' + $('#caseImpact').val() + '&stomachImpact=' + $('#stomachImpact').val() + '&beltImpact=' + $('#beltImpact').val() + '&legsImpact=' + $('#legsImpact').val() + '&headBlock=' + $('#headBlock').val() + '&caseBlock=' + $('#caseBlock').val() + '&stomachBlock=' + $('#stomachBlock').val() + '&beltBlock=' + $('#beltBlock').val() + '&legsBlock=' + $('#legsBlock').val() + '&rnd=' + Math.random(),
		'type': 'get',
		'dataType': 'json',
		'cache': false,
		'success': function(data)
		{
			impact_block = 0;
			impact_kick = 0;

			actionRefresh(data);

			clearTimeout(loader);
			loader = setTimeout(loaderRefresh, 45000);
		},
		error: function()
		{
			alert('Произошла ошибка при получении ответа от сервера');
			window.location.href = '/battle/';
		}
	});
}

function battle_side (battle_id)
{
	$('#battle_side').show().find('input[name=offer]').val(battle_id);
}

$(document).ready(function()
{
	$('#battle_side').on('click', 'input[type=submit]', function(e)
	{
		e.preventDefault();

		$('#battle_side input[name=side]').val($(this).data('side'));
		$('#battle_side form').submit();
	});

	$(document).on('change', 'input[name=auto_go]', function()
	{
		auto_go_check();
	});
});