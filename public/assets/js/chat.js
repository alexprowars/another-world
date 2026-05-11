function AddSmile(id)
{
	var msg = $('#msg');

	msg.val(msg.val() + ' :'+id+': ').focus();
}

function to (login)
{
	var msg = $('#msg');

	if ($('#windowDialog', window.frames['main'].document).is(':visible'))
	{
		$('#windowDialog', window.frames['main'].document).find('input[name=login]').val(login);
	}
	else
		msg.val('для [' + login + '] ' + msg.val()).focus();
}

function pp (login)
{
	var msg = $('#msg');

	if ($('#windowDialog', window.frames['main'].document).is(':visible'))
	{
		$('#windowDialog', window.frames['main'].document).find('input[name=login]').val(login);
	}
	else
		msg.val('приватно [' + login + '] ' + msg.val()).focus();
}

var ChatTimer;

function StopChatTimer()
{
	clearTimeout(ChatTimer);
}

function RefreshChat()
{
	StopChatTimer();
	showMessage();

	ChatTimer = setTimeout(RefreshChat, 10000);
}

function MsgSent(msg_id)
{
	StopChatTimer();

	$("#message_id").val(msg_id);

	ChatTimer = setTimeout(showMessage, 5000);
}

function ChatMsg(time, Player, To, Msg, Private, Me, My)
{
	var str = "";

	var j = 0;

	for (var i = 0; i < arSmiles.length; i++)
	{
		while (Msg.indexOf(':'+arSmiles[i]+':') >= 0)
		{
			Msg = Msg.replace(':'+arSmiles[i]+':', '<img src="/images/smile/' + arSmiles[i] + '.gif" onclick="S(\'' + arSmiles[i] + '\')" style="cursor:pointer">');

			if (++j >= 3)
				break;
		}

		if(j >= 3)
			break;
	}

	if (!time)
		return;

	if (Me > 0)
		str += "<span class='date2' onclick='pp(\"" + Player + "\");' style='cursor:pointer;'>";
	else if (My > 0)
		str += "<span class='date3' onclick='pp(\"" + Player + "\");' style='cursor:pointer;'>";
	else
		str += "<span class='date1' onclick='pp(\"" + Player + "\");' style='cursor:pointer;'>";

	if (!Player)
		str += print_date(time, 1) + "</span> ";
	else
	{
		str += print_date(time, 1) + "</span> ";

		if (My == 1)
			str += "<span class='negative'>";
		else
			str += "<span class='to' onclick='to(\"" + Player + "\");' style='cursor:pointer;'>";

		str += Player + "</span>: ";
	}

	if (To.length > 0)
	{
		str += '<span class="'+(Private ? 'private' : 'player')+'">'+(Private ? 'приватно' : 'для')+' [';

		for (i in To)
		{
			if (To.hasOwnProperty(i))
				str += (i > 0 ? ', ' : '') +'<a href=\'javascript:'+(Private ? 'pp' : 'to')+'("'+To[i]+'");\'>'+To[i]+'</a>';
		}

		str += ']</span> ';
	}

	str += Msg;

	$('#shoutbox').append('<div align="left">' + str + '</div>');

	descendreTchat();
	setTimeout(function(){descendreTchat();}, 500);
}

function descendreTchat()
{
	var elDiv = $('#shoutbox')[0];
	elDiv.scrollTop = elDiv.scrollHeight - elDiv.offsetHeight;
}

function addMessage()
{
	var obj = $("#msg");

	var data = obj.val();

	data = data.replace('%', '%25');
	while (data.indexOf('+') >= 0) data = data.replace('+', '%2B');
	while (data.indexOf('#') >= 0) data = data.replace('#', '%23');
	while (data.indexOf('&') >= 0) data = data.replace('&', '%26');
	while (data.indexOf('?') >= 0) data = data.replace('?', '%3F');
	while (data.indexOf('\'') >= 0)data = data.replace('\'', '`');

	obj.val('');

	StopChatTimer();
	HideSmiles();

	$.ajax({
		type: "POST",
		url: "/chat/send/",
		data: {msg: data},
		dataType: 'json',
		success: function(data)
		{
			if (data.success != 1)
				console.log('error in send message');

			if (data.messages.length > 0)
			{
				for (var i in data.messages)
				{
					if (data.messages.hasOwnProperty(i))
						ChatMsg(data.messages[i]['time'], data.messages[i]['user'], data.messages[i]['to'], data.messages[i]['text'], data.messages[i]['private'], data.messages[i]['me'], data.messages[i]['my']);
				}
			}
		},
		error: function()
		{
			console.log('error in send message');
		},
		complete: function()
		{
			ChatTimer = setTimeout(showMessage, 500);
		}
	});

	descendreTchat();
}

function showMessage()
{
	$.ajax({
		type: "GET",
		url: "/chat/",
		data: "message_id=" + parseInt($("#message_id").val()) + "",
		dataType: 'json',
		success: function (data)
		{
			if (data.messages.length > 0)
			{
				for (var i in data.messages)
				{
					if (data.messages.hasOwnProperty(i))
						ChatMsg(data.messages[i]['time'], data.messages[i]['user'], data.messages[i]['to'], data.messages[i]['text'], data.messages[i]['private'], data.messages[i]['me'], data.messages[i]['my']);
				}
			}

			MsgSent(data.last_message);
		}
	});
}

