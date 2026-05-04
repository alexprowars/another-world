{{ partial('shared/city_header', ['title': 'Рынок', 'credits': user.credits]) }}

<div class="textblock">
	<div class="row">
		<div class="col-xs-9">
			{% if message is not empty %}
				<p class="message alert-danger">{{ message }}</p>
			{% endif %}
			{% if otdel == 100 %}
				{{ partial('shared/city/komis/sale', ['objects': objects, 'otdel': otdel, 'price': 1]) }}
			{% elseif otdel < 40 %}
				{{ partial('shared/city/komis/buy', ['objects': objects, 'otdel': otdel, 'price': 1]) }}
			{% endif %}
		</div>
		<div class="col-xs-3">
			<div class="shopnav">
				<a href="{{ url('map/') }}?otdel=100"><img src='/images/images/shop_sale.gif' alt='Продать предметы'></a>
				<a href="{{ url('map/') }}?otdel={{ otdel }}"><img src='/images/images/refresh.gif' alt='Обновить'></a>
				<a href="{{ url('map/') }}?refer=20"><img src='/images/images/back.gif' alt='Вернуться'></a>
			</div>
			<div align='center' class="shopotdels">
				<table>
					<tr>
						<td width='100%' colspan='2' align='center'><b>Оружие</b></td>
					</tr>
					<tr>
						<td width='100%' colspan='2' align='center'><a href='{{ url('map/') }}?otdel=1'>Оружие</a></td>
					</tr>
				</table>
			</div>
			<div align='center' class="shopotdels">
				<table>
					<tr>
						<td width='100%' colspan='2' align='center'><b>Амуниция</b></td>
					</tr>
					<tr>
						<td width='50%' align='center'><a href='{{ url('map/') }}?otdel=8'>Шлемы</a></td>
						<td width='50%' align='center'><a href='{{ url('map/') }}?otdel=2'>Доспехи</a></td>
					</tr>
					<tr>
						<td width='50%' align='center'><a href='{{ url('map/') }}?otdel=25'>Штаны</a></td>
						<td width='50%' align='center'><a href='{{ url('map/') }}?otdel=10'>Нарукавники</a></td>
					</tr>
					<tr>
						<td width='50%' align='center'><a href='{{ url('map/') }}?otdel=9'>Перчатки</a></td>
						<td width='50%' align='center'><a href='{{ url('map/') }}?otdel=5'>Щиты</a></td>
					</tr>
					<tr>
						<td width='50%' align='center'><a href='{{ url('map/') }}?otdel=7'>Пояса</a></td>
						<td width='50%' align='center'><a href='{{ url('map/') }}?otdel=6'>Обувь</a></td>
					</tr>
					<tr>
						<td colspan="2" align='center'><a href='{{ url('map/') }}?otdel=11'>Рубахи</a></td>
					</tr>
				</table>
			</div>
			<div align='center' class="shopotdels">
				<table>
					<tr>
						<td width='100%' colspan='2' align='center'><b>Ювелирные украшения</b></td>
					</tr>
					<tr>
						<td width='50%' align='center'><a href='{{ url('map/') }}?otdel=4'>Ожерелья</a></td>
						<td width='50%' align='center'><a href='{{ url('map/') }}?otdel=3'>Кольца</a></td>
					</tr>
					<tr>
						<td width='100%' colspan='2' align='center'><a href='{{ url('map/') }}?otdel=24'>Серьги</a></td>
					</tr>
				</table>
			</div>
			<div align='center' class="shopotdels">
				<table>
					<tr>
						<td width='100%' colspan='4' align='center'><b>Магия</b></td>
					</tr>
					<tr>
						<td width='50%' align='center' colspan='2'><a href='{{ url('map/') }}?otdel=12'>Свитки</a></td>
						<td width='50%' align='center' colspan='2'><a href='{{ url('map/') }}?otdel=16'>Зелья</a></td>
					</tr>
				</table>
			</div>
			<div align='center' class="shopotdels">
				<table>
					<tr>
						<td width='100%' colspan='4' align='center'><b>Прочее</b></td>
					</tr>
					<tr>
						<td width='50%' align='center' colspan='2'><a href='{{ url('map/') }}?otdel=14'>Элексиры</a></td>
						<td width='50%' align='center' colspan='2'><a href='{{ url('map/') }}?otdel=21'>Документы</a></td>
					</tr>
					<tr>
						<td width='50%' align='center' colspan='2'><a href='{{ url('map/') }}?otdel=18'>Инструмент</a></td>
						<td width='50%' align='center' colspan='2'><a href='{{ url('map/') }}?otdel=19'>Ресурс</a></td>
					</tr>
					<tr>
						<td width='50%' align='center' colspan='2'><a href='{{ url('map/') }}?otdel=20'>Драгоценный камень</a></td>
						<td width='50%' align='center' colspan='2'><a href='{{ url('map/') }}?otdel=26'>Магический предмет</a></td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</div>