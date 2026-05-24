<template>
	<div class="shop-item">
		<table border="1">
			<tr>
				<td width="30%" align="center">
					<div><img :src="'/assets/images/items/' + item.type + '/' + item.code + '.gif'" :alt="item.title"></div>
					<a href="" @click.prevent="giftItem">
						<b>Подарить</b>
					</a>
				</td>
				<td width="70%" valign="top" class="text-xs">
					<div class="font-bold">{{ item.title }}</div>
					<div>Гос. цена: <b>{{ item.price }}</b> зол.</div>
					<div v-if="item.wearout">
						Долговечность предмета: <b>{{ item.wearout }}</b>/<b>{{ item.wearout_max }}</b>
					</div>

					<div v-if="Object.keys(item['requirements']).length > 0" class="mt-2">
						<div class="font-bold">Минимальные требования:</div>
						<div v-if="item['requirements']['level']" :class="{ 'text-red-600': user.level < item['requirements']['level'] }">
							Уровень: {{ item['requirements']['level'] }}
						</div>
						<div v-if="item['requirements']['profession']" :class="{ 'text-red-600': user.profession !== item['requirements']['profession'] }">
							Профессия: {{ $t('profession.' + item['requirements']['profession']) }}
						</div>
						<template v-for="stat in ['strength', 'dexterity', 'agility', 'vitality', 'magic', 'intelligence', 'battery']">
							<div v-if="item['requirements'][stat]" :class="{ 'text-red-600': user[stat] !== item['requirements'][stat] }">
								{{ $t('stats.' . stat) }}: {{ item['requirements'][stat] }}
							</div>
						</template>
					</div>
					<div v-if="bonuses.length" class="mt-2">
						<div class="font-bold">Действие предмета:</div>
						<div v-for="row in bonuses">
							{{ row.label }}: {{ row.value }}
						</div>
					</div>
					<div v-if="item.about" class="mt-2">
						<b>Дополнительная информация:</b><br>{{ item.about }}
					</div>
				</td>
			</tr>
		</table>
	</div>
</template>

<script setup>
	import useState from '~/composables/useState.js';
	import { computed } from 'vue';
	import { openPopupModal } from '~/composables/useModals.js';
	import Form from '~/components/City/Gift/Form.vue';

	const props = defineProps({
		item: Object,
	});

	const state = useState();
	const user = computed(() => state.user);

	function giftItem() {
		openPopupModal(Form, {
			item: props.item,
		});
	}

	const bonuses = computed(() => [
		bonus('min', 'Минимальный урон'),
		bonus('max', 'Максимальный урон'),
		bonus('armor1', 'Броня головы'),
		bonus('armor2', 'Броня корпуса'),
		bonus('armor3', 'Броня живота'),
		bonus('armor4', 'Броня пояса'),
		bonus('armor5', 'Броня ног'),
		bonus('strength', 'Сила'),
		bonus('dexterity', 'Удача'),
		bonus('agility', 'Ловкость'),
		bonus('vitality', 'Выносливость'),
		bonus('intelligence', 'Разум'),
		bonus('krit', 'Крит'),
		bonus('unkrit', 'Антикрит'),
		bonus('uv', 'Уворот'),
		bonus('unuv', 'Антиуворот'),
		bonus('mkrit', 'Мощность крита'),
		bonus('pblock', 'Пробой блока'),
		bonus('mblock', 'Мощность блока'),
		bonus('pbr', 'Пробой брони'),
		bonus('kbr', 'Крепость брони'),
		bonus('metk', 'Меткость'),
		bonus('hp', 'Уровень жизни'),
		bonus('energy', 'Уровень энергии'),
	].filter(Boolean));

	function bonus(key, label) {
		const value = props.item[key];

		if (!value) {
			return null;
		}

		return {
			label, value: value > 0 ? `+${value}` : String(value),
		};
	}
</script>