<template>
	<div class="service-section">
		<header class="service-heading">
			<h2>Выберите образ</h2>
			<p>Новый облик персонажа стоит {{ imagePrice }} пл.</p>
		</header>
		<div class="ui-notice ui-notice--blue ui-notice--with-icon">
			<GameIcon name="character" />
			<p>
				Добавление собственного оригинального образа —
				<strong>300 пл.</strong>
				Обратитесь к администрации.
			</p>
		</div>
		<div v-if="Object.keys(imageForm.errors).length" class="ui-notice ui-notice--red" role="alert">
			<p v-for="(error, field) in imageForm.errors" :key="field">{{ error }}</p>
		</div>
		<div v-if="images.length" class="administration-images">
			<article v-for="image in images" :key="image" class="administration-image" :class="{ 'is-selected': user.image === imagePath(image) }">
				<div class="administration-image-preview">
					<img :src="'/assets/images/avatar/' + imagePath(image)" :alt="'Образ №' + image" loading="lazy" />
				</div>
				<button type="button" class="ui-button" :disabled="imageForm.processing || user.image === imagePath(image)" @click="buyImage(image)">
					{{ user.image === imagePath(image) ? 'Установлен' : 'Купить · ' + imagePrice + ' пл.' }}
				</button>
			</article>
		</div>
		<div v-else class="ui-empty" role="status">
			<GameIcon name="character" />
			<h3>Нет доступных образов</h3>
			<p>Загляните в отдел позже.</p>
		</div>
	</div>
</template>

<script setup>
	import useLocation from '~/composables/useLocation.js';

	import { computed } from 'vue';
	import { useForm } from '@inertiajs/vue3';
	import GameIcon from '~/components/Layout/GameIcon.vue';
	import useState from '~/composables/useState.js';
	import { openConfirmModal } from '~/composables/useModals.js';

	const location = useLocation();

	const props = defineProps({
		imagePrice: Number,
		images: Array,
	});

	const state = useState();
	const user = computed(() => state.user);

	const imageForm = useForm({
		image: null
	});

	function imagePath(image) {
		return 'images/' + (user.value.gender === 'F' ? 2 : 1) + '/' + image + '.jpg';
	}

	function buyImage(image) {
		if (imageForm.processing) {
			return;
		}

		openConfirmModal('Подтвердите действие', 'Купить этот образ за ' + props.imagePrice + ' пл.?', [
			{ title: 'Нет' },
			{
				title: 'Да',
				handler() {
					if (imageForm.processing) {
						return;
					}

					imageForm.image = image;
					imageForm.post(location.value.actions.buy_image, {
						preserveScroll: true
					});
				},
			},
		]);
	}
</script>
