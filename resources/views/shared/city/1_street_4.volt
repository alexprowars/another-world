{{ partial('shared/city_header', ['title': 'Прихожая']) }}
<div class="textblock">
	<table class='ltable'>
		<tr>
			<td style="vertical-align: top">
				<ul class="mapHints">
					<li><a href='{{ url('map/') }}?room=1'>Вход на арену</a>
					<li><a href='{{ url('map/') }}?room=9'>Академия</a>
					<li><a href='{{ url('map/') }}?room=103'>Выход в город</a>
				</ul>
			</td>
			<td align="center" valign="middle" width="690">
				<div style="position:relative;width:690px;" class="mapImage">
					<img src="/images/world/arena.jpg" alt="">

					<div style="position:absolute; left:320px; top:30px;">
						<a href="{{ url('map/') }}?room=1"><img src="/images/world/0.gif" width="141" height="185" alt='Вход на арену' class="aFilter"></a>
					</div>
					<div style="position:absolute; left:0; top:90px;">
						<a href="{{ url('map/') }}?room=103"><img src=/images/world/0.gif width="60" height="165" alt='Выход в город' class="aFilter"></a>
					</div>
					<div style="position:absolute; left:100px; top:75px;">
						<a href="{{ url('map/') }}?room=9"><img src=/images/world/0.gif width="70" height="165" alt='Академия' class="aFilter"></a>
					</div>
					<div style="position:absolute; left:610px; top:80px;">
						<img src=/images/world/0.gif width="70" height="165" alt='Склад' class="aFilter">
					</div>
				</div>
			</td>
		</tr>
	</table>
</div>