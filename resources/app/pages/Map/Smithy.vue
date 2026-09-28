<template>
	<ContentBlock title="Кузница" class="smithy">
		<template #actions>
			<button v-if="page.busy" type="button" class="ui-icon-button" disabled title="Возвращение доступно после окончания работы">
				<GameIcon name="back" />
			</button>
			<Link v-else href="/map/change/11" class="ui-icon-button" title="Назад">
				<GameIcon name="back" />
			</Link>
			<Link :href="'/map?section=' + page.section" class="ui-icon-button" title="Обновить">
				<GameIcon name="refresh" />
			</Link>
		</template>

		<nav class="ui-tabs ui-tabs--stacked">
			<Link
				class="ui-tab"
				v-for="section in availableSections"
				:key="section.id"
				:href="'/map?section=' + section.id"
				:class="{ 'is-active': page.section === section.id }"
			>
				<GameIcon :name="section.icon" />
				{{ section.title }}
			</Link>
		</nav>

		<p v-if="page.message" class="ui-notice ui-notice--blue" role="status" v-html="page.message"></p>
		<div v-if="Object.keys(form.errors).length" class="ui-notice ui-notice--red" role="alert">
			<p v-for="(error, key) in form.errors" :key="key">{{ error }}</p>
		</div>

		<div v-if="page.busy" class="smithy-active" role="status">
			<div class="smithy-emblem"><GameIcon name="hourglass" /></div>
			<h2>Работа идёт</h2>
			<p>Вы заняты работой. Дождитесь её завершения, чтобы воспользоваться кузницей.</p>
			<div v-if="page.until" class="smithy-active-time">
				<span>До окончания работы</span>
				<Timer :key="page.until" :value="page.until" :callback="finishWork" class="smithy-countdown" />
			</div>
		</div>
		<div v-else-if="page.notice" class="ui-empty" role="status">
			<GameIcon :name="currentSection.icon" />
			<h3>Нужна другая профессия</h3>
			<p>{{ page.notice }}</p>
			<Link href="/map?section=1" class="ui-button">К починке вещей</Link>
		</div>
		<template v-else>
			<header class="storefront-catalog-header">
				<h2>
					{{ currentSection.title }}
					<span class="ui-badge">{{ page.items.length }}</span>
				</h2>
			</header>
			<p class="storefront-description">{{ currentSection.description }}</p>

			<div v-if="page.section === 2 || page.section === 4" class="smithy-conditions">
				<div class="smithy-condition">
					<GameIcon name="hourglass" />
					<div>
						<span>Время работы</span>
						<strong>{{ page.work_seconds / 60 }} мин.</strong>
					</div>
				</div>
				<div class="smithy-condition">
					<GameIcon name="energy" />
					<div>
						<span>Затраты сил</span>
						<strong>{{ page.work_stamina }} ед.</strong>
					</div>
				</div>
				<div class="smithy-condition">
					<GameIcon name="tools" />
					<div>
						<span>Инструмент профессии</span>
						<strong>Должен быть надет · +1 к износу</strong>
					</div>
				</div>
			</div>
			<div v-if="page.section === 3" class="smithy-conditions">
				<div class="smithy-condition">
					<GameIcon name="book" />
					<div>
						<span>Гравировка · {{ page.engraving_price }} зол.</span>
						<strong>До 25 символов, без замены надписи</strong>
					</div>
				</div>
				<div class="smithy-condition">
					<GameIcon name="swords" />
					<div>
						<span>Модернизация · {{ page.upgrade_price }} пл. · только для кузнеца</span>
						<strong>+1 к мин. и макс. урону, −20 к макс. долговечности</strong>
					</div>
				</div>
			</div>
			<p v-if="page.section === 4 && !page.targets.length" class="ui-notice ui-notice--yellow">
				Нет снятых предметов, подходящих для вставки камня. Снимите снаряжение, в которое ещё не вставлен камень.
			</p>

			<div v-if="page.items.length" class="storefront-grid">
				<CatalogItem v-for="entry in page.items" :key="entry.item.id" :item="entry.item" :player="user" inventory-item>
					<template #actions>
						<div class="smithy-item-actions">
							<template v-if="page.section === 1">
								<button type="button" class="ui-button" :disabled="form.processing" @click="confirmRepair(entry, true)">
									Починить полностью · {{ entry.repair_price }} зол.
								</button>
								<button type="button" class="ui-button ui-button--secondary" :disabled="form.processing" @click="confirmRepair(entry, false)">
									Починить 1 ед. · {{ entry.repair_one_price }} зол.
								</button>
							</template>
							<button
								v-else-if="page.section === 2"
								type="button"
								class="ui-button"
								:disabled="form.processing"
								@click="confirm('Огранить камень? Работа займёт ' + page.work_seconds / 60 + ' минут.', 'cut', entry.item.id)"
							>
								<GameIcon name="gem" />
								Огранить камень
							</button>
							<template v-else-if="page.section === 3">
								<form v-if="!entry.item.engraving" class="smithy-form" @submit.prevent="engrave(entry)">
									<label class="smithy-field">
										<span>
											Текст гравировки
											<small>{{ texts[entry.item.id]?.length || 0 }} / 25</small>
										</span>
										<input
											class="ui-input"
											v-model="texts[entry.item.id]"
											type="text"
											required
											maxlength="25"
											placeholder="Ваша надпись"
											:disabled="form.processing"
										/>
									</label>
									<button type="submit" class="ui-button" :disabled="form.processing">Гравировать · {{ page.engraving_price }} зол.</button>
								</form>
								<button
									v-if="user.profession === 2 && entry.item.wearout_max > 20"
									type="button"
									class="ui-button ui-button--secondary"
									:disabled="form.processing"
									@click="confirmUpgrade(entry)"
								>
									Модернизировать · {{ page.upgrade_price }} пл.
								</button>
								<p v-if="entry.item.engraving && (user.profession !== 2 || entry.item.wearout_max <= 20)" class="smithy-item-hint">
									{{
										user.profession === 2
											? 'Недостаточно долговечности для модернизации.'
											: 'Гравировка уже нанесена. Модернизация доступна кузнецу.'
									}}
								</p>
							</template>
							<form v-else-if="page.section === 4 && page.targets.length" class="smithy-form" @submit.prevent="insert(entry)">
								<label class="smithy-field">
									<span>Вставить в предмет</span>
									<select class="ui-input" v-model="targets[entry.item.id]" required :disabled="form.processing">
										<option disabled :value="undefined">Выберите предмет</option>
										<option v-for="target in page.targets" :key="target.id" :value="target.id">{{ target.title }}</option>
									</select>
								</label>
								<button type="submit" class="ui-button" :disabled="form.processing">Вставить камень</button>
							</form>
							<p v-else-if="page.section === 4" class="smithy-item-hint">Нет подходящего снаряжения для вставки.</p>
						</div>
					</template>
				</CatalogItem>
			</div>
			<div v-else class="ui-empty" role="status">
				<GameIcon :name="currentSection.icon" />
				<h3>Нет подходящих предметов</h3>
				<p>{{ page.section === 1 ? 'В инвентаре нет вещей, требующих починки.' : 'Здесь появятся снятые предметы, подходящие для этой операции.' }}</p>
			</div>
		</template>

		<template #footer>
			<GameIcon :name="page.busy ? 'hourglass' : 'book'" />
			<span>
				{{
					page.busy
						? 'Вернуться в город можно после окончания работы.'
						: 'Наведите на изображение предмета или нажмите на него, чтобы посмотреть характеристики.'
				}}
			</span>
		</template>
	</ContentBlock>
