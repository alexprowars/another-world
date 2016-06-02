{{ partial('shared/city_header', ['title': 'Сувенирная Лавка', 'credits': user.credits]) }}

<div class="textblock">
	<div class="row">
		<div class="col-xs-9">
			{% if message is not empty %}
				<p class="message alert-danger">{{ message }}</p>
			{% endif %}
			{% if otdel < 4 %}
				{{ partial('shared/city/prize/buy', ['objects': objects, 'otdel': otdel]) }}
			{% else %}
				{{ partial('shared/city/prize/gift', ['objects': objects, 'otdel': otdel]) }}
			{% endif %}
		</div>
		<div class="col-xs-3">
			<div class="shopnav">
				<a href="{{ url('map/') }}?otdel={{ otdel }}"><img src='/images/images/refresh.gif' alt='Обновить'></a>
				<a href="{{ url('map/') }}?refer=13"><img src='/images/images/back.gif' alt='Вернуться'></a>
			</div>
			<div align='center' class="shopotdels">
				<table>
					<tr>
						<td colspan='2' align='center'><b>Отделы магазина</b></td>
					</tr>
					<tr>
						<td width='50%' align='center'><a href='{{ url('map/') }}?otdel=1'>Открытки</a></td>
						<td align='center'><a href='{{ url('map/') }}?otdel=2'>Цветы</a></td>
					</tr>
					<tr>
						<td colspan="2" align='center'><a href='{{ url('map/') }}?otdel=3'>Подарки</a></td>
					</tr>
					<tr>
						<td width='100%' colspan='2' align='center'><a href='{{ url('map/') }}?otdel=4'>Подарить</a></td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</div>