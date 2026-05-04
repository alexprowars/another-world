{{ partial('shared/city_header', ['title': 'Парк']) }}
<div class="textblock">
	<table class='ltable'>
		<tr>
			<td style="vertical-align: top">
				<div style="text-align:justify; width:98%; padding: 0 10px 10px" class="small">Красивая, ровно постриженная и высокая трава, резные и весьма удобные деревянные лавочки, чистый воздух, тихая, приятная атмосфера - что может быть лучше для полноценного отдыха? Большинство посетителей парка приходят сюда для расслабления. Остальные же хотят развлечений - испытать свою удачу в казино или лотерее, а временами и выбрать подарок для близких и уважаемых людей. Это любимое место путешественников, ведь по выстеленным ровной брусчаткой парковым дорожкам, разбегающимся во все четыре стороны, можно уйти очень далеко…</div>

				<ul class="mapHints">
					<li><a href='{{ url('map/') }}?room=103'>Торговая площадь</a>
					<li><a href='{{ url('map/') }}?room=10'>Башня магов</a>
					<li><a href='{{ url('map/') }}?room=16'>Центр занятости</a>
					<li><a href='{{ url('map/') }}?room=13'>Сувениры</a>
					<li><a href='{{ url('map/') }}?room=105'>Промышленная зона</a>
				</ul>
			</td>
			<td>
				<div style="position:relative;width: 550px;">
					<img src="/images/world/city/{{ city }}/2_bg.jpg" alt="" width="550" height="300"/>

					<div style="position:absolute; left:37px; top:29px; width:187px; height:193px;">
						<img src="/images/world/city/{{ city }}/2_LV.gif" data-content="Игорный дом" class="tooltip text">
					</div>
					<div style="position:absolute; left:246px; top:174px; width:91px; height:69px;">
						<a href="{{ url('map/') }}?room=16"><img src="/images/world/city/{{ city }}/2_Gift.gif" data-content="Центр занятости" class="tooltip text aFilter"></a>
					</div>
					<div style="position:absolute; left:348px; top:204px; width:71px; height:49px;">
						<a href='{{ url('map/') }}?room=13'><img src="/images/world/city/{{ city }}/2_Loto.gif" data-content="Сувениры" class="tooltip text aFilter"></a>
					</div>
					<div style="position:absolute; left:28px; top:197px; width:96px; height:100px;">
						<img src="/images/world/city/{{ city }}/2_Tavern.gif" data-content="Таверна" class="tooltip text">
					</div>
					<div style="position:absolute; left:7px; top:230px; width:25px; height:24px;">
						<a href="{{ url('map/') }}?room=103"><img src="/images/world/city/{{ city }}/2_Left.gif" data-content="Торговая площадь" class="tooltip text aFilter"></a>
					</div>
					<div style="position:absolute; left:502px; top:265px; width:29px; height:26px;">
						<a href="{{ url('map/') }}?room=105"><img src="/images/world/city/{{ city }}/2_Right.gif" data-content="Промышленная зона" class="tooltip text aFilter"></a>
					</div>
					<div style="position:absolute; left:118px; top:273px; width:19px; height:20px;">
						<img src="/images/world/city/{{ city }}/2_Bottom.gif" data-content="Проход закрыт" class="tooltip text aFilter">
					</div>
					<div style="position:absolute; left:283px; top:20px; width:32px; height:101px;">
						<a href="{{ url('map/') }}?room=10"><img src="/images/world/city/{{ city }}/2_Mage.gif" data-content="Башня Магов" class="tooltip text aFilter"></a>
					</div>
				</div>
			</td>
		</tr>
	</table>
</div>