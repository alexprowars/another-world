<template>
	<div ref="shellRef" class="game-shell">
		<a class="game-skip-link" href="#game-content">Перейти к игре</a>
		<Header />
		<main ref="stageRef" id="game-content" class="game-stage" tabindex="-1">
			<div class="game-stage-content">
				<slot />
			</div>
		</main>
		<ChatPanel :shell-element="shellRef" :stage-element="stageRef" />
	</div>
	<ModalTarget group="default" class="game-modal-target">
		<ModalOverlay class="dialog-overlay" />
	</ModalTarget>
</template>

<script setup>
	import { router } from '@inertiajs/vue3';
	import { computed, onBeforeUnmount, provide, useTemplateRef } from 'vue';
	import useState from '~/composables/useState.js';
	import useEcho from '~/composables/useEcho.js';
	import useChat from '~/composables/useChat.js';
	import useNewbieMessages from '~/composables/useNewbieMessages.js';
	import { setLocale } from '~/i18n.js';
	import dayjs from 'dayjs';
	import Header from '~/components/Layout/Header.vue';
	import ChatPanel from '~/components/Layout/ChatPanel.vue';
	import { ModalOverlay, ModalTarget } from '@kolirt/vue-modal';

	const state = useState();
	const user = computed(() => state.user);
	const shellRef = useTemplateRef('shellRef');
	const stageRef = useTemplateRef('stageRef');

	const chat = useChat(user);
	useNewbieMessages(user, chat);
	const echo = useEcho();
	provide('echo', echo);
	provide('chat', chat);

	setLocale(state.locale);
	dayjs.locale(state.locale);

	const redirectedMessageIds = new Set();
	const subscribedChatChannels = new Set();

	const chatConnection = echo?.connector.pusher.connection;

	function resetChatSubscriptions({ current }) {
		if (current !== 'connected') {
			subscribedChatChannels.clear();
		}

		if (current === 'unavailable' || current === 'failed') {
			chat.loadMessages();
		}
	}

	function chatChannelSubscribed(channel) {
		if (subscribedChatChannels.has(channel)) {
			return;
		}

		subscribedChatChannels.add(channel);

		if (subscribedChatChannels.size === 2) {
			chat.syncMessages();
		}
	}

	chatConnection?.bind('state_change', resetChatSubscriptions);

	if (user.value) {
		echo?.private('chat')
			.listen('ChatPublicMessage', ({ message }) => chat.addMessage(message))
			.subscribed(() => chatChannelSubscribed('chat'))
			.error(() => chat.loadMessages());

		echo?.private('user.' + user.value.id)
			.listen('ChatPrivateMessage', ({ message }) => {
				chat.addMessage(message);

				if (message.redirect && !redirectedMessageIds.has(message.id)) {
					redirectedMessageIds.add(message.id);

					router.visit(message.redirect);
				}
			})
			.subscribed(() => chatChannelSubscribed('user'))
			.error(() => chat.loadMessages());
	}

	onBeforeUnmount(() => {
		chatConnection?.unbind('state_change', resetChatSubscriptions);
		echo?.disconnect();
	});
</script>
