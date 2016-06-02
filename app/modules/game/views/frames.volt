{{ getDoctype() }}
<html lang="ru">
    <head>
		{{ getTitle() }}
		{{ assets.outputCss('css') }}
		{{ assets.outputJs('js') }}
		<link href='//fonts.googleapis.com/css?family=Open+Sans:600&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
    </head>
    <body>
		<script type="text/javascript">
			var timestamp = {{ time() }};
			var timezone = 0;
			var serverTime = {{ time() }}000 - Djs + (timezone + {{ date("Z") / 1800 }}) * 1800000;
		</script>
		<input type="hidden" name="message_id" id="message_id" value="0">
		<div class="frame">
			<div class="border left"></div>
			<div class="content">
				<div class="top">
					<div class="header">
						<div class="corner left"></div>
						<div class="corner right"></div>
						<div class="links">
							<div class="left">
								<span>
									<a href="{{ url("map/") }}" target="main">
										<div class="icon_1"></div><div class="icon_1_text">Город</div>
									</a>
								</span>
								<span>
									<a href="{{ url("edit/") }}" target="main">
										<div class="icon_2"></div><div class="icon_2_text">Персонаж</div>
									</a>
								</span>
								<span>
									<a href="{{ url("library/") }}" target="main">
										<div class="icon_4"></div><div class="icon_4_text">Библиотека</div>
									</a>
								</span>
							</div>
							<div class="center logo"></div>
							<div class="right">
								<span>
									<a href="{{ url("battle/") }}" target="main">
										<div class="icon_5"></div><div class="icon_5_text">Поединки</div>
									</a>
								</span>
								<span>
									<a href="{{ url("pers/work/") }}" target="main">
										<div class="icon_6"></div><div class="icon_6_text">Заработок</div>
									</a>
								</span>
								<span>
									<a href="{{ url("pay/") }}" target="main">
										<div class="icon_7"></div><div class="icon_7_text">Платина</div>
									</a>
								</span>
							</div>
						</div>
					</div>
					<iframe src="{{ url("pers/") }}" class="mainFrame" name="main" id="main" width="100%" height="100%"></iframe>
				</div>
				<div class="bottom">
					<div class="line" id="linefon"></div>
					<div class="chat">
						<table>
							<tr>
								<td>
									<div class="shoutbox scrollbox" id="shoutbox"></div>
								</td>
								<td class="online">
									<div id="chatList" class="scrollbox"></div>
								</td>
							</tr>
						</table>
					</div>
					<div class="buttons">
						<table width="100%">
							<tr>
								<td><img src='/images/frames/lbd.jpg' width='16' height='39' alt=''></td>
								<td class='in' style='width:100%;'><input type="text" maxlength="180" name="text" id="msg" class="txt"></td>
								<td><img src='/images/frames/lbd1.jpg' width='20' height='39' alt=''/></td>
								<td class="btnfon"><input type="submit" value="" id="chsub" class="subbtn" title="Отправить" style="width:75px;" onclick="addMessage()"/></td>
								<td><img src='/images/frames/lbd2.jpg' width='10' height='39' alt=''></td>
								<td><img src="/images/frames/l_m.jpg" width="20" height="39" alt=""/></td>
								<td>
									<a href="javascript:;" onclick="window.frames.main.confirmDialog('Чат', 'Очистить окно чата?', 'ClearChat()')" title="Очистить окно чата">
									<img src="/images/menu/b_m1.jpg" width="40" height="39">
									</a>
								</td>
								<td><img src='/images/frames/lbd3.jpg' width="11" height="39" alt=""></td>
								<td>
									<a href="javascript:showSmiles()" title="Смайлики">
										<img src="/images/menu/b_m2.jpg" width="40" height="39">
									</a>
								</td>
								<!--<td><img src='/images/frames/lbd4.jpg' width='50' height='39' alt=''></td>-->
								{% if user.tribe > 0 %}
									<td><img src="/images/frames/lbd3.jpg" width="11" height="39" alt=""></td>
									<td>
										<a href="{{ url("tribe/") }}" target="main" title='Клан'>
											<img src='/images/menu/b_m4.jpg' width="40" height="39">
										</a>
									</td>
								{% endif %}
								{% if user.level >= 6 or user.isAdmin() %}
									<td><img src="/images/frames/lbd3.jpg" width="11" height="39" alt=""></td>
									<td>
										<a href="{{ url("transfers/") }}" target="main" title='Передача предметов / кредитов'>
											<img src='/images/menu/b_m5.jpg' width="40" height="39">
										</a>
									</td>
								{% endif %}
								{% if (user.rank >= 10 and user.rank < 15) or user.rank >=98 %}
									<td><img src="/images/frames/lbd3.jpg" width="11" height="39" alt=""></td>
									<td>
										<a href="{{ url("guard/") }}" target="main" title='Инквизиция'>
											<img src='/images/menu/b_m6.jpg' width="40" height="39">
										</a>
									</td>
								{% endif %}
								<td><img src="/images/frames/lbd5.jpg" width="51" height="39" border="0" alt="" /></td>
								<td valign="middle" class="timefon">
									<div id="clock" class="timer"></div>
									<script type="text/javascript">UpdateClock();</script>
								</td>
							</tr>
						</table>
					</div>
					<div class="corner left"></div>
					<div class="corner right"></div>
				</div>
				<div class="contextMenu" id="chatMenu" style="display: none">
					<ul>
						<li id="message">Обратиться</li>
						<li id="private">Написать в приват</li>
						<li id="mail">Личное сообщение</li>
						<li id="info">Профиль игрока</li>
					</ul>
				</div>
				<div id="smiles" style="display:none"></div>
			</div>
			<div class="border right"></div>
		</div>
		{% if request.get('viewer_id') is not empty %}
			<script src="//vk.com/js/api/xd_connection.js" type="text/javascript"></script>
			<script type="application/javascript">
				$(window).load(function()
				{
					  VK.init(function() { console.log('vk init success'); }, function() {}, '5.30');
				});
			</script>
		{% endif %}
		{% if request.get('api_server') is not empty %}
			<script src="{{ request.get('api_server') }}js/fapi5.js" type="text/javascript"></script>
			<script type="text/javascript">
				FAPI.init('{{ request.get('api_server') }}', '{{ request.get('apiconnection') }}',
					function()
					{
						console.log('ok api initialized');
					}
					, function(error)
					{
						alert("API initialization failed: "+error);
					}
				);
			</script>
		{% endif %}
		<!-- Yandex.Metrika counter -->
		<script type="text/javascript">
		(function (d, w, c) {
		    (w[c] = w[c] || []).push(function() {
		        try {
		            w.yaCounter30874551 = new Ya.Metrika({id:30874551});
		        } catch(e) { }
		    });

		    var n = d.getElementsByTagName("script")[0],
		        s = d.createElement("script"),
		        f = function () { n.parentNode.insertBefore(s, n); };
		    s.type = "text/javascript";
		    s.async = true;
		    s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

		    if (w.opera == "[object Opera]") {
		        d.addEventListener("DOMContentLoaded", f, false);
		    } else { f(); }
		})(document, window, "yandex_metrika_callbacks");
		</script>
		<noscript><div><img src="//mc.yandex.ru/watch/30874551" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
		<!-- /Yandex.Metrika counter -->
    </body>
</html>