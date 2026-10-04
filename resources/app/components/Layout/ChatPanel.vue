<template>
	<section
		ref="chatPanelRef"
		class="game-social"
		:style="chatPanelStyle"
		:class="{
			'is-collapsed': chatCollapsed,
			'show-players': showPlayers,
			'is-resizing-chat': chatResize !== null,
		}"
	>
		<div
			v-if="!chatCollapsed"
			class="chat-resizer"
			role="separator"
			@pointerdown="startChatResize"
			@pointermove="resizeChat"
			@pointerup="finishChatResize"
			@pointercancel="finishChatResize"
			@lostpointercapture="finishChatResize"
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
				<ChatList :active="chatVisible" @player="addressPlayer" @private="addressPrivate" />
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
</template>

<script setup>
	import { Link } from '@inertiajs/vue3';
	import { computed, inject, nextTick, ref, useTemplateRef } from 'vue';
	import { onClickOutside, useLocalStorage, useMediaQuery, useResizeObserver } from '@vueuse/core';
	import useState from '~/composables/useState.js';
	import { smiles } from '~/composables/useChat.js';
	import ChatList from './ChatList.vue';
	import GameIcon from './GameIcon.vue';
	import Online from './Online.vue';

	const props = defineProps({
		shellElement: Object,
		stageElement: Object,
	});

	const state = useState();
	const user = computed(() => state.user);
	const chat = inject('chat');

	const chatMessage = ref('');
	const textRef = useTemplateRef('textRef');
	const onlineRef = useTemplateRef('onlineRef');

	const chatCollapsed = useLocalStorage('game.chat.collapsed', false, { initOnMounted: true });
	const showPlayers = ref(false);
	const compactChat = useMediaQuery('(max-width: 600px)');
	const chatVisible = computed(() => !chatCollapsed.value && !(compactChat.value && showPlayers.value));
	const smilesOpen = ref(false);
	const sending = ref(false);
	const sendError = ref('');
	const smilePickerRef = useTemplateRef('smilePickerRef');

	const MIN_CHAT_HEIGHT = 160;
	const shellRef = computed(() => props.shellElement);
	const stageRef = computed(() => props.stageElement);
	const chatPanelRef = useTemplateRef('chatPanelRef');
	const savedChatHeight = useLocalStorage('game.chat.height', 0, { initOnMounted: true });
	const maxChatHeight = ref(600);
	const chatResize = ref(null);
	const draggedChatHeight = ref(null);

	const chatPanelStyle = computed(() => {
		if (chatCollapsed.value) {
			return {};
		}

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

		if (!shell || !stage || !panel) {
			return;
		}

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
		if (event.button !== 0 || !event.isPrimary || chatResize.value) {
			return;
		}

		event.preventDefault();
		event.currentTarget.setPointerCapture(event.pointerId);

		chatResize.value = {
			pointerId: event.pointerId,
			startY: event.clientY,
			startHeight: chatPanelRef.value.getBoundingClientRect().height,
		};
	}

	function resizeChat(event) {
		if (chatResize.value?.pointerId !== event.pointerId) {
			return;
		}

		draggedChatHeight.value = clampChatHeight(chatResize.value.startHeight + chatResize.value.startY - event.clientY);
	}

	function finishChatResize(event) {
		if (chatResize.value?.pointerId !== event.pointerId) {
			return;
		}

		if (draggedChatHeight.value !== null) {
			savedChatHeight.value = clampChatHeight(draggedChatHeight.value);
		}

		chatResize.value = null;
		draggedChatHeight.value = null;

		if (event.currentTarget.hasPointerCapture(event.pointerId)) {
			event.currentTarget.releasePointerCapture(event.pointerId);
		}
	}

	onClickOutside(smilePickerRef, () => {
		smilesOpen.value = false;
	});

	function handleEnter(event) {
		if (event.isComposing) {
			event.preventDefault();
		}
	}

	async function addMessage() {
		if (sending.value || !chatMessage.value.trim()) {
			return;
		}

		sending.value = true;
		sendError.value = '';

		try {
			await chat.sendMessage(chatMessage.value.trim());

			chatMessage.value = '';
			smilesOpen.value = false;
		} catch (error) {
			sendError.value = error.message || 'Сообщение не отправлено. Попробуйте ещё раз.';
		} finally {
			sending.value = false;

			await nextTick();

			textRef.value?.focus();
		}
	}

	async function insertText(text) {
		if (sending.value) {
			return;
		}

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
</script>
