function show_inf(login, id, level, rank, klan)
{
	var result = '';

	if (rank > 0)
	{
		var hint_rank;

		if (rank == 0)
			hint_rank = 'Смертные';
		else if ((rank >= 10 && rank <= 14) || rank == 99)
			hint_rank = 'Орден Инквизиции';
		else if (rank == 20)
			hint_rank = 'Тьма';
		else if (rank == 30)
			hint_rank = 'Дилер';
		else if (rank == 31)
			hint_rank = 'Наставник';
		else if (rank == 60)
			hint_rank = 'Бот';
		else if (rank == 100)
			hint_rank = 'Божество';

		result += '<img src="/images/rank/' + rank + '.gif" height="15" alt="' + hint_rank + '">';
	}

	if (klan != '' && klan != '0')
		result += '<img src="/images/tribe/' + klan + '.gif" height="15" alt="Клан ' + klan + '">';

	result += '<a href="/info/?id=' + id + '" target=_blank>' + login + '</a> [' + level + ']';

	return result;
}


function it(title, iznos, tip, min, max, hp, energy, grav, slot)
{
	var s = '<table width="170">';

	if (title && title != 'Снять ')
	{
		s += '<tr><td align="center" class="it"><b>' + title + '</b></td></tr>';

		if (grav && grav.length > 0 && grav != 0)
			s += '<tr><td class=it>&bull; Выгравирована надпись: <b>' + grav + '</b></td></tr>';
		if (iznos && iznos.length > 0)
			s += '<tr><td class=it>&bull; Долговечность: <b>' + iznos + '</b></td></tr>';
		if (tip && tip.length > 0)
			s += '<tr><td class=it>&bull; Класс: <b>' + tip + '</b></td></tr>';
		if (min && max && min > 0 || max > 0)
			s += '<tr><td class=it>&bull; Удар: <b>' + min + ' - ' + max + '</b></td></tr>';
		if (hp && hp > 0)
			s += '<tr><td class=it>&bull; Уровень жизни: +<b>' + hp + ' HP</b></td></tr>';
		if (energy && energy > 0)
			s += '<tr><td class=it>&bull; Уровень энергии: +<b>' + energy + ' EP</b></td></tr>';
	}
	else
	{
		var slot_hint;

		if (slot == 1) slot_hint = 'Шлем';
		if (slot == 2) slot_hint = 'Ожерелье';
		if (slot == 3) slot_hint = 'Оружие';
		if (slot == 4) slot_hint = 'Доспех';
		if (slot == 5) slot_hint = 'Щит';
		if ((slot >= 6 && slot <= 8) || (slot >= 10 && slot <= 12)) slot_hint = 'Кольцо';
		if (slot == 9) slot_hint = 'Пояс';
		if (slot == 13) slot_hint = 'Обувь';
		if (slot == 14) slot_hint = 'Нарукавники';
		if (slot == 15) slot_hint = 'Перчатки';
		if (slot == 17 || slot == 18 || slot == 19) slot_hint = 'Магия';
		if (slot == 20) slot_hint = 'Магический предмет';
		if (slot == 21) slot_hint = 'Серьги';
		if (slot == 22) slot_hint = 'Штаны';

		s += '<tr><td align=center class=it>Пустой слот <b>' + slot_hint + '</b></td></tr>';
	}

	s += '</table>';

	return s;
}

function SD(text, num)
{
	document.write('<TR><TD WIDTH=70>');
	document.write(text);
	document.write(':</TD><TD valign=bottom><table cellspacing=0 cellpadding=0 border=0 align=center WIDTH=102><TR><TD WIDTH=1 HEIGHT=8><IMG SRC=\'/images/images/inf/left.gif\' WIDTH=1 HEIGHT=8></TD><TD WIDTH=100 HEIGHT=8 BACKGROUND=\'/images/images/inf/line_gray.gif\' STYLE=\'COLOR: White\' ALIGN=Left><IMG SRC=\'/images/images/inf/line.gif\' WIDTH=');
	document.write(num * 0.1);
	document.write(' HEIGHT=8></TD><TD WIDTH=1 HEIGHT=8><IMG SRC=\'/images/images/inf/right.gif\' WIDTH=1 HEIGHT=8></TD></TR><TR><TD COLSPAN=3 HEIGHT=1><SPAN></SPAN></TD></TR></table></TD></TR>');
}

function gebi(id)
{
	return this.document.getElementById(id);
}

function artifactAlt(id)
{
	return $('#' + id).html();
}

