<template>
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
								<Link href="/map">
									<div class="icon_1"></div>
									<div class="icon_1_text">Город</div>
								</Link>
							</span>
							<span>
								<Link href="/edit">
									<div class="icon_2"></div>
									<div class="icon_2_text">Персонаж</div>
								</Link>
							</span>
							<span>
								<Link href="/library">
									<div class="icon_4"></div>
									<div class="icon_4_text">Библиотека</div>
								</Link>
							</span>
						</div>
						<div class="center logo"></div>
						<div class="right">
							<span>
								<Link href="/battle">
									<div class="icon_5"></div>
									<div class="icon_5_text">Поединки</div>
								</Link>
							</span>
							<span>
								<Link href="/person/work">
									<div class="icon_6"></div>
									<div class="icon_6_text">Заработок</div>
								</Link>
							</span>
							<span>
								<Link href="/pay">
									<div class="icon_7"></div>
									<div class="icon_7_text">Платина</div>
								</Link>
							</span>
						</div>
					</div>
				</div>
				<div class="mainFrame" id="main" width="100%">
					<slot/>
				</div>
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
							<td><img src="/assets/images/frames/lbd.jpg" width='16' height='39' alt=''></td>
							<td class='in' style='width:100%;'><input type="text" maxlength="180" name="text" id="msg" class="txt"></td>
							<td><img src='/assets/images/frames/lbd1.jpg' width='20' height='39' alt=''/></td>
							<td class="btnfon"><input type="submit" value="" id="chsub" class="subbtn" title="Отправить" style="width:75px;" onclick="addMessage()"/></td>
							<td><img src='/assets/images/frames/lbd2.jpg' width='10' height='39' alt=''></td>
							<td><img src="/assets/images/frames/l_m.jpg" width="20" height="39" alt=""/></td>
							<td>
								<a href="javascript:;" onclick="window.frames.main.confirmDialog('Чат', 'Очистить окно чата?', 'ClearChat()')" title="Очистить окно чата">
									<img src="/assets/images/menu/b_m1.jpg" width="40" height="39">
								</a>
							</td>
							<td><img src='/assets/images/frames/lbd3.jpg' width="11" height="39" alt=""></td>
							<td>
								<a href="javascript:showSmiles()" title="Смайлики">
									<img src="/assets/images/menu/b_m2.jpg" width="40" height="39">
								</a>
							</td>
							<!--<td><img src='/images/frames/lbd4.jpg' width='50' height='39' alt=''></td>-->
							@if($user->tribe_id)
								<td><img src="/assets/images/frames/lbd3.jpg" width="11" height="39" alt=""></td>
								<td>
									<Link href="/tribe" title='Клан'>
										<img src='/assets/images/menu/b_m4.jpg' width="40" height="39">
									</Link>
								</td>
							@endif
							@if($user->level >= 6 || $user->isAdmin())
								<td><img src="/assets/images/frames/lbd3.jpg" width="11" height="39" alt=""></td>
								<td>
									<Link href="/transfer" title='Передача предметов / кредитов'>
										<img src='/assets/images/menu/b_m5.jpg' width="40" height="39">
									</Link>
								</td>
							@endif
							@if(($user->rank >= 10 && $user->rank < 15) || $user->rank >=98)
								<td><img src="/assets/images/frames/lbd3.jpg" width="11" height="39" alt=""></td>
								<td>
									<Link href="/guard" title='Инквизиция'>
										<img src='/assets/images/menu/b_m6.jpg' width="40" height="39">
									</Link>
								</td>
							@endif
							<td><img src="/assets/images/frames/lbd5.jpg" width="51" height="39" border="0" alt="" /></td>
							<td valign="middle" class="timefon">
								<div id="clock" class="timer"></div>
								scri type="text/javascript">UpdateClock();/scri
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
</template>

<script setup>
	import { Link } from '@inertiajs/vue3';

</script>