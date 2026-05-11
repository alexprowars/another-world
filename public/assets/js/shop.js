
function ShopItem (otdel, type, id, name, title, price, t_price, vip_price, t, iznos_1, iznos_2, ostatok, zavoz, min_level, min_proff, min_str, min_dex, min_ag, min_vit, min_razum, min_d, max_d, br1, br2, br3, br4, br5, strength, dex, agility, vitality, razum, krit, mkrit, unkrit, uv, unuv, pblock, mblock, pbr, kbr, metk, hp, energy, about, otravl, use_mana, magic) {
	var str; var tip;

	switch (t) {
		case 1: 	tip = "Оружие"; break;
		case 2: 	tip = "Доспех"; break;
		case 3: 	tip = "Кольцо"; break;
		case 4: 	tip = "Ожерелье"; break;
		case 5: 	tip = "Щит"; break;
		case 6: 	tip = "Обувь"; break;
		case 7: 	tip = "Пояс"; break;
		case 8: 	tip = "Шлем"; break;
		case 9: 	tip = "Перчатки"; break;
		case 10: 	tip = "Нарукавники"; break;
		case 11: 	tip = "Рубаха"; break;
		case 12: 	tip = "Свиток"; break;
		case 13: 	tip = "Магический предмет"; break;
		case 14: 	tip = "Зелье"; break;
		case 15: 	tip = "Открытка"; break;
		case 16: 	tip = "Подарок"; break;
		case 17: 	tip = "Цветы"; break;
		case 18: 	tip = "Инструмент"; break;
		case 19: 	tip = "Ресурс"; break;
		case 20: 	tip = "Драгоценный камень"; break;
		case 21: 	tip = "Документ"; break;
		case 22: 	tip = "Квест предмет"; break;
		case 23: 	tip = "Ингридиент"; break;
		case 24: 	tip = "Серьги"; break;
		case 25: 	tip = "Штаны"; break;
	}

	str = "";
	str += "<table class='table item'>";
	str += '<tr>';
	str += '<td align="center" width="40%" style="vertical-align: middle">';
	str += '<img src="/images/items/' + name + '.gif" alt="' + title + '"><br>';

	if (type == 1)
		str += "<a href=\"javascript:;\" onclick=\"confirmDialog('Магазин', 'Купить предмет &quot;" + title + "&quot;?', 'load(\'/map/?otdel=" + otdel + "&buy=" + id + "\')')\"><b>Купить</b></a>";
	if (type == 2)
	{
		str += "<a href='javascript:;' onclick=\"present(" + id + ");\" id='f_" + id + "'>Выгравировать надпись за 150 кр.</a><br><br>";

		if (my_proff == 2)
			str += "<a href=/map/?otdel=" + otdel + "&act=upgrade&id=" + id + ">Увеличить урон предмета за 50 екр.<br>(мин +1 и мах +1)<br>(<i>Максимальная долговечноть -20</i>)</a>";
		else
			str += "<font color=red><b>Перековать может только Кузнец</b></font>";
	}
	if (type == 3)
	{
		str += "<a href=\"#\" onclick=\"confirmDialog('Магазин', 'Починить этот предмет за " + t_price + " кр.?', 'load(\'/map/?otdel=" + otdel + "&iznos=" + id + "\')')\"><b>Починить весь предмет</b></a><br>";
		str += "<a href=\"#\" onclick=\"confirmDialog('Магазин', 'Починить за " + vip_price + " кр.?', 'load(\'/map/?otdel=" + otdel + "&iznos1=" + id + "\')')\"><b>Починить 1 ед.</b></a>";
	}
	if (type == 4)
	{
		str += "<a href=\"#\" onclick=\"confirmDialog('Магазин', 'Вы действительно хотите огранить &quot;" + title + "&quot; ?', 'load(\'/map/?otdel=" + otdel + "&ogran=" + id + "\')')\"><b>Огранить</b></a><br>";
	}

	str += '</td>';
	str += '<td>';

	str += "<small><b>" + title + "</b><br>";
	str += "Гос. цена: <b>" + price + "</b> ";

	if (vip_price > 0)
		str += "екр."; else str += "кр.";

	str += "<br>";

	if (vip_price > 0 && type == 1)
		str += "VIP. цена: <b>" + vip_price + "</b> екр.<br>";

	if (t_price > 0 && type == 1)
		str += "Торговая. цена: <b>" + t_price + "</b> кр.<br>";

	str += "Долговечность: <b>" + iznos_1 + "</b>/<b>" + iznos_2 + "</b></small><br>";
	str += "<small>Тип предмета: <i>" + tip + "</i><br>";

	if (use_mana > 0)
		str += "<small>Затраты маны: <i>" + use_mana + "</i><br>";
	if (ostatok > 0)
		str += "Остаток на складе: <b>" + ostatok + "</b>.";
	if (zavoz > 0)
		str += " Завоз: <b>" + zavoz + "</b>.</small>";
	if (ostatok > 0 || zavoz > 0)
		str += "<br>";

	str += '</td>';
	str += '</tr>';
	str += "<tr>";
	str += "<td>";

	if (min_proff || min_level || min_str || min_dex || min_ag || min_vit || min_razum)
	{
		str += "<small><b>Требования:</b><br>";

		if (min_proff > 0)
		{
			var proff;

			switch (min_proff)
			{
				case 1: proff = "Лекарь"; break;
				case 2: proff = "Кузнец"; break;
				case 3: proff = "Огранщик"; break;
				case 4: proff = "Наёмник"; break;
				case 5: proff = "Шахтёр"; break;
				case 6: proff = "Маг"; break;
				case 7: proff = "Алхимик"; break;
				case 8: proff = "Торговец"; break;
				case 9: proff = "Травник"; break;
			}

			str += '<font color='; if (min_proff != my_proff) str += 'red'; else str += 'black'; str += '>Профессия: '+proff+'</font><br>';
		}
		if (min_level > 0){ str += '<font color='; if (min_level > my_level) str += 'red'; else str += 'black'; str += '>Уровень: '+min_level+'</font><br>';}
		if (min_str > 0){ str += '<font color='; if (min_str > my_strength) str += 'red'; else str += 'black'; str += '>Сила: '+min_str+'</font><br>';}
		if (min_dex > 0){ str += '<font color='; if (min_dex > my_dex) str += 'red'; else str += 'black'; str += '>Удача: '+min_dex+'</font><br>';}
		if (min_ag > 0){ str += '<font color='; if (min_ag > my_agility) str += 'red'; else str += 'black'; str += '>Ловкость: '+min_ag+'</font><br>';}
		if (min_vit > 0){ str += '<font color='; if (min_vit > my_vitality) str += 'red'; else str += 'black'; str += '>Выносливость: '+min_vit+'</font><br>';}
		if (min_razum > 0){ str += '<font color='; if (min_razum > my_razum) str += 'red'; else str += 'black'; str += '>Разум: '+min_razum+'</font><br>';}

		str += "</small>";
	}

	str += "</td><td>";

	if (otravl || hp || energy || min_d || max_d || dex || agility || vitality || razum || br1 || br2 || br3 || br4 || br5 || krit || mkrit || unkrit || uv || unuv || pblock || mblock || pbr || kbr || metk)
	{
		str += "<small><b>Действие предмета:</b><br>";

		if (otravl != 0){ str += "Отравление: "; if (otravl > 0) str += "+"; str += otravl+"<br>";}
		if (hp != 0){ str += "Уровень жизни: "; if (hp > 0) str += "+"; str += hp+"<br>";}
		if (energy != 0){ str += "Уровень маны: "; if (energy > 0) str += "+"; str += energy+"<br>";}
		if (min_d != 0){ str += "Минимальный урон: "; if (min_d > 0) str += "+"; str += min_d+"<br>";}
		if (max_d != 0){ str += "Максимальный урон: "; if (max_d > 0) str += "+"; str += max_d+"<br>";}

		if (strength != 0){ str += "Сила: "; if (strength > 0) str += "+"; str += strength+"<br>";}
		if (dex != 0){ str += "Удача: "; if (dex > 0) str += "+"; str += dex+"<br>";}
		if (agility != 0){ str += "Ловкость: "; if (agility > 0) str += "+"; str += agility+"<br>";}
		if (vitality != 0){ str += "Выносливость: "; if (vitality > 0) str += "+"; str += vitality+"<br>";}
		if (razum != 0){ str += "Разум: "; if (razum > 0) str += "+"; str += razum+"<br>";}

		if (br1 != 0){ str += "Броня головы: "; if (br1 > 0) str += "+"; str += br1+"<br>";}
		if (br2 != 0){ str += "Броня корпуса: "; if (br2 > 0) str += "+"; str += br2+"<br>";}
		if (br3 != 0){ str += "Броня живота: "; if (br3 > 0) str += "+"; str += br3+"<br>";}
		if (br4 != 0){ str += "Броня пояса: "; if (br4 > 0) str += "+"; str += br4+"<br>";}
		if (br5 != 0){ str += "Броня ног: "; if (br5 > 0) str += "+"; str += br5+"<br>";}

		if (krit != 0){ str += "Критический удар: "; if (krit > 0) str += "+"; str += krit+"<br>";}
		if (unkrit != 0){ str += "Анти критический удар: "; if (unkrit > 0) str += "+"; str += unkrit+"<br>";}
		if (mkrit != 0){ str += "Мощность крит. удара: "; if (mkrit > 0) str += "+"; str += mkrit+"<br>";}
		if (uv != 0){ str += "Уворот: "; if (uv > 0) str += "+"; str += uv+"<br>";}
		if (unuv != 0){ str += "Анти уворот: "; if (unuv > 0) str += "+"; str += unuv+"<br>";}
		if (pblock != 0){ str += "Пробивание блока: "; if (pblock > 0) str += "+"; str += pblock+"<br>";}

		if (mblock != 0){ str += "Мощность блока: "; if (mblock > 0) str += "+"; str += mblock+"<br>";}
		if (pbr != 0){ str += "Пробивание брони: "; if (pbr > 0) str += "+"; str += pbr+"<br>";}
		if (kbr != 0){ str += "Крепкость брони: "; if (kbr > 0) str += "+"; str += kbr+"<br>";}
		if (metk != 0){ str += "Меткость: "; if (metk > 0) str += "+"; str += metk+"<br>";}

		str += "</small>";
	}

	str += "</td></tr><tr><td colspan='2'>";

	if (magic.length)
		str += "<div><small><b>Встроенная магия:</b><br>"+magic+"</small></div>";

	if (about.length)
		str += "<div><small><b>Дополнительная информация:</b><br>"+about+"</small></div>";

	str += "</td></tr></table>";

	return str;
}