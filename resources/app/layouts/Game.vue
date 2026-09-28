<template>
	<div ref="shellRef" class="game-shell" :class="{ 'is-resizing-chat': chatResize !== null }">
		<a class="game-skip-link" href="#game-content">Перейти к игре</a>
		<Header />
		<main ref="stageRef" id="game-content" class="game-stage" tabindex="-1">
			<div class="game-stage-content">
				<slot />
			</div>
		</main>
		<section ref="chatPanelRef" class="game-social" :style="chatPanelStyle" :class="{ 'is-collapsed': chatCollapsed, 'show-players': showPlayers }">
			<div
				v-if="!chatCollapsed"
				class="chat-resizer"
				role="separator"
				tabindex="0"
				@pointerdown="startChatResize"
				@pointermove="resizeChat"
				@pointerup="finishChatResize"
				@pointercancel="finishChatResize"
				@lostpointercapture="finishChatResize"
				@keydown="resizeChatWithKeyboard"
			></div>
			<div class="social-toolbar">
				<div class="social-title">
					<GameIcon name="chat" />
					<h2>Общий чат</h2>
				</div>
				<div class="social-actions">
					<Link v-if="user.tribe" href="/tribe" class="social-action" title="Клан">
						<GameIcon name="clan" />
						<span>Клан</span>
					</Link>
					<Link v-if="user.level >= 6 || user.admin" href="/transfers" class="social-action" title="Передачи">
						<GameIcon name="transfer" />
						<span>Передачи</span>
					</Link>
					<Link v-if="(user.rank >= 10 && user.rank < 15) || user.rank >= 98" href="/guard" class="social-action" title="Инквизиция">
						<GameIcon name="justice" />
						<span>Инквизиция</span>
					</Link>
					<button
						type="button"
						class="social-action players-toggle"
						@click="
							showPlayers = !showPlayers;
							chatCollapsed = false;
						"
					>
						<GameIcon name="users" />
						<span>Игроки</span>
					</button>
					<button
						type="button"
						class="ui-icon-button ui-icon-button--quiet"
						title="Обновить список игроков"
						:disabled="onlineRef?.loading"
						@click="onlineRef?.refresh()"
					>
						<GameIcon name="refresh" />
						<span class="sr-only">Обновить список игроков</span>
					</button>
					<button type="button" class="ui-icon-button ui-icon-button--quiet" title="Очистить окно чата" @click="chat.clear()">
						<GameIcon name="trash" />
					</button>
					<button type="button" class="ui-icon-button ui-icon-button--quiet collapse-chat" @click="chatCollapsed = !chatCollapsed">
						<GameIcon name="chevron" />
					</button>
				</div>
			</div>
			<div v-show="!chatCollapsed" id="game-chat-body" class="social-body">
				<div class="conversation">
					<ChatList @player="addressPlayer" @private="addressPrivate" />
					<form class="chat-composer" @submit.prevent="addMessage">
						<div class="smile-picker-wrap" ref="smilePickerRef" @keydown.esc="smilesOpen = false">
							<button type="button" class="ui-icon-button ui-icon-button--quiet" @click="smilesOpen = !smilesOpen">
								<GameIcon name="smile" />
							</button>
							<div v-if="smilesOpen" id="game-smiles" class="smile-picker">
								<button v-for="smile in smiles" :key="smile" type="button" :title="smile" @click="insertSmile(smile)">
									<img :src="'/assets/images/smile/' + smile + '.gif'" alt="" loading="lazy" />
								</button>
							</div>
						</div>
						<label for="msg" class="sr-only">Сообщение в общий чат</label>
						<input
							ref="textRef"
							id="msg"
							v-model="chatMessage"
							type="text"
							maxlength="180"
							autocomplete="off"
							placeholder="Напишите сообщение…"
							:disabled="sending"
							@keydown.enter="handleEnter"
						/>
						<span class="message-limit">{{ chatMessage.length }}/180</span>
						<button type="submit" class="ui-button ui-button--compact chat-send" :disabled="!chatMessage.trim() || sending">
							<span>{{ sending ? 'Отправка…' : 'Отправить' }}</span>
							<GameIcon name="send" />
						</button>
					</form>
					<p v-if="sendError" class="chat-send-error" role="alert">{{ sendError }}</p>
				</div>
				<aside id="game-players" class="game-players">
					<Online ref="onlineRef" @player="addressPlayer" @private="addressPrivate" />
				</aside>
			</div>
		</section>
	</div>
	<ModalTarget group="default" class="game-modal-target">
		<ModalOverlay class="dialog-overlay" />
	</ModalTarget>