function S(name)
{
	var msg = $('#msg');

	msg.val(msg.val()+':' + name + ':');
	msg.focus();
}

function showSmiles()
{
	var obj = $("#smiles");

	if (obj.is(':visible'))
		obj.html('').hide();
	else
	{
		for (var i = 0; i < arSmiles.length; i++)
			obj.append('<img src="/images/smile/'+arSmiles[i]+'.gif" alt="'+arSmiles[i]+'" onclick="AddSmile(\''+arSmiles[i]+'\')" style="cursor:pointer"> ');

		obj.show();
	}

	descendreTchat();
}

function HideSmiles()
{
	$('#smiles').html('').attr('style', 'display:none');
}

function ClearChat()
{
	$("#shoutbox").html('');
}

function autoClearChat()
{
	var obj = $("#shoutbox");
	var s = obj.html();

	if (s.length > 5000)
	{
		var j = s.lastIndexOf('<br>', s.length-5000);

		obj.html(s.substring(j, s.length));
	}
}

function doSomething(e)
{
	if (!e)
		e = window.event;
	if (e.keyCode == 13)
		addMessage();

	return true;
}

window.document.onkeydown = doSomething;

$(document).ready(function()
{
	setTimeout(showMessage, 1000);
	setInterval(autoClearChat, 150000);
});

var noob_count = 0;
var mes = [];
mes[0] = 'Здравствуйте, вы попали в славный мир Another World. Я помогу вам освоиться здесь.';
mes[1] = 'Прежде всего вы должны распределить свободные параметры, такие как: сила, удача, ловкость и выносливость. Разум на нулевом уровне качать нет смысла. Чтобы увеличить параметры надо нажать на "Есть свободные статы!", и в появившемся окне, сделать выбор статов.';
mes[2] = 'Не спешите выбирать между энергией и выносливостью, т.к. этот выбор определит вашу будущую раскачку под мага или воина соответственно. Выбор можно сделать в любой момент.';
mes[3] = 'Теперь следует приобрести тренировочный нож за 2 кр, он значительно увеличит наносимый вами урон.  Чтобы попасть в любое здание вначале надо нажать  кнопку "Город", расположенную в верхнем фрэйме, затем выбрать здание, в нашем случае "Магазин", он находится на "Торговой площади".';
mes[4] = 'Возращаемся на Арену и начинаем свой путь к первому уровню! Опыт игроки набирают в поединках, на нулевом уровне доступны физические поединки (1х1) и бои с вашим клоном в тренировочной комнате. В бою вы можете сделать 1 удар и поставить 2 блока(без щита).';
mes[5] = 'Удачи вам, на этом не лёгком пути к славе и победам.';

function noob_text()
{
	if (mes[noob_count] != undefined)
	{
		ChatMsg((new Date().getTime() / 1000), 'Коментатор', [], mes[noob_count], 0, 1, 0);
		setTimeout(noob_text, 45000);

		noob_count++;
	}
}

var history_cur = 0;
var history = new Array(100);

function keypress(code)
{
	switch (code)
	{
		case 13:
			addMessage();
			break;
		case 38:
			if (history_cur == 0)
				history[0] = $('#msg').val();

			if (history[history_cur + 1] == null) return;

			if (history_cur < 99)
			{
				history_cur++;
				$('#msg').val(history[history_cur]);
			}
			break;

		case 40:
			if (history_cur > 0)
			{
				history_cur--;
				$('#msg').val(history[history_cur]);
			}
			else
				$('#msg').val(history[0]);

			break;
	}
}

function f(target_url, win_name)
{
	var new_win = window.open(target_url, win_name, 'resizable=yes, scrollbars=yes, menubar=no, toolbar=no, width=550, height=280, top=0, left=0');
	new_win.focus();
}

function history_rec(msg)
{
	if (msg != "")
	{
		for (var i = 99; i > 1; i--)
		{
			history[i] = history[i - 1];
		}

		history[1] = msg;
		history_cur = 0;
	}
}

function loadChatList ()
{
	var reload = $('#autoreload');

	$.ajax({
		type: "get",
		url: "/chat/online/?autoreload="+(reload.is(':checked') ? reload.val() : ''),
		data: "",
		dataType: 'json',
		success:function (data)
		{
			$('#chatList').html(data.html);
		}
	});
}

var listReload;

$(document).ready(function()
{
	setTimeout(loadChatList, 1000);

	$('#chatList').on('click', '.actions a', function(e)
	{
		e.preventDefault();

		$.ajax({
			type: "get",
			url: "/chat/online/"+$(this).attr('href'),
			data: "",
			dataType: 'json',
			success:function (data)
			{
				console.log(data);

				$('#chatList').html(data.html);
			},
			error: function()
			{
				console.log('error in loading chat users list');
			}
		});
	})
	.on('change', '#autoreload', function()
	{
		if ($(this).is(':checked'))
			listReload = setInterval(loadChatList, 60000);
		else
			clearInterval(listReload);
	});
});