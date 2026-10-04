<template>
	<Head title="Установка образа" />
	<ContentBlock title="Бесплатные образы">
		<template #actions>
			<Link href="/person" class="ui-icon-button" title="К персонажу">
				<GameIcon name="back" />
			</Link>
			<Link href="/person/avatar" class="ui-icon-button" title="Обновить">
				<GameIcon name="refresh" />
			</Link>
		</template>

		<div class="text-center text-sm font-bold mb-4">Внимание! Выбрав образ сейчас, Вы более не сможете его сменить!</div>

		<div v-if="form.errors.image" class="ui-notice ui-notice--red" role="alert">{{ form.errors.image }}</div>

		<div v-if="imageId >= 1 && imageId <= 49" class="text-center font-bold">У вас уже установлен образ. Сменить его вы сможете только в здании администрации.</div>
		<div v-else class="flex gap-4 justify-center">
			<div v-for="i in page.images">
				<a href="" @click.prevent="changeImage(i)">
					<img :src="'/assets/images/avatar/images/' + (user.gender === 'F' ? 2 : 1) + '/' + i + '.jpg'" alt="" />
				</a>
			</div>
		</div>
	</ContentBlock>
</template>

<script setup>
	import { Head, Link, useForm } from '@inertiajs/vue3';
	import useState from '~/composables/useState.js';
	import { computed } from 'vue';
	import { openConfirmModal } from '~/composables/useModals.js';
	import ContentBlock from '~/components/ContentBlock.vue';
	import GameIcon from '~/components/Layout/GameIcon.vue';

	defineProps({
		page: Object,
	});

	const state = useState();
	const user = computed(() => state.user);
	const imageId = computed(() => Number.parseInt(user.value.image?.split('/').pop() ?? '', 10));
	const form = useForm({
		image: null
	});

	function changeImage(i) {
		openConfirmModal('Подтвердите действие', 'Применить это образ?', [
			{ title: 'Нет' },
			{
				title: 'Да',
				handler() {
					form.image = i;
					form.post('/person/avatar');
				},
			},
		]);
	}
</script>
