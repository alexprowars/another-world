<template>
	<Head title="Церковь" />
	<ContentBlock title="Церковь">
		<template #actions>
			<MovementLink :to="location.exit" class="ui-icon-button" title="Вернуться на Королевскую улицу">
				<GameIcon name="back" />
			</MovementLink>
			<Link :href="location.url" class="ui-icon-button" title="Обновить">
				<GameIcon name="refresh" />
			</Link>
		</template>

		<header class="church-intro">
			<div class="church-emblem"><GameIcon name="church" /></div>
			<div>
				<span class="church-kicker">Храм на Королевской улице</span>
				<h2>Под сенью храма</h2>
				<p>Здесь два сердца соединяются, чтобы вместе пройти дорогами Другого Мира. Колокольный звон возвещает о новом союзе, а золотые кольца хранят память о клятвах.</p>
			</div>
		</header>

		<nav class="ui-tabs ui-tabs--stacked">
			<Link :href="location.url" class="ui-tab is-active">
				<GameIcon name="rings" />
				Заключение брака и развод
			</Link>
		</nav>

		<div class="church-content">
			<div class="church-status ui-panel">
				<GameIcon name="rings" />
				<div>
					<h3>Ваш семейный статус</h3>
					<p v-if="page.spouse">
						В браке с <a :href="'/info/' + page.spouse.id" target="_blank" rel="noopener">{{ page.spouse.name }}</a>
						<span class="church-status-date">с {{ page.married_at }}</span>
					</p>
					<p v-else>Вы пока не состоите в браке.</p>
				</div>
			</div>

			<p v-if="!page.can_officiate" class="ui-notice ui-notice--blue">
				Для заключения брака или развода обратитесь к администрации. Обряды проводит администратор в церкви.
			</p>

			<div class="service-columns">
				<section class="ui-panel">
					<header class="service-panel-heading">
						<GameIcon name="rings" />
						<h2>Заключение брака</h2>
					</header>
					<div class="service-panel-body">
						<div class="church-price">
							<img src="/assets/images/items/3/weddingring.png" width="40" height="40" alt="Обручальное кольцо" />
							<div><strong>{{ page.marriage_price }} пл.</strong><span>Плату вносит жених</span></div>
						</div>
						<p class="service-hint">Жених и невеста должны быть свободны от других браков. После обряда каждый получает обручальное кольцо в рюкзак.</p>
						<form v-if="page.can_officiate" class="service-form" @submit.prevent="marry">
							<label class="service-field">
								Имя жениха
								<input v-model="marriageForm.husband" class="ui-input" type="text" maxlength="100" autocomplete="off" required />
							</label>
							<label class="service-field">
								Имя невесты
								<input v-model="marriageForm.wife" class="ui-input" type="text" maxlength="100" autocomplete="off" required />
							</label>
							<p v-for="(error, field) in marriageForm.errors" :key="field" class="service-error" role="alert">{{ error }}</p>
							<button class="ui-button" type="submit" :disabled="busy">
								<GameIcon name="rings" />
								{{ marriageForm.processing ? 'Проведение обряда…' : 'Заключить брак' }}
							</button>
						</form>
					</div>
				</section>

				<section class="ui-panel">
					<header class="service-panel-heading">
						<GameIcon name="quill" />
						<h2>Расторжение брака</h2>
					</header>
					<div class="service-panel-body">
						<div class="church-price">
							<GameIcon name="coins" />
							<div><strong>{{ page.divorce_price }} зол.</strong><span>Плату вносит заявитель</span></div>
						</div>
						<p class="service-hint">У заявителя должна быть действующая проверка инквизиторов. После развода обручальные кольца обоих супругов изымаются, в том числе из экипировки и хранилища.</p>
						<form v-if="page.can_officiate" class="service-form" @submit.prevent="divorce">
							<label class="service-field">
								Имя одного из супругов
								<input v-model="divorceForm.name" class="ui-input" type="text" maxlength="100" autocomplete="off" required />
							</label>
							<p v-for="(error, field) in divorceForm.errors" :key="field" class="service-error" role="alert">{{ error }}</p>
							<button class="ui-button ui-button--secondary" type="submit" :disabled="busy">
								<GameIcon name="quill" />
								{{ divorceForm.processing ? 'Расторжение брака…' : 'Расторгнуть брак' }}
							</button>
						</form>
					</div>
				</section>
			</div>

			<section class="service-section">
				<header class="service-heading">
					<h2>Последние браки</h2>
					<p>Последние 20 союзов, заключённых в церкви.</p>
				</header>
				<div v-if="page.history.length" class="ui-table-wrap">
					<table class="ui-table service-table">
						<thead>
							<tr><th>Муж</th><th>Жена</th><th>Священник</th><th>Дата</th><th>Состояние</th></tr>
						</thead>
						<tbody>
							<tr v-for="entry in page.history" :key="entry.id">
								<td><a :href="'/info/' + entry.husband.id" target="_blank" rel="noopener">{{ entry.husband.name }}</a></td>
								<td><a :href="'/info/' + entry.wife.id" target="_blank" rel="noopener">{{ entry.wife.name }}</a></td>
								<td><a :href="'/info/' + entry.priest.id" target="_blank" rel="noopener">{{ entry.priest.name }}</a></td>
								<td class="service-number">{{ entry.date }}</td>
								<td><span class="ui-badge">{{ entry.divorced ? 'Расторгнут' : 'В браке' }}</span></td>
							</tr>
						</tbody>
					</table>
				</div>
				<div v-else class="ui-empty" role="status">
					<GameIcon name="rings" />
					<h3>Первая история любви ещё впереди</h3>
					<p>Здесь появятся имена молодожёнов и дата их свадьбы.</p>
				</div>
			</section>
		</div>
	</ContentBlock>
</template>

<script setup>
	import MovementLink from '~/components/City/MovementLink.vue';
	import useLocation from '~/composables/useLocation.js';

	import { computed } from 'vue';
	import { Head, Link, useForm } from '@inertiajs/vue3';
	import ContentBlock from '~/components/ContentBlock.vue';
	import GameIcon from '~/components/Layout/GameIcon.vue';

	const location = useLocation();

	defineProps({
		page: Object,
	});

	const marriageForm = useForm({
		husband: '',
		wife: '',
	});

	const divorceForm = useForm({
		name: '',
	});

	const busy = computed(() => marriageForm.processing || divorceForm.processing);

	function marry() {
		if (busy.value) {
			return;
		}

		marriageForm.post(location.value.actions.marry, {
			preserveScroll: true,
			errorBag: 'marriage',
		});
	}

	function divorce() {
		if (busy.value) {
			return;
		}

		divorceForm.post(location.value.actions.divorce, {
			preserveScroll: true,
			errorBag: 'divorce',
		});
	}
</script>
