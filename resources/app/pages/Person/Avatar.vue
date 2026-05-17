<template>
	<Head title="Установка образа"/>
	<ContentBlock title="Бесплатные образы">
		<div class="text-center text-sm font-bold mb-4">
			Внимание! Выбрав образ сейчас, Вы более не сможете его сменить!
		</div>

		<div v-if="user.image" class="text-center font-bold">
			У вас уже установлен образ. Сменить его вы сможете только в здании администрации.
		</div>
		<div v-else class="flex gap-4 justify-center">
			<div v-for="i in page.images">
				<a href="" @click.prevent="changeImage(i)">
					<img :src="'/assets/images/avatar/images/' + (user.gender === 'F' ? 2 : 1) + '/' + i + '.png'" alt="">
				</a>
			</div>
		</div>
	</ContentBlock>
</template>

<script setup>
	import CityHeader from '~/components/CityHeader.vue';
	import { Head, useForm } from '@inertiajs/vue3';
	import useState from '~/composables/useState.js';
	import { computed } from 'vue';
	import { openConfirmModal } from '~/composables/useModals.js';
	import ContentBlock from '~/components/ContentBlock.vue';

	defineProps({
		page: Object,
	});

	const state = useState();
	const user = computed(() => state.user);

	function changeImage(i) {
		openConfirmModal(
			'Подтвердите действие',
			'Применить это образ?',
			[{ title: 'Нет' }, {
				title: 'Да',
				handler() {
					useForm({
						image: i
					})
					.post('/person/avatar');
				},
			},
		]);
	}
</script>