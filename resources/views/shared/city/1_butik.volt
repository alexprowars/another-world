<SCRIPT LANGUAGE='JavaScript'>
	var my_proff, my_level, my_strength, my_dex, my_agility, my_vitality, my_razum, type;
	my_proff = {{ user.proff }};
	my_level = {{ user.level }};
	my_strength = {{ user.strength }};
	my_dex = {{ user.dex }};
	my_agility = {{ user.agility }};
	my_vitality = {{ user.vitality }};
	my_razum = {{ user.razum }};
	type = 1;
</SCRIPT>

{{ partial('shared/city_header', ['title': 'Бутик', 'f_credits': user.f_credits]) }}

<div class="textblock">
	<div class="row">
		<div class="col-xs-9">
			{% if message is not empty %}
				<p class="message alert-danger">{{ message }}</p>
			{% endif %}
			{% if otdel == 100 %}
				{{ partial('shared/city/shop/sale', ['objects': objects, 'otdel': otdel, 'price': 2]) }}
			{% elseif otdel < 40 %}
				{{ partial('shared/city/shop/buy', ['objects': objects, 'otdel': otdel, 'price': 2]) }}
			{% endif %}
		</div>
		<div class="col-xs-3">
			<div class="shopnav">
				<a href="{{ url('map/') }}?otdel=100"><img src='/images/images/shop_sale.gif' alt='Продать предметы'></a>
				<a href="{{ url('map/') }}?otdel={{ otdel }}"><img src='/images/images/refresh.gif' alt='Обновить'></a>
				<a href="{{ url('map/') }}?refer=29"><img src='/images/images/back.gif' alt='Вернуться'></a>
			</div>
			<div align='center' class="shopotdels">
				<table>
					<tr>
						<td colspan='2' align='center'><b>Оружие</b></td>
					</tr>
					<tr>
						<td width='50%' align='center'><a href='{{ url('map/') }}?otdel=1'>Ножи</a></td>
						<td align='center'><a href='{{ url('map/') }}?otdel=2'>Мечи</a></td>
					</tr>
					<tr>
						<td width='50%' align='center'><a href='{{ url('map/') }}?otdel=3'>Топоры</a></td>
						<td align='center'><a href='{{ url('map/') }}?otdel=4'>Дубины</a></td>
					</tr>
				</table>
			</div>
			<div align='center' class="shopotdels">
				<table>
					<tr>
						<td colspan='2' align='center'><b>Амуниция</b></td>
					</tr>
					<tr>
						<td width='50%' align='center'><a href='{{ url('map/') }}?otdel=6'>Шлемы</a></td>
						<td align='center'><a href='{{ url('map/') }}?otdel=7'>Рубахи</a></td>
					</tr>
					<tr>
						<td width='50%' align='center'><a href='{{ url('map/') }}?otdel=8'>Тяжёлая броня</a></td>
						<td align='center'><a href='{{ url('map/') }}?otdel=16'>Браслеты</a></td>
					</tr>
					<tr>
						<td width='50%' align='center'><a href='{{ url('map/') }}?otdel=16'>Нарукавники</a></td>
						<td align='center'><a href='{{ url('map/') }}?otdel=10'>Щиты</a></td>
					</tr>
					<tr>
						<td width='50%' align='center'><a href='{{ url('map/') }}?otdel=11'>Пояса</a></td>
						<td align='center'><a href='{{ url('map/') }}?otdel=12'>Обувь</a></td>
					</tr>
					<tr>
						<td width='50%' align='center'><a href='{{ url('map/') }}?otdel=9'>Перчатки</a></td>
						<td align='center'><a href='{{ url('map/') }}?otdel=17'>Штаны</a></td>
					</tr>
				</table>
			</div>
			<div align='center' class="shopotdels">
				<table>
					<tr>
						<td colspan='2' align='center'><b>Ювелирные украшения</b></td>
					</tr>
					<tr>
						<td width='50%' align='center'><a href='{{ url('map/') }}?otdel=13'>Ожерелья</a></td>
						<td width='50%' align='center'><a href='{{ url('map/') }}?otdel=14'>Кольца</a></td>
					</tr>
					<tr>
						<td width='100%' colspan='2' align='center'><a href='{{ url('map/') }}?otdel=15'>Серьги</a></td>
					</tr>
				</table>
			</div>
			<div align='center' class="shopotdels">
				<table>
					<tr>
						<td colspan='2' align='center'><b>Магические предметы</b></td>
					</tr>
					<tr>
						<td width='50%' align='center'><a href='{{ url('map/') }}?otdel=18'>Свитки</a></td>
						<td width='50%' align='center'><a href='{{ url('map/') }}?otdel=19'>Зелья</a></td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</div>