<template>
	<ContentBlock title="Территория подземелья">
		<div class="mb-4 flex flex-wrap items-center justify-between gap-3">
			<div class="flex flex-wrap items-center gap-4">
				<Name :player="user"/>
				<HpLine :current="user.hp_now" :max="user.hp_max" color="g_line"/>
			</div>
			<div class="flex gap-1">
				<Link href="/map"><img src="/assets/images/images/refresh.gif" alt="Обновить"></Link>
				<Link v-if="page.vault.id === 200 && !busy" href="/map/change/200"><img src="/assets/images/images/back.gif" alt="Вернуться"></Link>
			</div>
		</div>

		<h2 class="mb-4 text-center font-bold underline">{{ page.vault.title }}</h2>

		<div class="grid items-start gap-4 md:grid-cols-[170px_1fr_170px]">
			<div class="rounded border border-slate-300 p-3 text-center">
				<template v-if="!busy">
					<p class="mb-3 border-b border-slate-300 pb-2 font-bold">Навигация</p>
					<div class="grid grid-cols-3 grid-rows-3 items-center justify-items-center gap-1">
						<button v-for="direction in directions" :key="direction.key" type="button"
							:class="direction.position" :disabled="!page.directions[direction.key] || form.processing"
							:title="page.directions[direction.key] ? 'Перейти в ' + page.directions[direction.key].title : 'Нет прохода'"
							@click="act({ go: direction.key })"
						>
							<img :src="'/assets/images/images/vault/navigation/' + (page.directions[direction.key] ? 'active/' : 'n_active/') + direction.key + '.gif'" :alt="direction.label">
						</button>
						<img src="/assets/images/images/vault/navigation/center.gif" class="col-start-2 row-start-2" alt="Текущая комната">
					</div>
				</template>
				<template v-else>
					<p v-if="user.r_type === 10">Топаем в <b>{{ page.destination }}</b></p>
					<p v-else-if="user.r_type === 8" class="font-bold">Добываем руду</p>
					<p v-else>Вы заняты работой</p>
					<div v-if="user.r_date" class="mt-3 border-t border-slate-300 pt-3">
						Ещё:
						<Timer :key="user.r_date" :value="user.r_date" :callback="onTimeout" class="font-bold"/>
					</div>
					<button v-if="user.r_type === 8" type="button" class="btn btn-primary mt-3" :disabled="form.processing" @click="act({ unwork: 'Y' })">Отменить добычу</button>
				</template>
			</div>

			<div class="text-center" v-html="page.vault.text"></div>

			<div v-if="!busy" class="rounded border border-slate-300 p-3 text-center">
				<p class="mb-3 border-b border-slate-300 pb-2 font-bold">Действия</p>
				<button type="button" class="btn btn-primary w-full" :disabled="!page.canHeal || form.processing" @click="act({ heal: 'Y' })">Колодец Жизни</button>
				<form class="mt-3 space-y-2 border-t border-slate-300 pt-3" @submit.prevent="dig">
					<img :src="page.captcha" width="140" height="48" alt="Код для добычи руды" class="mx-auto">
					<label for="vault-captcha" class="block">Код с картинки</label>
					<input id="vault-captcha" v-model="form.captcha" type="text" inputmode="numeric" autocomplete="off" maxlength="5" required class="w-full rounded border border-slate-300 px-2 py-1">
					<button type="submit" class="btn btn-primary w-full" :disabled="form.processing">Добыча руды</button>
				</form>
			</div>
		</div>
	</ContentBlock>
</template>

<script setup>
	import { computed } from 'vue';
	import { Link, router, useForm } from '@inertiajs/vue3';
	import ContentBlock from '~/components/ContentBlock.vue';
	import Timer from '~/components/Timer.vue';
	import Name from '~/components/Person/Name.vue';
	import HpLine from '~/components/Person/HpLine.vue';
	import useState from '~/composables/useState.js';

	defineProps({ page: Object });

	const state = useState();
	const user = computed(() => state.user);
	const busy = computed(() => !!user.value.r_date || !!user.value.r_type);
	const form = useForm({ captcha: '' });
	const directions = [
		{ key: 'top', label: 'Вперёд', position: 'col-start-2 row-start-1' },
		{ key: 'left', label: 'Налево', position: 'col-start-1 row-start-2' },
		{ key: 'right', label: 'Направо', position: 'col-start-3 row-start-2' },
		{ key: 'bottom', label: 'Назад', position: 'col-start-2 row-start-3' },
	];

	function act(data) {
		form.transform(() => data).post('/map', { onFinish: () => form.reset() });
	}

	function dig() {
		act({ dig: 'Y', captcha: form.captcha });
	}

	function onTimeout() {
		router.reload();
	}
</script>
