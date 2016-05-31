
function print_date(timestamp, view)
{
	timestamp = (timestamp + timezone * 1800) * 1000;

    var X = new Date(timestamp);

	if (view == 1) {
		return (X.getHours()+':'+((m=X.getMinutes())<10?'0':'')+m);
	} else {
		document.write(((d=X.getDate())<10?'0':'')+d+'-'+((mn=X.getMonth()+1)<10?'0':'')+mn+' '+X.getHours()+':'+((m=X.getMinutes())<10?'0':'')+m+':'+((s=X.getSeconds())<10?'0':'')+s);
		return '';
	}
}

var isMobile = /Android|Mini|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent);

var arSmiles = ['adolf','am','angel','angl','aplause','baby','boxing','bye','crazy','dollar','duel','evil','face1','face2','face5','fingal','fuu','girl','gun1','ha','happy','heart','hello','help','hummer','hummer2','ill','inlove','jack','jedy','killed','king','kiss2','knut','lick','lips','lol','med','roze','mol','ninja','nunchak','ogo','pare','police','prise','punk','ravvin','rip','rupor','scare','shut','sleep','song','strong','training','user','wall','rofl','hunter','bratan','diskot','vglaz','duet','ff','smoke','bita','perec','popope','morpeh','naem','pirat','baraban','klizma','gamer2','pulemet','good2','negative','quiet','ball','pooh','vv','fig1'];

function fightTo (id)
{
	confirmDialog('Бой', 'Вы действительно хотите напасть на этого игрока?', 'load(\'/map/?attack=Y&userid='+id+'\')')
}

var timeouts = [];
var start_time 	= new Date();

var Djs = start_time.getTime() - start_time.getTimezoneOffset()*60000;

function hms(layr, X)
{
      var d,mn,m,s;

      $("#" + layr).html(((d=X.getDate())<10?'0':'')+d+'.'+((mn=X.getMonth()+1)<10?'0':'')+mn+'.'+X.getFullYear()+' '+X.getHours()+':'+((m=X.getMinutes())<10?'0':'')+m+':'+((s=X.getSeconds())<10?'0':'')+s);
}

function UpdateClock()
{
   	var D0 = new Date;

	var m;
	var X = new Date(D0.getTime() + serverTime);

 	$("#clock").html(''+X.getHours()+':'+((m=X.getMinutes())<10?'0':'')+m+'');

	timeouts['clock'] = setTimeout(UpdateClock, 999);
}

var dialog;

$(document).ready(function()
{
	if ($.isFunction($(document).tooltip))
	{
		$(document).tooltip({
			items: ".tooltip",
			track: true,
			show: false,
			hide: false,
			position: {my: "left+25 top+15", at: "left bottom", collision: "flipfit"},
			content: function ()
			{
				if ($(this).hasClass('text'))
					return $(this).data('content');
				if ($(this).hasClass('script'))
					return eval($(this).data('content'));
			}
		});

		$('body').tooltip({
			items: ".tooltip2",
			tooltipClass: 'transparent',
			track: true,
			show: false,
			hide: false,
			position: {my: "left+25 top+15", at: "left bottom", collision: "flipfit"},
			content: function ()
			{
				if ($(this).hasClass('text'))
					return $(this).data('content');
				if ($(this).hasClass('script'))
					return eval($(this).data('content'));
			}
		});
	}

	if ($.isFunction($(document).dialog))
	{
		dialog = $("#windowDialog").dialog(
		{
			autoOpen: false,
			width: 400,
			modal: true,
			resizable: false,
			position: { my: "center", at: "center", of: window },
			buttons: {
				"Применить": function ()
				{
					$("#windowDialog form").submit();
				},
				"Отмена": function ()
				{
					dialog.dialog("close");
					$('#windowDialog').html('');
				}
			}
		});
	}

	setAjaxNavigation();

	$('.frame .header a').on('click', function(e)
	{
		e.preventDefault();

		window.frames.main.load($(this).attr('href'));
	});

	if (typeof VK != 'undefined')
	{
		VK.addCallback("onScrollTop", function(v, a, s, h)
		{
			VK.callMethod("resizeWindow", 1000, a - s - 80);
		});

		VK.callMethod("scrollTop");

		$(window).on('resize', function()
		{
			VK.callMethod("scrollTop");
		});
	}

	$('.game').on('scroll', function(e)
	{
		e.preventDefault();
	});
});

