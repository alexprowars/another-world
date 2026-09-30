<template>
	<div class="shoutbox scrollbox" ref="chatboxRef" id="shoutbox">
		<div v-if="!messages.length && !chatStore.loading.value && !chatStore.loadError.value" class="chat-empty">
			<span>Здесь начинается общение</span>
			<small>Поздоровайтесь с миром или выберите игрока для личного сообщения.</small>
		</div>
		<p v-if="chatStore.loadError.value" class="chat-load-error" role="alert">
			{{ chatStore.loadError.value }}
			<button type="button" class="ui-button ui-button--compact" @click="chatStore.syncMessages()">Повторить</button>
		</p>
		<div v-for="message in messages" :key="message.id">
			<div class="chat-messages text-left">
				<span
					:class="{ date1: !message.me && !message.my, date2: message.me, date3: message.my }"
					@click="message.sender && emit('private', message.sender.name)"
				>
					{{ $formatDate(message.created_at, 'HH:mm') }}
				</span>
				<span v-if="!message.sender">{{ message.system_name }}</span>
				<span v-else-if="message.my" class="negative">{{ message.sender.name }}</span>
				<span v-else class="to" @click="emit('player', message.sender.name)">{{ message.sender.name }}</span>:
				<span v-if="message.recipients.length" :class="message.visibility === 'private' ? 'private' : 'player'">
					{{ message.visibility === 'private' ? 'приватно' : 'для' }}
					[<span v-for="(recipient, index) in message.recipients" :key="recipient.id">{{ index > 0 ? ', ' : '' }}<a
						@click.prevent="emit(message.visibility === 'private' ? 'private' : 'player', recipient.name)"
					>{{ recipient.name }}</a></span>]
				</span>
				<span class="chat-messages-text" v-html="reformatMessage(message.body)"></span>
			</div>
		</div>
	</div>
</template>

<script setup>
	import { inject, nextTick, onBeforeUnmount, onMounted, useTemplateRef, watch } from 'vue';
	import { reformatMessage } from '~/composables/useChat.js';

	const props = defineProps({ active: Boolean });
	const emit = defineEmits(['player', 'private']);
	const chatStore = inject('chat');
	const chatboxRef = useTemplateRef('chatboxRef');
	const { messages } = chatStore;

	onMounted(() => {
		window.addEventListener('resize', scrollToBottom, true);
		nextTick(scrollToBottom);
	});

	onBeforeUnmount(() => {
		window.removeEventListener('resize', scrollToBottom, true);
	});

	watch(
		[() => messages.value, () => props.active],
		() => {
			if (!props.active) {
				return;
			}

			nextTick(scrollToBottom);

			chatStore.clearUnread();
		},
	);

	function scrollToBottom() {
		if (chatboxRef.value) {
			chatboxRef.value.scrollTop = chatboxRef.value.scrollHeight;
		}
	}
</script>