function Show_Item(name, title, id, price, izn_1, izn_2, t, grav, min_level, min_str, min_dex, min_ag, min_vit, min_razum, min_proff, min_d, max_d, br1, br2, br3, br4, br5, br_m, strength, dex, agility, vitality, razum, krit, unkrit, uv, unuv, mkrit, pblock, mblock, pbr, kbr, hp, energy, about, otravl, use_mana, magic)
{
	var str;
	var tip;
	var proff;
	var c = 0;
	str = '';

	switch (t)
	{
		case 1:
			tip = "Оружие";
			break;
		case 2:
			tip = "Доспех";
			break;
		case 3:
			tip = "Кольцо";
			break;
		case 4:
			tip = "Ожерелье";
			break;
		case 5:
			tip = "Щит";
			break;
		case 6:
			tip = "Обувь";
			break;
		case 7:
			tip = "Пояс";
			break;
		case 8:
			tip = "Шлем";
			break;
		case 9:
			tip = "Перчатки";
			break;
		case 10:
			tip = "Нарукавники";
			break;
		case 11:
			tip = "Рубаха";
			break;
		case 12:
			tip = "Свиток";
			break;
		case 13:
			tip = "Магический предмет";
			break;
		case 14:
			tip = "Зелье";
			break;
		case 15:
			tip = "Открытка";
			break;
		case 16:
			tip = "Подарок";
			break;
		case 17:
			tip = "Цветы";
			break;
		case 18:
			tip = "Инструмент";
			break;
		case 19:
			tip = "Ресурс";
			break;
		case 20:
			tip = "Драгоценный камень";
			break;
		case 21:
			tip = "Документ";
			break;
		case 22:
			tip = "Квест предмет";
			break;
		case 23:
			tip = "Ингридиент";
			break;
		case 24:
			tip = "Серьги";
			break;
		case 25:
			tip = "Штаны";
			break;
		case 26:
			tip = "Магич. предмет";
			break;
	}

	str += '<table style="border: 1px solid #DB9F73; background: url(\'/images/header/sand3.gif\')" width="100%">';
	str += '<tr>';
	str += '<td align="center" width="150">';
	str += '<img src="/images/items/'+t+'/' + name + '.gif" class="tooltip2 script" data-content="artifactAlt(\'AA_' + id + '\')">';
	str += '</td>';

	str += '<td valign="top" style="padding:5px">';
	str += '<table width="100%" class="table sm">';
	str += '<tr><td><a href="#" style="color:#666666" class="b">' + title + '</a></td>';
	str += '<td align="center" width="130"><a href="javascript:;" class="butt2" onClick="confirmDialog(\'Рюкзак\', \'Вы действительно хотите надеть эту вещь?\', \'load(\\\'/edit/?onset=' + id + '\\\')\')">надеть</a></td>';
	str += '</tr>';
	str += '<tr>';
	str += '<td title="Тип предмета"><img src="/images/images/tbl-shp_item-icon.gif" width="11" height="10" align="absmiddle"> ' + tip + '</td>';
	str += '<td colspan="2" align="center" title="Требуемый уровень" nowrap><img src="/images/images/tbl-shp_level-icon.gif" width="11" height="10" align="absmiddle"> Уровень <b class="red">' + min_level + '</b></td>';
	str += '</tr>';
	str += '<tr>';
	str += '<td title="Прочность предмета"><img src="/images/images/tbl-shp_item-iznos.gif" width="11" height="10" align="absmiddle"> <font color="red">' + izn_1 + '</font>/' + izn_2 + '</td>';

	if (t == 12 || t == 13 || t == 14)
		str += '<td align="center"><a href="javascript:;" class="butt2" onClick="ShowForm(\'' + title + '\',\'\',\'\',\'\',\'1\',\'' + name + '\',\'' + id + '\',\'0\');">использовать</a></td>';


	str += '</tr>';
	str += '<tr>';
	str += '<td class="b grnn" title="Цена"><span title="Кредиты"><img src="/images/images/m_game3.gif" border=0 width=11 height=11 align=absmiddle></span>&nbsp;' + price + '</td>';
	str += '<td align="center"><a href="javascript:;" class="butt2" onClick="confirmDialog(\'Рюкзак\', \'Вы действительно хотите выбросить ' + title + '?\', \'load(\\\'/edit/?drop=' + id + '\\\')\')">выбросить</ф></td>';
	str += '</tr>';
	str += '</table>';
	str += '</td>';
	str += '</tr>';
	str += '</table>';

	str += '<div ID="AA_' + id + '" style="display:none;">';
	str += '<table width="250">';
	str += '<tr><td style="font-size:10px;" width="22" height="25"><img src="/images/header/stm1-tl.gif" width="22" height="25" alt="" border="0"><br></td><td style="font-size:10px;background: url(\'/images/header/stm1-t.gif\') repeat-x top" class="tabcata" align="center"><b style="font-size:10px;color:#ff0000">' + title + '</b></td><td style="font-size:10px;" width="22"><img src="/images/header/stm1-tr.gif" width="22" height="25" alt="" border="0"><br></td></tr>';
	str += '<tr><td style="font-size:10px;background: url(\'/images/header/stm1-l.gif\') repeat-y top left"></td><td style="font-size:10px;background: url(\'/images/header/sand3.gif\')">';

	if (min_proff || min_level || min_str || min_dex || min_ag || min_vit || min_razum)
	{
		str += '<table class="table np">';
		str += '<tr><td colspan="2"><b>Минимальные требования:</b></td></td>';

		if (min_proff > 0)
		{
			switch (min_proff)
			{
				case 1:
					proff = "Лекарь";
					break;
				case 2:
					proff = "Кузнец";
					break;
				case 3:
					proff = "Огранщик";
					break;
				case 4:
					proff = "Наёмник";
					break;
				case 5:
					proff = "Шахтёр";
					break;
				case 6:
					proff = "Маг";
					break;
				case 7:
					proff = "Алхимик";
					break;
				case 8:
					proff = "Торговец";
					break;
				case 9:
					proff = "Травник";
					break;
			}

			str += '<tr style="';
			if (c % 2 == 0) str += 'background-color: #F4BB8A;';
			c++;
			str += '"><td>Профессия</td><td ';
			if (min_proff != my_proff) str += 'style="color: #d00000;"';
			str += 'align="right"><b>' + proff + '</b></b></td></tr>';
		}
		if (min_level.length && min_level > 0)
		{
			str += '<tr style="';
			if (c % 2 == 0) str += 'background-color: #F4BB8A;';
			c++;
			str += '"><td>Уровень</td><td ';
			if (min_level > my_level) str += 'style="color: #d00000;"';
			str += 'align="right"><b>' + min_level + '</b></b></td></tr>';
		}
		if (min_str.length && min_str > 0)
		{
			str += '<tr style="';
			if (c % 2 == 0) str += 'background-color: #F4BB8A;';
			c++;
			str += '"><td>Сила</td><td ';
			if (min_str > my_strength) str += 'style="color: #d00000;"';
			str += 'align="right"><b>' + min_str + '</b></b></td></tr>';
		}
		if (min_dex.length && min_dex > 0)
		{
			str += '<tr style="';
			if (c % 2 == 0) str += 'background-color: #F4BB8A;';
			c++;
			str += '"><td>Удача</td><td ';
			if (min_dex > my_dex) str += 'style="color: #d00000;"';
			str += 'align="right"><b>' + min_dex + '</b></b></td></tr>';
		}
		if (min_ag.length && min_ag > 0)
		{
			str += '<tr style="';
			if (c % 2 == 0) str += 'background-color: #F4BB8A;';
			c++;
			str += '"><td>Ловкость</td><td ';
			if (min_ag > my_agility) str += 'style="color: #d00000;"';
			str += 'align="right"><b>' + min_ag + '</b></b></td></tr>';
		}
		if (min_vit.length && min_vit > 0)
		{
			str += '<tr style="';
			if (c % 2 == 0) str += 'background-color: #F4BB8A;';
			c++;
			str += '"><td>Выносливость</td><td ';
			if (min_vit > my_vitality) str += 'style="color: #d00000;"';
			str += 'align="right"><b>' + min_vit + '</b></b></td></tr>';
		}
		if (min_razum.length && min_razum > 0)
		{
			str += '<tr style="';
			if (c % 2 == 0) str += 'background-color: #F4BB8A;';
			c++;
			str += '"><td>Разум</td><td ';
			if (min_razum > my_razum) str += 'style="color: #d00000;"';
			str += 'align="right"><b>' + min_razum + '</b></b></td></tr>';
		}

		str += '	</table>';
	}

	if (otravl || hp || energy || min_d || max_d || dex || agility || vitality || razum || br1 || br2 || br3 || br4 || br5 || krit || unkrit || uv || unuv || mkrit || pblock || mblock || pbr || kbr || metk)
	{
		str += '<table class="table np">';
		str += '<tr><td colspan="2"><b>Действие предмета:</b></td></td>';
		c = 0;

		if (otravl != 0)
		{
			str += '	<tr style="';
			if (c % 2 == 0) str += 'background-color: #F4BB8A;';
			c++;
			str += '"><td>Отравление</td><td align="right"><b>';
			if (otravl > 0) str += '+';
			str += otravl + '</b></b></td></tr>';
		}
		if (hp != 0)
		{
			str += '	<tr style="';
			if (c % 2 == 0) str += 'background-color: #F4BB8A;';
			c++;
			str += '"><td>Уровень жизни</td><td align="right"><b>';
			if (hp > 0) str += '+';
			str += hp + '</b></b></td></tr>';
		}
		if (energy != 0)
		{
			str += '<tr style="';
			if (c % 2 == 0) str += 'background-color: #F4BB8A;';
			c++;
			str += '"><td>Уровень энергии</td><td align="right"><b>';
			if (energy > 0) str += '+';
			str += energy + '</b></b></td></tr>';
		}
		if (min_d > 0 || max_d > 0)
		{
			str += '<tr style="';
			if (c % 2 == 0) str += 'background-color: #F4BB8A;';
			c++;
			str += '"><td>Урон</td><td align="right"><b>min: ';
			if (min_d > 0) str += '+';
			str += min_d + '...max: ';
			if (max_d > 0) str += '+';
			str += max_d + '</b></b></td></tr>';
		}

		if (strength != 0)
		{
			str += '<tr style="';
			if (c % 2 == 0) str += 'background-color: #F4BB8A;';
			c++;
			str += '"><td>Сила</td><td align="right"><b>';
			if (strength > 0) str += '+';
			str += strength + '</b></b></td></tr>';
		}
		if (dex != 0)
		{
			str += '<tr style="';
			if (c % 2 == 0) str += 'background-color: #F4BB8A;';
			c++;
			str += '"><td>Удача</td><td align="right"><b>';
			if (dex > 0) str += '+';
			str += dex + '</b></b></td></tr>';
		}
		if (agility != 0)
		{
			str += '<tr style="';
			if (c % 2 == 0) str += 'background-color: #F4BB8A;';
			c++;
			str += '"><td>Ловкость</td><td align="right"><b>';
			if (agility > 0) str += '+';
			str += agility + '</b></b></td></tr>';
		}
		if (vitality != 0)
		{
			str += '<tr style="';
			if (c % 2 == 0) str += 'background-color: #F4BB8A;';
			c++;
			str += '"><td>Выносливость</td><td align="right"><b>';
			if (vitality > 0) str += '+';
			str += vitality + '</b></b></td></tr>';
		}
		if (razum != 0)
		{
			str += '<tr style="';
			if (c % 2 == 0) str += 'background-color: #F4BB8A;';
			c++;
			str += '"><td>Разум</td><td align="right"><b>';
			if (razum > 0) str += '+';
			str += razum + '</b></b></td></tr>';
		}

		if (br1 != 0)
		{
			str += '	<tr style="';
			if (c % 2 == 0) str += 'background-color: #F4BB8A;';
			c++;
			str += '"><td>Броня головы</td><td align="right"><b>';
			if (br1 > 0) str += '+';
			str += br1 + '</b></b></td></tr>';
		}
		if (br2 != 0)
		{
			str += '	<tr style="';
			if (c % 2 == 0) str += 'background-color: #F4BB8A;';
			c++;
			str += '"><td>Броня корпуса</td><td align="right"><b>';
			if (br2 > 0) str += '+';
			str += br2 + '</b></b></td></tr>';
		}
		if (br3 != 0)
		{
			str += '	<tr style="';
			if (c % 2 == 0) str += 'background-color: #F4BB8A;';
			c++;
			str += '"><td>Броня живота</td><td align="right"><b>';
			if (br3 > 0) str += '+';
			str += br3 + '</b></b></td></tr>';
		}
		if (br4 != 0)
		{
			str += '	<tr style="';
			if (c % 2 == 0) str += 'background-color: #F4BB8A;';
			c++;
			str += '"><td>Броня пояса</td><td align="right"><b>';
			if (br4 > 0) str += '+';
			str += br4 + '</b></b></td></tr>';
		}
		if (br5 != 0)
		{
			str += '	<tr style="';
			if (c % 2 == 0) str += 'background-color: #F4BB8A;';
			c++;
			str += '"><td>Броня ног</td><td align="right"><b>';
			if (br5 > 0) str += '+';
			str += br5 + '</b></b></td></tr>';
		}

		if (krit != 0)
		{
			str += '	<tr style="';
			if (c % 2 == 0) str += 'background-color: #F4BB8A;';
			c++;
			str += '"><td>Крит</td><td align="right"><b>';
			if (krit > 0) str += '+';
			str += krit + '</b></b></td></tr>';
		}
		if (unkrit != 0)
		{
			str += '<tr style="';
			if (c % 2 == 0) str += 'background-color: #F4BB8A;';
			c++;
			str += '"><td>Антикрит</td><td align="right"><b>';
			if (unkrit > 0) str += '+';
			str += unkrit + '</b></b></td></tr>';
		}
		if (uv != 0)
		{
			str += '	<tr style="';
			if (c % 2 == 0) str += 'background-color: #F4BB8A;';
			c++;
			str += '"><td>Уворот</td><td align="right"><b>';
			if (uv > 0) str += '+';
			str += uv + '</b></b></td></tr>';
		}
		if (unuv != 0)
		{
			str += '<tr style="';
			if (c % 2 == 0) str += 'background-color: #F4BB8A;';
			c++;
			str += '"><td>Антиуворот</td><td align="right"><b>';
			if (unuv > 0) str += '+';
			str += unuv + '</b></b></td></tr>';
		}

		if (mkrit != 0)
		{
			str += '<tr style="';
			if (c % 2 == 0) str += 'background-color: #F4BB8A;';
			c++;
			str += '"><td>Мощность крита</td><td align="right"><b>';
			if (mkrit > 0) str += '+';
			str += mkrit + '</b></b></td></tr>';
		}
		if (pblock != 0)
		{
			str += '<tr style="';
			if (c % 2 == 0) str += 'background-color: #F4BB8A;';
			c++;
			str += '"><td>Пробой блока</td><td align="right"><b>';
			if (pblock > 0) str += '+';
			str += pblock + '</b></b></td></tr>';
		}
		if (mblock != 0)
		{
			str += '<tr style="';
			if (c % 2 == 0) str += 'background-color: #F4BB8A;';
			c++;
			str += '"><td>Мощность блока</td><td align="right"><b>';
			if (mblock > 0) str += '+';
			str += mblock + '</b></b></td></tr>';
		}
		if (pbr != 0)
		{
			str += '	<tr style="';
			if (c % 2 == 0) str += 'background-color: #F4BB8A;';
			c++;
			str += '"><td>Пробой брони</td><td align="right"><b>';
			if (pbr > 0) str += '+';
			str += pbr + '</b></b></td></tr>';
		}
		if (kbr != 0)
		{
			str += '	<tr style="';
			if (c % 2 == 0) str += 'background-color: #F4BB8A;';
			c++;
			str += '"><td>Крепость брони</td><td align="right"><b>';
			if (kbr > 0) str += '+';
			str += kbr + '</b></b></td></tr>';
		}

		str += '	</table>';
	}

	if (magic && magic.length)
	{
		str += '<table width="100%" class="table sm" style="margin: 5px 0; border: 1px solid #d8ad83">';
		str += '<tr class=""><td style="font-size:10px;" colspan=2><b>Встроенная магия:</b><br>' + magic + '</td></tr>';
		str += '</table>';
	}
	if (grav && grav.length && grav != 0)
	{
		str += '<table width="100%" class="table sm" style="margin: 5px 0; border: 1px solid #d8ad83">';
		str += '<tr class=""><td style="font-size:10px;" colspan=2><b>Выгравирована надпись:</b><br>' + grav + '</td></tr>';
		str += '</table>';
	}
	if (about && about.length)
	{
		str += '<table width="100%" class="table sm" style="margin: 5px 0; border: 1px solid #d8ad83">';
		str += '<tr class=""><td style="font-size:10px;" colspan=2><b>Дополнительная информация:</b><br>' + about + '</td></tr>';
		str += '</table>';
	}

	str += '<div style="height:5px"></div></td><td style="font-size:10px;background: url(\'/images/header/stm1-r.gif\') repeat-y top right">';
	str += '<tr><td style="font-size:1px;" height="5"><img src="/images/header/stm1-bl.gif" width="22" height="5" alt="" border="0"><br></td><td style="font-size:1px;background: url(\'/images/header/stm1-b.gif\') repeat-x bottom"></td><td style="font-size:1px;"><img src="/images/header/stm1-br.gif" width="22" height="5" alt="" border="0"><br></td></tr>';
	str += '</table>';
	str += '</div>';

	return str;
}