function ShowTime(fname, lefttime, type)
{
	lefttime--;

	if (lefttime <= 0)
	{
		$('#'+fname).text();
		window.location.reload();
	}
	else
	{
		var sec 	= lefttime % 60;
		var min 	= Math.floor(lefttime / 60);
		var day 	= Math.floor(lefttime / 86400);
		var hour 	= Math.floor((lefttime / 3600) - (day * 86400 / 3600));

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
			$('#'+fname).text(min + " мин. " + sec + " сек.");
		else
		{
			if (day > 0)
				$('#'+fname).text(day + " д. " + hour + " ч. " + min + " мин.");
			else
				$('#'+fname).text(hour + " ч. " + min + " мин.");
		}

		if (lefttime > 0)
			timeouts[fname] = setTimeout("ShowTime('" + fname + "'," + lefttime + "," + type + ")", 1000);
	}
}

function drop(title, id)
{
	confirmDialog('Рюкзак', 'Вы действительно хотите выбросить предмет "' + title + '"?', 'load(\'/edit/?drop='+id+'\')');
}

function ShowForm(title, script, fval, fname, mainform, usemagic, useid, onset, user)
{
	if (fval === undefined)
		fval = '';

	if (fname === undefined || fname == '')
		fname = 'login';

	if (usemagic === undefined)
		usemagic = '';

	if (useid === undefined)
		useid = '';

	if (onset === undefined)
		onset = '';

	dialog.dialog("option", "title", title);
	$("#windowDialog").html('<div class="container-fluid">' +
	'<form method="post" action="' + script + '" class="form-horizontal">' +
	'<div class="form-group"><div class="col-xs-12"><input type="text" name="' + fname + '" placeholder="Введите ник" class="form-control input-sm"></div></div>' +
	(usemagic != '' ? '<input name="usemagic" type="hidden" value=\'' + usemagic + '\'>' : '') +
	(useid != '' ? '<input name="useid" type="hidden" value=\'' + useid + '\'>' : '') +
	(onset != '' ? '<input name="onset" type="hidden" value=\'' + onset + '\'>' : '') +
	'</form></div>');
	dialog.dialog("open");

	if (user)
	{
		$("#windowDialog").find('input[name='+fname+']').val(user);
	}
	else
	{
		$("#windowDialog").find('input[name='+fname+']').val(fval);
	}
}

var statusMessages = {0: 'success', 1: 'info', 2: 'warning', 3: 'error'};

