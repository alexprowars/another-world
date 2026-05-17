<template>
	<div class="frame">
		<div class="frame-border left"></div>
		<div class="content">
			<div class="top">
				<Header/>
				<div class="mainFrame">
					<div class="w-full max-w-400 pb-10 mt-2 mx-5 h-fit">
						<slot/>
					</div>
				</div>
			</div>
			<div class="bottom">
				<div class="line"></div>
				<div class="chat">
					<table>
						<tr>
							<td>
								<ChatList/>
							</td>
							<td class="online">
								<Online/>
							</td>
						</tr>
					</table>
				</div>
				<div class="buttons">
					<div class="flex">
						<div><img src="/assets/images/frames/lbd.jpg" width="16" height="39" alt=""></div>
						<div class="in" style="width:100%;">
							<input ref="textRef" type="text" maxlength="180" name="text" id="msg" class="txt" v-model="chatMessage">
						</div>
						<div><img src="/assets/images/frames/lbd1.jpg" class="h-full" alt=""></div>
						<div class="btnfon">
							<input type="submit" value="" id="chsub" class="subbtn" v-tooltip="'Отправить'" style="width:75px;" @click.prevent="addMessage"/>
						</div>
						<div><img src="/assets/images/frames/lbd2.jpg" class="h-full" alt=""></div>
						<div><img src="/assets/images/frames/l_m.jpg" class="h-full" alt=""></div>
						<div>
							<a href="javascript:;" onclick="window.frames.main.confirmDialog('Чат', 'Очистить окно чата?', 'ClearChat()')" title="Очистить окно чата">
								<img src="/assets/images/menu/b_m1.jpg" class="h-full" alt="">
							</a>
						</div>
						<div><img src="/assets/images/frames/lbd3.jpg" class="h-full" alt=""></div>
						<div>
							<a href="javascript:showSmiles()" v-tooltip="'Смайлики'">
								<img src="/assets/images/menu/b_m2.jpg" class="h-full" alt="">
							</a>
						</div>
						<!--<div><img src="/assets/images/frames/lbd4.jpg" width="50" height="39" alt=""></div>-->
						<template v-if="user.tribe">
							<div><img src="/assets/images/frames/lbd3.jpg" class="h-full" alt=""></div>
							<div>
								<Link href="/tribe" v-tooltip="'Клан'">
									<img src="/assets/images/menu/b_m4.jpg" class="h-full" alt="">
								</Link>
							</div>
						</template>
						<template v-if="user.level >= 6 || user.isAdmin">
							<div><img src="/assets/images/frames/lbd3.jpg" class="h-full" alt=""></div>
							<div>
								<Link href="/transfer" v-tooltip="'Передача предметов / кредитов'">
									<img src="/assets/images/menu/b_m5.jpg" class="h-full" alt="">
								</Link>
							</div>
						</template>
						<template v-if="(user.rank >= 10 && user.rank < 15) || user.rank >= 98">
							<div><img src="/assets/images/frames/lbd3.jpg" class="h-full" alt=""></div>
							<div>
								<Link href="/guard" v-tooltip="'Инквизиция'">
									<img src="/assets/images/menu/b_m6.jpg" class="h-full" alt="">
								</Link>
							</div>
						</template>
						<div><img src="/assets/images/frames/lbd5.jpg" class="h-full" border="0" alt="" /></div>
						<div class="timefon">
							<div data-allow-mismatch class="timer">{{ $formatDate(now, 'HH:mm') }}</div>
						</div>
					</div>
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
		<div class="frame-border right"></div>
	</div>
	<ModalsContainer />
</template>

<script setup>
	import { Link } from '@inertiajs/vue3';
	import { computed, provide, ref, useTemplateRef, watch } from 'vue';
	import useState from '~/composables/useState.js';
	import { useNow } from '@vueuse/core';
	import Online from '~/components/Layout/Online.vue';
	import useEcho from '~/composables/useEcho.js';
	import ChatList from '~/components/Layout/ChatList.vue';
	import useChat from '~/composables/useChat.js';
	import { setLocale } from '~/i18n.js';
	import dayjs from 'dayjs';
	import Header from '~/components/Layout/Header.vue';
	import { ModalsContainer } from 'vue-final-modal';

	const state = useState();
	const user = computed(() => state.user);
	const now = useNow({ interval: 1000 });

	const chat = useChat();
	const echo = useEcho();
	provide('echo', echo);
	provide('chat', chat);

	setLocale(state.locale);
	dayjs.locale(state.locale);

	const chatMessage = ref('');
	const textRef = useTemplateRef('textRef');

	watch(chatMessage, () => {
		textRef.value.focus()
	});

	function addMessage() {
		chat.sendMessage(chatMessage.value);
		chatMessage.value = '';
	}

	if (user.value) {
		echo?.channel('chat')
			.listen('ChatPublicMessage', ({ message }) => {
				chat.addMessage(message);
			});

		echo?.private('user.' + user.value.id)
			.listen('ChatPrivateMessage', ({ message }) => {
				chat.addMessage(message);
			})
	}

	var newbieCount = 0;
	var newbieMessages = [];
	newbieMessages[0] = 'Здравствуйте, вы попали в славный мир Another World. Я помогу вам освоиться здесь.';
	newbieMessages[1] = 'Прежде всего вы должны распределить свободные параметры, такие как: сила, удача, ловкость и выносливость. Разум на нулевом уровне качать нет смысла. Чтобы увеличить параметры надо нажать на "Есть свободные статы!", и в появившемся окне, сделать выбор статов.';
	newbieMessages[2] = 'Не спешите выбирать между энергией и выносливостью, т.к. этот выбор определит вашу будущую раскачку под мага или воина соответственно. Выбор можно сделать в любой момент.';
	newbieMessages[3] = 'Теперь следует приобрести тренировочный нож за 2 золота, он значительно увеличит наносимый вами урон. Чтобы попасть в любое здание вначале надо нажать  кнопку "Город", расположенную в верхнем фрэйме, затем выбрать здание, в нашем случае "Магазин", он находится на "Торговой площади".';
	newbieMessages[4] = 'Возращаемся на Арену и начинаем свой путь к первому уровню! Опыт игроки набирают в поединках, на нулевом уровне доступны физические поединки (1х1) и бои с вашим клоном в тренировочной комнате. В бою вы можете сделать 1 удар и поставить 1 блок (без щита).';
	newbieMessages[5] = 'Удачи вам, на этом не лёгком пути к славе и победам.';

	if (user.value.exp === 0) {
		setTimeout(() => newbieSend(), 30000);
	}

	function newbieSend() {
		if (typeof newbieMessages[newbieCount] != 'undefined') {

			chat.addMessage({
				date: new Date(),
				user: 'Коментатор',
				tou: [],
				toi: [],
				text: newbieMessages[newbieCount],
				private: true,
				me: true,
				my: false,
			});

			setTimeout(newbieSend, 45000);

			newbieCount++;
		}
	}
</script>