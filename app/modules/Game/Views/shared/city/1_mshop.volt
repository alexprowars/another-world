{{ partial('shared/city_header', ['title': 'Башня Мага', 'credits': user.credits]) }}

<div class="textblock">
	<div class="row">
		<div class="col-xs-9">
			{% if message is not empty %}
				<p class="message alert-danger">{{ message }}</p>
			{% endif %}
			{{ partial('shared/city/shop/buy', ['objects': objects, 'otdel': otdel, 'price': 1]) }}
		</div>
		<div class="col-xs-3">
			<div class="shopnav">
				<a href="{{ url('map/') }}?otdel={{ otdel }}"><img src='/images/images/refresh.gif' alt='Обновить'></a>
				<a href="{{ url('map/') }}?refer=10"><img src='/images/images/back.gif' alt='Вернуться'></a>
			</div>
			<div align='center' class="shopotdels">
				<table>
					<tr>
						<td width='100%' colspan='2' align='center'><u>Свитки:</u></td>
					</tr>
					<tr>
						<td width='50%' align='center'><a href='{{ url('map/') }}?otdel=1'>Восст. здоровья</a></td>
						<td width='50%' align='center'><a href='{{ url('map/') }}?otdel=2'>Восст. маны</a></td>
					</tr>
					<tr>
						<td width='50%' align='center'><a href='{{ url('map/') }}?otdel=3'>Лечение травм</a></td>
						<td width='50%' align='center'><a href='{{ url('map/') }}?otdel=4'>Иммунитет</a></td>
					</tr><tr>
						<td width='50%' align='center'><a href='{{ url('map/') }}?otdel=5'>Нападение</a></td>
						<td width='50%' align='center'><a href='{{ url('map/') }}?otdel=6'>Боевая магия</a></td>
					</tr>
					<tr>
						<td width='100%' colspan='2' align='center'><a href='{{ url('map/') }}?otdel=7'>Прочее</a></td>
					</tr>
				</table>
			</div>
			<div align='center' class="shopotdels">
				<table>
					<tr>
						<td width='100%' colspan='2' align='center'><u>Зелья:</u></td>
					</tr>
					<tr>
						<td width='50%' align='center'><a href='{{ url('map/') }}?otdel=8'>Зелья</a></td>
						<td width='50%' align='center'><a href='{{ url('map/') }}?otdel=12'>Ингридиенты</a></td>
					</tr>
				</table>
			</div>
			<div align='center' class="shopotdels">
				<table>
					<tr>
						<td width='100%' colspan='2' align='center'><u>Магические предметы:</u></td>
					</tr>
					<tr>
						<td width='33%' align='center'><a href='{{ url('map/') }}?otdel=9'>Посохи</a></td>
						<td width='33%' align='center'><a href='{{ url('map/') }}?otdel=10'>Одеяние</a></td>
					</tr>
					<tr>
						<td width='100%' colspan='2' align='center'><a href='{{ url('map/') }}?otdel=11'>Маг. вещи</a></td>
					</tr>
					<tr>
						<td width='100%' colspan='2' align='center'><a href='{{ url('map/') }}?otdel=13'>Руны</a></td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</div>