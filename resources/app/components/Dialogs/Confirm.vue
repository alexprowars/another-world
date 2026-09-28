<template>
	<div class="confirm-box">
		<div class="dialog-text" v-html="content"></div>
		<div class="dialog-buttons">
			<button
				v-for="(button, index) in buttons"
				:key="index"
				type="button"
				class="ui-button ui-button--compact dialog-button"
				:class="[button.class, { 'ui-button--secondary': buttons.length > 1 && typeof button.handler !== 'function' }]"
				@click.stop="handle(button.handler)"
				v-html="button.title"
			></button>
		</div>
	</div>
</template>

<script setup>
	defineProps({
		content: { type: String, default: '' },
		buttons: { type: Array, default: () => [{ title: 'Понятно' }] },
	});

	const emit = defineEmits(['close']);

	function handle(action) {
		if (typeof action === 'function') {
			action();
		}

		emit('close');
	}
</script>