</template>

<script setup>
	import { Link, router } from '@inertiajs/vue3';
	import { computed, nextTick, onBeforeUnmount, onMounted, provide, ref, useTemplateRef } from 'vue';
	import useState from '~/composables/useState.js';
	import { onClickOutside, useLocalStorage, useResizeObserver } from '@vueuse/core';
	import Online from '~/components/Layout/Online.vue';
	import useEcho from '~/composables/useEcho.js';
	import ChatList from '~/components/Layout/ChatList.vue';
	import useChat, { smiles } from '~/composables/useChat.js';
	import { setLocale } from '~/i18n.js';
	import dayjs from 'dayjs';
	import Header from '~/components/Layout/Header.vue';
	import { ModalOverlay, ModalTarget } from '@kolirt/vue-modal';
	import GameIcon from '~/components/Layout/GameIcon.vue';

	const state = useState();
	const user = computed(() => state.user);

	const chat = useChat();
	const echo = useEcho();
	provide('echo', echo);
	provide('chat', chat);

	setLocale(state.locale);
	dayjs.locale(state.locale);

	const chatMessage = ref('');
	const textRef = useTemplateRef('textRef');
	const onlineRef = useTemplateRef('onlineRef');

	const chatCollapsed = useLocalStorage('game.chat.collapsed', false, { initOnMounted: true });
	const showPlayers = ref(false);
	const smilesOpen = ref(false);
	const sending = ref(false);
	const sendError = ref('');
	const smilePickerRef = useTemplateRef('smilePickerRef');

	const MIN_CHAT_HEIGHT = 160;
	const shellRef = useTemplateRef('shellRef');
	const stageRef = useTemplateRef('stageRef');
	const chatPanelRef = useTemplateRef('chatPanelRef');
	const savedChatHeight = useLocalStorage('game.chat.height', 0, { initOnMounted: true });
	const maxChatHeight = ref(600);
	const chatResize = ref(null);
	const draggedChatHeight = ref(null);
	const chatPanelStyle = computed(() => {
		if (chatCollapsed.value) return {};
		const height = draggedChatHeight.value ?? savedChatHeight.value;
		return {
			height: Number.isFinite(height) && height > 0 ? clampChatHeight(height) + 'px' : undefined,
			maxHeight: maxChatHeight.value + 'px',
		};
	});

	useResizeObserver([shellRef, stageRef, chatPanelRef], () => {
		const shell = shellRef.value;
		const stage = stageRef.value;
		const panel = chatPanelRef.value;
		if (!shell || !stage || !panel) return;
		maxChatHeight.value = Math.max(
			MIN_CHAT_HEIGHT,
			Math.floor(
				shell.getBoundingClientRect().bottom -
					stage.getBoundingClientRect().top -
					parseFloat(getComputedStyle(shell).paddingBottom) -
					parseFloat(getComputedStyle(panel).marginTop) -
					parseFloat(getComputedStyle(stage).minHeight),
			),
		);
	});

	function clampChatHeight(height) {
		return Math.round(Math.min(maxChatHeight.value, Math.max(MIN_CHAT_HEIGHT, height)));
	}

	function startChatResize(event) {
		if (event.button !== 0 || !event.isPrimary || chatResize.value) return;
		event.preventDefault();
		event.currentTarget.focus({ preventScroll: true });
		event.currentTarget.setPointerCapture(event.pointerId);
		chatResize.value = {
			pointerId: event.pointerId,
			startY: event.clientY,
			startHeight: chatPanelRef.value.getBoundingClientRect().height,
		};
	}

	function resizeChat(event) {
		if (chatResize.value?.pointerId !== event.pointerId) return;
		draggedChatHeight.value = clampChatHeight(chatResize.value.startHeight + chatResize.value.startY - event.clientY);
	}

	function finishChatResize(event) {
		if (chatResize.value?.pointerId !== event.pointerId) return;
		if (draggedChatHeight.value !== null) savedChatHeight.value = clampChatHeight(draggedChatHeight.value);
		chatResize.value = null;
		draggedChatHeight.value = null;
		if (event.currentTarget.hasPointerCapture(event.pointerId)) event.currentTarget.releasePointerCapture(event.pointerId);
	}

	function resizeChatWithKeyboard(event) {
		const height = chatPanelRef.value.getBoundingClientRect().height;
		const heights = { ArrowUp: height + 20, ArrowDown: height - 20, Home: MIN_CHAT_HEIGHT, End: maxChatHeight.value };
		if (!(event.key in heights)) return;
		event.preventDefault();
		savedChatHeight.value = clampChatHeight(heights[event.key]);
	}

	onClickOutside(smilePickerRef, () => {
		smilesOpen.value = false;
	});

	function handleEnter(event) {
		if (event.isComposing) event.preventDefault();
	}

	async function addMessage() {
		if (sending.value || !chatMessage.value.trim()) return;
		sending.value = true;
		sendError.value = '';
		try {
			await chat.sendMessage(chatMessage.value.trim());
			chatMessage.value = '';
			smilesOpen.value = false;
		} catch {
			sendError.value = 'Сообщение не отправлено. Попробуйте ещё раз.';
		} finally {
			sending.value = false;
			await nextTick();
			textRef.value?.focus();
		}
	}

	async function insertText(text) {
		if (sending.value) return;
		chatMessage.value = (chatMessage.value + text).slice(0, 180);
		chatCollapsed.value = false;
		showPlayers.value = false;
		await nextTick();
		textRef.value?.focus();
	}

	function insertSmile(smile) {
		insertText(':' + smile + ': ');
		smilesOpen.value = false;
	}

	function addressPlayer(name) {
		insertText('для [' + name + '] ');
	}

	function addressPrivate(name) {
		insertText('приватно [' + name + '] ');
	}

	if (user.value) {
		echo?.channel('chat').listen('ChatPublicMessage', ({ message }) => {
			chat.addMessage(message);
		});

		echo?.private('user.' + user.value.id).listen('ChatPrivateMessage', ({ message }) => {
			chat.addMessage(message);

			if (message.redirect) {
				router.visit(message.redirect);
			}
		});
	}

	var newbieCount = 0;
	var newbieMessages = [];
	newbieMessages[0] = 'Здравствуйте, вы попали в славный мир Another World. Я помогу вам освоиться здесь.';
	newbieMessages[1] =
		'Прежде всего вы должны распределить свободные параметры, такие как: сила, удача, ловкость и выносливость. Разум на нулевом уровне качать нет смысла. Чтобы увеличить параметры надо нажать на "Есть свободные статы!", и в появившемся окне, сделать выбор статов.';
	newbieMessages[2] =
		'Не спешите выбирать между энергией и выносливостью, т.к. этот выбор определит вашу будущую раскачку под мага или воина соответственно. Выбор можно сделать в любой момент.';
	newbieMessages[3] =
		'Теперь следует приобрести тренировочный нож за 2 золота, он значительно увеличит наносимый вами урон. Чтобы попасть в любое здание вначале надо нажать  кнопку "Город", расположенную в верхнем фрэйме, затем выбрать здание, в нашем случае "Магазин", он находится на "Торговой площади".';
	newbieMessages[4] =
		'Возращаемся на Арену и начинаем свой путь к первому уровню! Опыт игроки набирают в поединках, на нулевом уровне доступны физические поединки (1х1) и бои с вашим клоном в тренировочной комнате. В бою вы можете сделать 1 удар и поставить 1 блок (без щита).';
	newbieMessages[5] = 'Удачи вам, на этом не лёгком пути к славе и победам.';

	let newbieTimer;
	onMounted(() => {
		if (user.value.exp === 0) newbieTimer = setTimeout(newbieSend, 30000);
	});
	onBeforeUnmount(() => {
		clearTimeout(newbieTimer);
		echo?.disconnect();
	});

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

			newbieTimer = setTimeout(newbieSend, 45000);

			newbieCount++;
		}
	}
</script>
