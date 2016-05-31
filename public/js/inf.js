function iteminfo(id)
{
	var PageUrl = "iteminfo.php?id=" + id;
	window.open(PageUrl, 'Inf', 'location=no,menubar=no,status=no,toolbar=no,scrollbars=no,width=650,height=400')
}

var gift = ['Подарок от ', '', 'Анонимный подарок'];

function quoteString(str)
{
	str = str.replace(/\\/g, '\\\\');
	str = str.replace(/\'/g, '\\\'');
	str = str.replace(/\"/g, '\\"');
	str = str.replace(/\n/g, '\\n');
	return "'" + str + "'";
}

function DrawGift(name, flag, title, text, from, uid, width, height)
{
	var s = ('<img SRC="/images/items/' + name + '.gif" width=' + width + ' height=' + height + ' style="cursor: pointer;" title="');

	if (text)
		s += text + "\n";

	from = from.replace(/клан /g, 'клана ');

	if (from == '__hide')
		from = '';

	s += (from ? (gift[0] + from + gift[1]) : gift[2]) + '"' +
	' onclick="HideGift();ShowGift(' + quoteString(title) + ', ' + quoteString(name) + ', ' +
	flag + ', ' + quoteString(text) + ', ' + quoteString(from) + ', this.offsetTop' + (uid ? (',\'' + uid + '\'') : '') + ');"' +
	'>';

	return s;
}


function DG1(name, flag, title, text, from, uid)
{
	DrawGift(name, flag, title, text, from, uid, 61, 60);
}

function DG2(name, flag, title, text, from, uid)
{
	DrawGift(name, flag, title, text, from, uid, 80, 74);
}

function DF(name, flag, title, text, from, uid)
{
	return DrawGift(name, flag, title, text, from, uid, 60, 60);
}

function quote_url(s)
{
	var from = Array('+', ' ', '#');
	var to = Array('%2B', '+', '%23');
	for (var i = 0; i < from.length; ++i) while (s.indexOf(from[i]) >= 0)  s = s.replace(from[i], to[i]);
	return s;
}

function ShowGift(title, name, img, text, from, y, uid)
{
	var el = $('#mgift');

	if (el.is(':hidden'))
	{
		if (uid)
		{
			if (parseInt(uid))
				from = gift[0] + '<a target=_blank href="/clan_inf.php?clan=' + uid + '">' + from + '</a>' + gift[1];
			else
				from = gift[0] + '<a target=_blank href="/info/?login=' + uid + '">' + from + '</a>' + gift[1];
		}

		if (!from)
			from = gift[2];

		$('#mgift_sign').html('<small>' + (text ? text + '<br>' : '') + from + '</small>');
		$('#mgift_title').html('<small><b>' + title + '</b></small>');

		$('#mgift_pict').html('<br><img src=/images/items/' + name + '.gif alt="' + title + '"><br><br>');
	}

	var x = 15;

	el.css('left', x + "px");
	el.css('top', y + "px");

	el.show();
}

function HideGift()
{
	$('#mgift').hide();
}