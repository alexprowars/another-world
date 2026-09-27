<template>
	<Head title="Администрация"/>
	<ContentBlock title="Администрация">
		<div class="mb-4 flex justify-end gap-1">
			<Link :href="'/map?section=' + page.section"><img src="/assets/images/images/refresh.gif" alt="Обновить"></Link>
			<Link href="/map/change/14"><img src="/assets/images/images/back.gif" alt="Вернуться"></Link>
		</div>

		<p class="mb-4">У вас: <b>{{ user.credits }} пл.</b></p>
		<nav class="mb-4 flex flex-wrap gap-4 border-b pb-2">
			<Link v-for="(title, index) in sections" :key="index" :href="'/map?section=' + (index + 1)" :class="{ 'font-bold': page.section === index + 1 }">{{ title }}</Link>
		</nav>

		<div v-if="page.section === 1" class="space-y-4">
			<h3 class="text-center font-bold">Регистратура кланов</h3>
			<p>Добро пожаловать в отдел регистрации кланов! Для создания клана ознакомьтесь с правилами регистрации.</p>
			<h4 class="font-bold">Правила регистрации клана</h4>
			<ol class="list-decimal space-y-2 pl-6">
				<li>Для регистрации клана необходимо уплатить пошлину в размере <b>300 пл.</b></li>
				<li>Глава клана должен достигнуть <b>{{ page.min_level }} уровня</b> и пройти проверку у инквизиторов.</li>
				<li>Глава клана должен предоставить значок клана (24 × 14 пикселей, прозрачный GIF) и историю клана для информационного отдела.</li>
				<li>Склонность для клана покупается отдельно.</li>
			</ol>
			<h4 class="font-bold">Порядок регистрации клана</h4>
			<ol class="list-decimal space-y-2 pl-6">
				<li>Игроки с общими интересами собираются в группу.</li>
				<li>Выбирается лидер, который будет управлять кланом.</li>
				<li>Заявка рассматривается отделом регистрации кланов.</li>
				<li>При положительном результате глава клана проходит проверку у инквизиторов.</li>
				<li>Глава клана оплачивает пошлину за регистрацию.</li>
				<li>После успешного прохождения проверки клан открывается и вносится в государственный реестр.</li>
			</ol>
		</div>

		<div v-else-if="page.section === 2" class="space-y-4">
			<h3 class="font-bold">Заявка на проверку у инквизиторов</h3>
			<p>Здесь вы можете подать заявку на проверку. Обычно ожидание занимает около 24 часов. Если во время проверки вы будете в игре, вам придёт сообщение о результате.</p>
			<p>Стоимость подачи заявки: <b>{{ page.request_price }} пл.</b> Минимальный уровень: <b>{{ page.min_level }}</b>.</p>
			<form @submit.prevent="sendRequest">
				<button type="submit" class="button" :disabled="requestForm.processing">
					{{ page.has_request ? 'Убрать свою заявку' : 'Подать заявку на проверку' }}
				</button>
				<p v-if="page.has_request" class="mt-2 text-sm">При отзыве заявки плата не возвращается.</p>
				<p v-if="requestForm.errors.action" class="mt-2 text-red-700">{{ requestForm.errors.action }}</p>
			</form>

			<h4 class="font-bold">Последние 15 заявок</h4>
			<table v-if="page.requests.length" class="table w-full">
				<thead><tr><th>#</th><th>Игрок</th><th>Состояние</th></tr></thead>
				<tbody>
					<tr v-for="entry in page.requests" :key="entry.id">
						<td>{{ entry.id }}</td>
						<td>{{ entry.user }}</td>
						<td>{{ statuses[entry.status] ?? 'Неизвестно' }}</td>
					</tr>
				</tbody>
			</table>
			<p v-else>Заявок пока нет.</p>
		</div>

		<div v-else-if="page.section === 3" class="space-y-4">
			<p>Добро пожаловать в отдел выбора образа! Стоимость образа — <b>{{ page.image_price }} пл.</b></p>
			<p>Стоимость добавления собственного оригинального образа — <b>300 пл.</b> Обращайтесь к администрации.</p>
			<p v-if="imageForm.errors.image" class="text-red-700">{{ imageForm.errors.image }}</p>
			<div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
				<button v-for="image in page.images" :key="image" type="button" class="flex flex-col items-center gap-2 disabled:opacity-50" :disabled="imageForm.processing || user.image === imagePath(image)" @click="buyImage(image)">
					<img :src="'/assets/images/avatar/' + imagePath(image)" :alt="'Образ №' + image" loading="lazy">
					<span>{{ user.image === imagePath(image) ? 'Установлен' : 'Купить' }}</span>
				</button>
			</div>
		</div>

		<div v-else class="space-y-4">
			<p>Добро пожаловать в Государственный архив кланов. Здесь вы можете просмотреть записи о кланах и информацию о них.</p>
			<details v-for="(tribe, index) in page.tribes" :key="tribe.id" class="border-b pb-3">
				<summary class="cursor-pointer font-bold">
					{{ index + 1 }}.
					<img :src="'/assets/images/tribe/' + tribe.id + '.gif'" :alt="tribe.short" class="inline-block">
					{{ tribe.short }} — {{ tribe.name }}
				</summary>
				<p class="mt-3 whitespace-pre-line">{{ tribe.about || 'Описание клана пока не добавлено.' }}</p>
				<template v-if="tribe.laws">
					<h4 class="mt-3 font-bold">Устав клана</h4>
					<p class="whitespace-pre-line">{{ tribe.laws }}</p>
				</template>
			</details>
			<p v-if="!page.tribes.length">Зарегистрированных кланов пока нет.</p>
		</div>
	</ContentBlock>
</template>

<script setup>
	import { computed } from 'vue';
	import { Head, Link, useForm } from '@inertiajs/vue3';
	import ContentBlock from '~/components/ContentBlock.vue';
	import useState from '~/composables/useState.js';
	import { openConfirmModal } from '~/composables/useModals.js';

	const props = defineProps({ page: Object });
	const state = useState();
	const user = computed(() => state.user);
	const sections = ['Правила регистрации', 'Подать заявку на проверку', 'Образ', 'Архив кланов'];
	const statuses = ['На рассмотрении', 'Принято', 'Отклонено'];
	const requestForm = useForm({ action: '' });
	const imageForm = useForm({ action: 'buy_image', image: null });

	function sendRequest() {
		if (requestForm.processing) return;
		requestForm.action = props.page.has_request ? 'withdraw' : 'submit';
		requestForm.post('/map?section=2', { preserveScroll: true });
	}

	function imagePath(image) {
		return 'images/' + (user.value.gender === 'F' ? 2 : 1) + '/' + image + '.png';
	}

	function buyImage(image) {
		if (imageForm.processing) return;
		openConfirmModal('Подтвердите действие', 'Купить этот образ за ' + props.page.image_price + ' пл.?', [
			{ title: 'Нет' },
			{
				title: 'Да',
				handler() {
					if (imageForm.processing) return;
					imageForm.image = image;
					imageForm.post('/map?section=3', { preserveScroll: true });
				},
			},
		]);
	}
</script>
