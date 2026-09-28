<template>
	<Head :title="page.tribe ? 'Клан «' + page.tribe.name + '»' : 'Клан'" />
	<ContentBlock :title="page.tribe ? 'Клан «' + page.tribe.name + '»' : 'Клан'">
		<template #actions>
			<Link href="/map" class="ui-icon-button" title="Вернуться в город">
				<GameIcon name="back" />
			</Link>
			<Link :href="'/tribe?section=' + page.section" class="ui-icon-button" title="Обновить">
				<GameIcon name="refresh" />
			</Link>
		</template>

		<p v-if="!page.tribe">Вы не состоите ни в каком клане!</p>
		<div v-else class="grid gap-6 lg:grid-cols-[220px_1fr]">
			<aside class="space-y-4">
				<h3 class="font-bold">Управление кланом</h3>
				<p>
					Казна:
					<b>{{ page.tribe.moneys }} зол.</b>
				</p>
				<nav class="ui-menu flex flex-col gap-2">
					<Link class="ui-menu-link" href="/tribe" :class="{ 'is-active': page.section === 'members' }">Состав клана</Link>
					<Link class="ui-menu-link" href="/tribe?section=artifacts" :class="{ 'is-active': page.section === 'artifacts' }">Артефакты клана</Link>
					<Link
						class="ui-menu-link"
						v-if="page.permissions.leader"
						href="/tribe?section=settings"
						:class="{ 'is-active': page.section === 'settings' }"
					>
						Редактирование клана
					</Link>
					<Link class="ui-menu-link" v-if="page.permissions.withdraw" href="/tribe?section=logs" :class="{ 'is-active': page.section === 'logs' }">
						Журнал клана
					</Link>
				</nav>
				<form class="space-y-2" @submit.prevent="submitMoney">
					<h4 class="font-bold">Казна</h4>
					<label class="block">
						Операция
						<select v-model="moneyForm.action" class="ui-input ui-input--compact">
							<option value="deposit">Положить в казну</option>
							<option v-if="page.permissions.withdraw" value="withdraw">Взять из казны</option>
						</select>
					</label>
					<label class="block">
						Сумма, зол.
						<input
							v-model="moneyForm.amount"
							class="ui-input ui-input--compact"
							type="text"
							inputmode="decimal"
							maxlength="13"
							placeholder="0,00"
							required
						/>
					</label>
					<p v-for="(error, field) in moneyForm.errors" :key="field" class="text-red-700" role="alert">{{ error }}</p>
					<button class="ui-button ui-button--compact" type="submit" :disabled="busy">
						{{ moneyForm.action === 'withdraw' ? 'Снять' : 'Пополнить' }}
					</button>
				</form>
				<form v-if="page.permissions.members" class="space-y-2" @submit.prevent="submitMember">
					<h4 class="font-bold">Участники</h4>
					<label class="block">
						Действие
						<select v-model="memberForm.action" class="ui-input ui-input--compact" @change="memberForm.clearErrors()">
							<option value="add">Принять в клан</option>
							<option value="remove">Исключить из клана</option>
							<option value="rank">Изменить ранг</option>
							<option v-if="page.permissions.leader" value="transfer">Передать полномочия главы</option>
						</select>
					</label>
					<label class="block">
						Ник персонажа
						<input v-model.trim="memberForm.name" class="ui-input ui-input--compact" type="text" maxlength="100" required />
					</label>
					<label v-if="memberForm.action === 'rank'" class="block">
						Новый ранг
						<select v-model="memberForm.rank" class="ui-input ui-input--compact">
							<option v-for="rank in availableRanks" :key="rank.id" :value="rank.id">{{ rank.name }}</option>
						</select>
					</label>
					<p v-if="memberForm.action === 'add'">
						Стоимость приёма: {{ page.recruit_price }} зол. из казны. Требуются 4 уровень и действующая проверка инквизиторов.
					</p>
					<p v-if="memberForm.action === 'remove'">Для исключения требуется действующая проверка инквизиторов.</p>
					<p v-if="memberForm.action === 'transfer'">После передачи полномочий вы станете бойцом клана.</p>
					<p v-for="(error, field) in memberForm.errors" :key="field" class="text-red-700" role="alert">{{ error }}</p>
					<button class="ui-button ui-button--compact" type="submit" :disabled="busy">Выполнить</button>
				</form>
			</aside>

			<section class="min-w-0">
				<template v-if="page.section === 'members'">
					<h3 class="mb-3 font-bold">Состав клана</h3>
					<div class="overflow-x-auto">
						<table class="w-full text-left">
							<thead>
								<tr>
									<th class="p-2">Статус</th>
									<th class="p-2">Персонаж</th>
									<th class="p-2">Ранг в клане</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="member in page.members" :key="member.id" class="border-t border-stone-300">
									<td class="p-2" :class="member.online ? 'text-green-700' : 'text-stone-500'">
										{{ member.online ? 'В игре' : 'Не в сети' }}
									</td>
									<td class="p-2"><Name :player="member" /></td>
									<td class="p-2">{{ rankName(member.tribe_rank) }}</td>
								</tr>
							</tbody>
						</table>
					</div>
					<p class="mt-3">
						В клане:
						<b>{{ page.members.length }}</b>
						человек. Приём следующего участника:
						<b>{{ page.recruit_price }} зол.</b>
					</p>
					<div v-if="page.tribe.about" class="mt-6">
						<h3 class="font-bold">Информация о клане</h3>
						<p class="whitespace-pre-wrap">{{ page.tribe.about }}</p>
					</div>
					<div v-if="page.tribe.laws" class="mt-4">
						<h3 class="font-bold">Законы клана</h3>
						<p class="whitespace-pre-wrap">{{ page.tribe.laws }}</p>
					</div>
					<p v-if="website" class="mt-4"><a :href="website" target="_blank" rel="noopener noreferrer">Сайт клана</a></p>
				</template>

				<template v-else-if="page.section === 'artifacts'">
					<h3 class="mb-3 font-bold">Артефакты клана</h3>
					<div v-if="page.items.length" class="shop-items grid grid-cols-1 gap-4 xl:grid-cols-2">
						<SellItem v-for="entry in page.items" :key="entry.item.id" :item="entry.item">
							<template #actions><span>Клановый артефакт</span></template>
							<template #details>
								<div v-if="entry.holder" class="my-2">
									Находится у персонажа:
									<Name :player="entry.holder" />
								</div>
							</template>
						</SellItem>
					</div>
					<p v-else>У клана нет клановых артефактов.</p>
				</template>

				<form v-else-if="page.section === 'settings' && page.permissions.leader" class="max-w-xl space-y-4" @submit.prevent="submitSettings">
					<h3 class="font-bold">Редактирование клана</h3>
					<label class="block">
						Информация о клане
						<textarea v-model="settingsForm.about" class="ui-input ui-input--compact" rows="6" maxlength="10000" />
					</label>
					<label class="block">
						Законы клана
						<textarea v-model="settingsForm.laws" class="ui-input ui-input--compact" rows="6" maxlength="10000" />
					</label>
					<label class="block">
						Сайт клана
						<input
							v-model.trim="settingsForm.url"
							class="ui-input ui-input--compact"
							type="url"
							maxlength="255"
							placeholder="https://example.com"
						/>
					</label>
					<p v-for="(error, field) in settingsForm.errors" :key="field" class="text-red-700" role="alert">{{ error }}</p>
					<button class="ui-button ui-button--compact" type="submit" :disabled="busy">Сохранить изменения</button>
				</form>

				<template v-else-if="page.section === 'logs'">
					<h3 class="mb-3 font-bold">Последние 50 операций клана</h3>
					<div v-if="page.logs.length" class="overflow-x-auto">
						<table class="w-full text-left">
							<thead>
								<tr>
									<th class="p-2">Дата</th>
									<th class="p-2">Персонаж</th>
									<th class="p-2">Действие</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="log in page.logs" :key="log.id" class="border-t border-stone-300">
									<td class="p-2">{{ log.date }}</td>
									<td class="p-2">{{ log.user }}</td>
									<td class="p-2">{{ log.action }}</td>
								</tr>
							</tbody>
						</table>
					</div>
					<p v-else>В журнале пока нет записей.</p>
				</template>
			</section>
		</div>
	</ContentBlock>