</template>

<script setup>
	import { computed, reactive } from 'vue';
	import { Link, router, useForm } from '@inertiajs/vue3';
	import ContentBlock from '~/components/ContentBlock.vue';
	import GameIcon from '~/components/Layout/GameIcon.vue';
	import CatalogItem from '~/components/City/Shop/CatalogItem.vue';
	import Timer from '~/components/Timer.vue';
	import useState from '~/composables/useState.js';
	import { openConfirmModal } from '~/composables/useModals.js';

	const props = defineProps({ page: Object });
	const state = useState();
	const user = computed(() => state.user);
	const sections = [
		{ id: 1, title: 'Починка вещей', icon: 'tools', description: 'Восстановите изношенное снаряжение полностью или на одну единицу.' },
		{
			id: 2,
			title: 'Огранка камней',
			icon: 'gem',
			profession: 3,
			description: 'Ограните необработанные камни, чтобы использовать их для улучшения снаряжения.',
		},
		{
			id: 3,
			title: 'Гравировка и модернизация',
			icon: 'swords',
			description: 'Нанесите памятную надпись или увеличьте урон снаряжения. Предмет нужно снять.',
		},
		{
			id: 4,
			title: 'Вставка камней',
			icon: 'gem',
			profession: 2,
			description: 'Перенесите свойства огранённого камня на снаряжение. Камень будет израсходован.',
		},
	];
	const availableSections = computed(() => sections.filter(section => !section.profession || section.profession === user.value.profession));
	const currentSection = computed(() => sections.find(section => section.id === props.page.section));
	const texts = reactive({});
	const targets = reactive({});
	const form = useForm({ action: '', id: null, full: true, text: null, target: null });

	function confirm(message, action, id, data = {}) {
		openConfirmModal('Подтвердите действие', message, [
			{ title: 'Нет' },
			{
				title: 'Да',
				handler() {
					if (form.processing) return;
					form.reset();
					form.clearErrors();
					Object.assign(form, { action, id }, data);
					form.post('/map?section=' + props.page.section, { preserveScroll: true });
				},
			},
		]);
	}

	function confirmRepair(entry, full) {
		confirm('Починить предмет за ' + (full ? entry.repair_price : entry.repair_one_price) + ' зол.?', 'repair', entry.item.id, {
			full,
		});
	}

	function engrave(entry) {
		confirm('Выгравировать надпись за ' + props.page.engraving_price + ' зол.? Гравировку нельзя заменить.', 'engrave', entry.item.id, {
			text: texts[entry.item.id],
		});
	}

	function confirmUpgrade(entry) {
		confirm(
			'Увеличить минимальный и максимальный урон на 1 за ' + props.page.upgrade_price + ' пл.? Максимальная долговечность уменьшится на 20.',
			'upgrade',
			entry.item.id,
		);
	}

	function insert(entry) {
		confirm(
			'Вставить камень в выбранный предмет? Камень будет израсходован. Работа займёт ' + props.page.work_seconds / 60 + ' минут.',
			'insert',
			entry.item.id,
			{
				target: targets[entry.item.id],
			},
		);
	}

	function finishWork() {
		router.reload();
	}
</script>