function setAjaxNavigation ()
{
	if (!$('#gamediv').length)
		return;

	$.ajaxSetup({data: {isAjax: true}});

	$("body").on('click', 'a[data-link!=Y]', function(e)
	{
		var el = $(this);

		if (el.hasClass('window'))
			return false;

		if (!el.attr('href'))
			return false;

		if (el.attr('href').indexOf('#') == 0)
			return false;

		if (el.attr('href').indexOf('javascript') == 0 || el.attr('href').indexOf('mailto') == 0 || el.attr('href').indexOf('#') >= 0 || el.attr('target') == '_blank')
			return true;
		else
		{
			e.preventDefault();

			load(el.attr('href'));
		}

		return false;
	});

	$('#gamediv form[class!=noajax]').ajaxForm(
	{
		delegation: true,
		dataType: 'json',
		beforeSerialize: function(form)
		{
			$(form).append('<input type="hidden" name="ajax" value="1">');

			showLoading();

			start_time = new Date();
			Djs = start_time.getTime() - start_time.getTimezoneOffset()*60000;
		},
		success: function (data)
		{
			hideLoading();
			ClearTimers();

			$('#gamediv').html(data.html);
		},
		error: function()
		{
			hideLoading();
			ClearTimers();

			alert('Что-то пошло не так!? Попробуйте еще раз');
		}
	});

	$(document).on('submit', '#windowDialog form', function(e)
	{
		e.preventDefault();

		showLoading();

		$.ajax({
			url: $(this).attr('target'),
			type: 'post',
			data: $(this).serializeObject(),
			dataType: 'json',
			success: function (data)
			{
				hideLoading();

				if (data.message != '')
				{
					$.toast({
						text: data.message,
						icon: statusMessages[data.status]
					});
				}
				else if (data.html != '')
				{
					ClearTimers();

					$('#gamediv').html(data.html);
					dialog.dialog("close");
				}

				if (data.status == 0)
				{
					dialog.dialog("close");
				}
			},
			error: function()
			{
				hideLoading();

				alert('Что-то пошло не так!? Попробуйте еще раз');
			}
		})
	});
}

$.fn.serializeObject = function()
{
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};

function ClearTimers ()
{
	for (var i in timeouts)
	{
		if (timeouts.hasOwnProperty(i))
		{
			clearInterval(timeouts[i]);
			clearTimeout(timeouts[i]);
		}
	}

	timeouts.length = 0;
}

function load (url)
{
	if (!blockTimer)
		return false;

    ClearTimers();

	blockTimer = false;

	if (addToUrl != '' && url.indexOf('?') < 0)
		url = url+'?';

	url = url+(addToUrl != '' ? '&'+addToUrl : '');

	showLoading();

	$.ajax(
	{
		url: url,
		cache: false,
		dataType: 'json',
		success: function(data)
		{
			hideLoading();
			ClearTimers();

			$('#gamediv').html(data.html);

			if (data.message != '')
			{
				$.toast({
					text: data.message,
					icon: statusMessages[data.status]
				});
			}

			dialog.dialog("close");

			if (data.data.tutorial.popup != '')
			{
				$.confirm({
				    title: 'Обучение',
				    content: data.data.tutorial.popup,
					confirmButton: 'Продолжить',
					cancelButton: false,
					backgroundDismiss: false,
					confirm: function ()
					{
						if (data.data.tutorial.url != '')
						{
							load(data.data.tutorial.url);
						}
					}
				});
			}

			if (data.data.tutorial.toast != '')
			{
				$.toast({
					text: data.data.tutorial.toast,
					icon: 'info',
					stack : 1
				});
			}
		},
		timeout: 10000,
		error: function()
		{
			window.location = url;
		}
	});

	start_time      = new Date();
    Djs             = start_time.getTime() - start_time.getTimezoneOffset()*60000;

	return true;
}

var loadingTimer;
var blockTimer = true;

function showLoading ()
{
	$('#preloadOverlay').show();

	setTimeout(function()
	{
		blockTimer = true;
	}, 200);

	/**
	clearTimeout(loadingTimer);
	loadingTimer = setTimeout(function()
	{
		$('#preloadOverlay').hide();
		$('#loadingOverlay').show();
	}, 1000);
	**/
}

function hideLoading ()
{
	//blockTimer = true;
	//clearTimeout(loadingTimer);
	//$('#loadingOverlay').hide();
	$('#preloadOverlay').hide();
}

function confirmDialog (title, content, callback)
{
	if (content == '')
		content = false;

	if (title == '')
		title = false;

	$.confirm({
	    title: title,
		content: content,
		confirmButton: 'Да',
		cancelButton: 'Нет',
		autoClose: 'cancel|6000',
	    confirm: function()
		{
			if (callback !== undefined && callback != '')
	        	eval(callback);
	    }
	});
}