</template>

<script setup>
	import { computed } from 'vue';
	import { Head, Link, useForm } from '@inertiajs/vue3';
	import ContentBlock from '~/components/ContentBlock.vue';
	import GameIcon from '~/components/Layout/GameIcon.vue';
	import Name from '~/components/Person/Name.vue';
	import SellItem from '~/components/City/Shop/SellItem.vue';

	const props = defineProps({ page: Object });
	const moneyForm = useForm({ action: 'deposit', amount: '' });
	const memberForm = useForm({ action: 'add', name: '', rank: 0 });
	const settingsForm = useForm({
		action: 'settings',
		about: props.page.tribe?.about ?? '',
		laws: props.page.tribe?.laws ?? '',
		url: props.page.tribe?.url ?? '',
	});
	const busy = computed(() => moneyForm.processing || memberForm.processing || settingsForm.processing);
	const availableRanks = computed(() => props.page.ranks.filter(rank => rank.id !== 1 && (props.page.permissions.leader || ![2, 3, 5].includes(rank.id))));
	const website = computed(() => (/^https?:\/\//i.test(props.page.tribe?.url ?? '') ? props.page.tribe.url : null));

	function rankName(id) {
		return props.page.ranks.find(rank => rank.id === id)?.name ?? 'Боец';
	}

	function clearErrors() {
		moneyForm.clearErrors();
		memberForm.clearErrors();
		settingsForm.clearErrors();
	}

	function submitMoney() {
		if (busy.value) return;
		clearErrors();
		moneyForm.post('/tribe', { preserveScroll: true, onSuccess: () => moneyForm.reset() });
	}

	function submitMember() {
		if (busy.value) return;
		clearErrors();
		memberForm.post('/tribe', { preserveScroll: true, onSuccess: () => memberForm.reset() });
	}

	function submitSettings() {
		if (busy.value) return;
		clearErrors();
		settingsForm.post('/tribe', { preserveScroll: true });
	}
</script>
