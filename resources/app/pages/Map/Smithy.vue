<template>
	<ContentBlock title="Кузница">
		<div class="w-full text-right">
			<Link :href="'/map?section=' + page.section"><img src="/assets/images/images/refresh.gif" alt="Обновить"></Link>
			<Link v-if="!page.busy" href="/map/change/11"><img src="/assets/images/images/back.gif" alt="Вернуться"></Link>
		</div>
		<p v-if="page.message" class="message bg-red-100 text-red-700 mb-4" v-html="page.message"></p>
		<p v-for="(error, key) in form.errors" :key="key" class="text-red-700 mb-2">{{ error }}</p>
		<p class="mb-4">У вас: <b>{{ user.gold }} зол.</b> и <b>{{ user.credits }} пл.</b></p>

		<div class="flex flex-wrap gap-4 border-b mb-4 pb-2">
			<Link href="/map?section=1" :class="{ 'font-bold': page.section === 1 }">Починка вещей</Link>
			<Link v-if="user.profession === 3" href="/map?section=2" :class="{ 'font-bold': page.section === 2 }">Огранка камней</Link>
			<Link href="/map?section=3" :class="{ 'font-bold': page.section === 3 }">Гравировка и модернизация</Link>
			<Link v-if="user.profession === 2" href="/map?section=4" :class="{ 'font-bold': page.section === 4 }">Вставка камней</Link>
		</div>

		<div v-if="page.busy" class="text-center my-4">
			<p>Вы заняты работой.</p>
			<template v-if="page.until">
				<p>Оставшееся время:</p>
				<Timer :value="page.until" :callback="finishWork" class="font-bold"/>
			</template>
		</div>
		<p v-else-if="page.notice">{{ page.notice }}</p>
		<template v-else>
			<p v-if="page.section === 2 || page.section === 4" class="mb-4">
				Работа занимает {{ page.work_seconds / 60 }} минут и расходует {{ page.work_stamina }} единиц сил.
				Нужен надетый инструмент своей профессии; его износ увеличится на 1.
			</p>
			<p v-if="page.section === 3" class="mb-4">
				Гравировка стоит {{ page.engraving_price }} зол. Текст — до 25 символов.
				Кузнец может увеличить минимальный и максимальный урон на 1 за {{ page.upgrade_price }} пл., уменьшив максимальную долговечность на 20.
			</p>
			<p v-if="page.section === 4 && !page.targets.length" class="mb-4">Нет снятых предметов, подходящих для вставки камня.</p>

			<div v-if="page.items.length" class="shop-items grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4">
				<SellItem v-for="entry in page.items" :key="entry.item.id" :item="entry.item">
					<template #actions>
						<div v-if="page.section === 1" class="space-y-2 mt-2">
							<button type="button" class="button" :disabled="form.processing" @click="confirmRepair(entry, true)">Починить за {{ entry.repair_price }} зол.</button>
							<button type="button" class="button" :disabled="form.processing" @click="confirmRepair(entry, false)">Починить 1 ед. за {{ entry.repair_one_price }} зол.</button>
						</div>
						<button v-else-if="page.section === 2" type="button" class="button mt-2" :disabled="form.processing" @click="confirm('Огранить камень? Работа займёт 10 минут.', 'cut', entry.item.id)">Огранить</button>
						<div v-else-if="page.section === 3" class="space-y-3 mt-2">
							<form v-if="!entry.item.engraving" @submit.prevent="engrave(entry)">
								<label class="text-xs">Текст гравировки
									<input v-model="texts[entry.item.id]" type="text" required maxlength="25" class="w-full">
								</label>
								<button type="submit" class="button mt-2" :disabled="form.processing">Гравировать за {{ page.engraving_price }} зол.</button>
							</form>
							<button v-if="user.profession === 2 && entry.item.wearout_max > 20" type="button" class="button" :disabled="form.processing" @click="confirmUpgrade(entry)">Модернизировать за {{ page.upgrade_price }} пл.</button>
						</div>
						<form v-else-if="page.section === 4 && page.targets.length" class="mt-2" @submit.prevent="insert(entry)">
							<label class="text-xs">Вставить в предмет
								<select v-model="targets[entry.item.id]" required class="w-full">
									<option disabled value="">Выберите предмет</option>
									<option v-for="target in page.targets" :key="target.id" :value="target.id">{{ target.title }}</option>
								</select>
							</label>
							<button type="submit" class="button mt-2" :disabled="form.processing">Вставить камень</button>
						</form>
					</template>
				</SellItem>
			</div>
			<p v-else>Нет предметов, подходящих для этой операции.</p>
		</template>
	</ContentBlock>
</template>

<script setup>
	import { computed, reactive } from 'vue';
	import { Link, router, useForm } from '@inertiajs/vue3';
	import ContentBlock from '~/components/ContentBlock.vue';
	import SellItem from '~/components/City/Shop/SellItem.vue';
	import Timer from '~/components/Timer.vue';
	import useState from '~/composables/useState.js';
	import { openConfirmModal } from '~/composables/useModals.js';

	const props = defineProps({ page: Object });
	const state = useState();
	const user = computed(() => state.user);
	const texts = reactive({});
	const targets = reactive({});
	const form = useForm({ action: '', id: null, full: true, text: null, target: null });

	function confirm(message, action, id, data = {}) {
		openConfirmModal('Подтвердите действие', message, [
			{ title: 'Нет' },
			{ title: 'Да', handler() {
				if (form.processing) return;
				form.reset();
				form.clearErrors();
				Object.assign(form, { action, id }, data);
				form.post('/map?section=' + props.page.section, { preserveScroll: true });
			} },
		]);
	}

	function confirmRepair(entry, full) {
		confirm('Починить предмет за ' + (full ? entry.repair_price : entry.repair_one_price) + ' зол.?', 'repair', entry.item.id, { full });
	}

	function engrave(entry) {
		confirm('Выгравировать надпись за ' + props.page.engraving_price + ' зол.? Гравировку нельзя заменить.', 'engrave', entry.item.id, { text: texts[entry.item.id] });
	}

	function confirmUpgrade(entry) {
		confirm('Увеличить минимальный и максимальный урон на 1 за ' + props.page.upgrade_price + ' пл.? Максимальная долговечность уменьшится на 20.', 'upgrade', entry.item.id);
	}

	function insert(entry) {
		confirm('Вставить камень в выбранный предмет? Камень будет израсходован. Работа займёт 10 минут.', 'insert', entry.item.id, { target: targets[entry.item.id] });
	}

	function finishWork() {
		router.reload();
	}
</script>