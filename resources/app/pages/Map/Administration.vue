<template>
	<Head title="Администрация" />
	<ContentBlock title="Администрация">
		<template #actions>
			<Link href="/map/change/14" class="ui-icon-button" title="Назад">
				<GameIcon name="back" />
			</Link>
			<Link :href="'/map?section=' + page.section" class="ui-icon-button" title="Обновить">
				<GameIcon name="refresh" />
			</Link>
		</template>

		<nav class="ui-tabs ui-tabs--stacked">
			<Link
				class="ui-tab"
				v-for="(title, index) in sections"
				:key="index"
				:href="'/map?section=' + (index + 1)"
				:class="{ 'is-active': page.section === index + 1 }"
			>
				<GameIcon :name="['clan', 'justice', 'character', 'book'][index]" />
				{{ title }}
			</Link>
		</nav>
		<div v-if="page.section === 1" class="service-section">
			<header class="service-heading">
				<h2>Регистратура кланов</h2>
				<p>Соберите союзников и ознакомьтесь с условиями регистрации своего клана.</p>
			</header>
			<div class="service-terms">
				<div>
					<span>Регистрационная пошлина</span>
					<strong>300 пл.</strong>
				</div>
				<div>
					<span>Уровень главы клана</span>
					<strong>{{ page.min_level }}</strong>
				</div>
				<div>
					<span>Допуск</span>
					<strong>Проверка инквизиторов</strong>
				</div>
			</div>
			<div class="service-columns">
				<section class="ui-panel service-panel">
					<header class="service-panel-heading">
						<GameIcon name="clan" />
						<h2>Правила регистрации</h2>
					</header>
					<div class="service-panel-body">
						<ul class="service-list">
							<li>Глава клана оплачивает пошлину, достигает {{ page.min_level }} уровня и проходит проверку у инквизиторов.</li>
							<li>Подготовьте значок клана: 24 × 14 пикселей, прозрачный GIF.</li>
							<li>Предоставьте историю клана для информационного отдела.</li>
							<li>Склонность для клана покупается отдельно.</li>
						</ul>
					</div>
				</section>
				<section class="ui-panel service-panel">
					<header class="service-panel-heading">
						<GameIcon name="work" />
						<h2>Порядок регистрации</h2>
					</header>
					<div class="service-panel-body">
						<ol class="service-list">
							<li>Соберите игроков с общими интересами и выберите главу клана.</li>
							<li>Подайте заявку в отдел регистрации кланов.</li>
							<li>После одобрения заявки глава проходит проверку у инквизиторов.</li>
							<li>Оплатите регистрационную пошлину.</li>
							<li>После проверки клан открывается и вносится в государственный реестр.</li>
						</ol>
					</div>
				</section>
			</div>
		</div>
		<div v-else-if="page.section === 2" class="service-split">
			<section class="ui-panel service-panel">
				<header class="service-panel-heading">
					<GameIcon name="justice" />
					<h2>Проверка инквизиторов</h2>
				</header>
				<div class="service-panel-body">
					<p class="service-hint">Обычно рассмотрение занимает около 24 часов. Если вы будете в игре, сообщение о результате придёт в чат.</p>
					<dl class="service-facts">
						<div>
							<dt>Стоимость заявки</dt>
							<dd>{{ page.request_price }} пл.</dd>
						</div>
						<div>
							<dt>Минимальный уровень</dt>
							<dd>{{ page.min_level }}</dd>
						</div>
					</dl>
					<p v-if="page.has_request" class="service-status">
						<GameIcon name="hourglass" />
						Заявка подана
					</p>
					<form class="service-form" @submit.prevent="sendRequest">
						<button type="submit" class="ui-button" :class="{ 'ui-button--secondary': page.has_request }" :disabled="requestForm.processing">
							{{ page.has_request ? 'Отозвать заявку' : 'Подать заявку' }}
						</button>
						<p v-if="page.has_request" class="service-hint">При отзыве заявки плата не возвращается.</p>
						<p v-for="(error, field) in requestForm.errors" :key="field" class="service-error" role="alert">{{ error }}</p>
					</form>
				</div>
			</section>
			<section class="service-section">
				<header class="service-heading">
					<h2>Последние заявки</h2>
					<p>Последние 15 обращений игроков.</p>
				</header>
				<div v-if="page.requests.length" class="ui-table-wrap service-table-wrap">
					<table class="ui-table service-table">
						<thead>
							<tr>
								<th>№</th>
								<th>Игрок</th>
								<th>Состояние</th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="entry in page.requests" :key="entry.id">
								<td>{{ entry.id }}</td>
								<td>{{ entry.user }}</td>
								<td>
									<span class="ui-badge">{{ statuses[entry.status] ?? 'Неизвестно' }}</span>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div v-else class="ui-empty" role="status">
					<GameIcon name="work" />
					<h3>Заявок пока нет</h3>
					<p>Здесь появятся обращения на проверку.</p>
				</div>
			</section>
		</div>
		<div v-else-if="page.section === 3" class="service-section">
			<header class="service-heading">
				<h2>Выберите образ</h2>
				<p>Новый облик персонажа стоит {{ page.image_price }} пл.</p>
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
			<div v-if="page.images.length" class="administration-images">
				<article v-for="image in page.images" :key="image" class="administration-image" :class="{ 'is-selected': user.image === imagePath(image) }">
					<div class="administration-image-preview">
						<img :src="'/assets/images/avatar/' + imagePath(image)" :alt="'Образ №' + image" loading="lazy" />
					</div>
					<button type="button" class="ui-button" :disabled="imageForm.processing || user.image === imagePath(image)" @click="buyImage(image)">
						{{ user.image === imagePath(image) ? 'Установлен' : 'Купить · ' + page.image_price + ' пл.' }}
					</button>
				</article>
			</div>
			<div v-else class="ui-empty" role="status">
				<GameIcon name="character" />
				<h3>Нет доступных образов</h3>
				<p>Загляните в отдел позже.</p>
			</div>
		</div>
		<div v-else class="service-section">
			<header class="service-heading">
				<h2>Государственный архив кланов</h2>
				<p>Истории и уставы зарегистрированных объединений.</p>
			</header>
			<details v-for="tribe in page.tribes" :key="tribe.id" class="administration-tribe">
				<summary>
					<img :src="'/assets/images/tribe/' + tribe.id + '.gif'" :alt="tribe.short" />
					<span>{{ tribe.short }} — {{ tribe.name }}</span>
					<GameIcon name="chevron" />
				</summary>
				<div class="service-panel-body">
					<p class="service-prose">{{ tribe.about || 'Описание клана пока не добавлено.' }}</p>
					<template v-if="tribe.laws">
						<h3>Устав клана</h3>
						<p class="service-prose">{{ tribe.laws }}</p>
					</template>
				</div>
			</details>
			<div v-if="!page.tribes.length" class="ui-empty" role="status">
				<GameIcon name="clan" />
				<h3>Кланы пока не зарегистрированы</h3>
				<p>После регистрации сведения о кланах появятся в архиве.</p>
			</div>
		</div>
	</ContentBlock>
</template>

<script setup>
	import { computed } from 'vue';
	import { Head, Link, useForm } from '@inertiajs/vue3';
	import ContentBlock from '~/components/ContentBlock.vue';
	import GameIcon from '~/components/Layout/GameIcon.vue';
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
