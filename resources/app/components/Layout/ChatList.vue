<template>
	<div class="shoutbox scrollbox" ref="chatboxRef" id="shoutbox">
		<div v-for="message in messages" :key="message.id">
			<div class="chat-messages text-left">
				<span :class="{date1: !message['me'] && !message['my'], date2: !!message['me'], date3: !!message['my']}" @click="emit('private', message['user'])">{{ $formatDate(message['date'], 'HH:mm') }}</span>
				<span v-if="message['my']" class="negative">{{ message['user'] }}</span><span v-else class="to" @click="emit('player', message['user'])">{{ message['user'] }}</span>:
				<span v-if="message['tou'].length" :class="[message['private'] ? 'private' : 'player']">
					{{ message['private'] ? 'приватно' : 'для' }} [<span v-for="(u, i) in message['tou']">{{ i > 0 ? ',' : '' }}<a v-if="!message['private']" @click.prevent="emit('player', u)">{{ u }}</a><a v-else @click.prevent="emit('private', u)">{{ u }}</a></span>]
				</span>
				<span class="chat-messages-text" v-html="reformatMessage(message['text'])"></span>
			</div>
		</div>
	</div>
</template>

<script setup>
import { inject, onBeforeUnmount, onMounted, useTemplateRef, watch } from 'vue';
	import { reformatMessage } from '~/composables/useChat.js';

	const chatStore = inject('chat');
	const chatboxRef = useTemplateRef('chatboxRef');
	const { messages } = chatStore;

	onMounted(() => {
		chatStore.loadMessages();

		window.addEventListener('resize', scrollToBottom, true);
		setTimeout(scrollToBottom, 250);
	});

	onBeforeUnmount(() => {
		window.removeEventListener('resize', scrollToBottom);
	});

	watch(() => messages.value.length, () => {
		setTimeout(scrollToBottom, 250);

		chatStore.clearUnread();
	});

	function scrollToBottom () {
		if (chatboxRef.value) {
			chatboxRef.value.scrollTop = chatboxRef.value.scrollHeight;
		}
	}
</script>