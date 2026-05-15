<template>
	<Popper class="inline">
		<img :src="image" :class="{ 'cursor-pointer': isEdit }" :width="width" :height="height" :alt="title" @click.prevent="unsetItem()">

		<template #content>
			<table width="170">
				<template v-if="item">
					<tr>
						<td align="center" class="it"><b>{{ item.title }}</b></td>
					</tr>
					<tr v-if="item.engraving">
						<td class=it>&bull; Выгравирована надпись: <b>{{ item.engraving }}</b></td>
					</tr>
					<tr v-if="item.wearout_max">
						<td class=it>&bull; Долговечность: <b>{{ item.wearout }} [{{ item.wearout_max }}]</b></td>
					</tr>
					<tr v-if="item.type">
						<td class=it>&bull; Класс: <b>{{ $t('weapon.' + item.type) }}</b></td>
					</tr>
					<tr v-if="item.min || item.max">
						<td class=it>&bull; Удар: <b>{{ item.min }} - {{ item.max }}</b></td>
					</tr>
					<tr v-if="item.hp">
						<td class=it>&bull; Уровень жизни: +<b>{{ item.hp }} HP</b></td>
					</tr>
					<tr v-if="item.energy">
						<td class=it>&bull; Уровень энергии: +<b>{{ item.energy }} EP</b></td>
					</tr>
				</template>
				<tr v-else>
					<td align=center class=it>Пустой слот <b>{{ positionHint }}</b></td>
				</tr>
			</table>
		</template>
	</Popper>
</template>

<script setup>
	import { computed } from 'vue';
	import Popper from '~/components/Popper.vue';
	import { router, usePage } from '@inertiajs/vue3';

	const props = defineProps({
		position: {
			type: Number,
		},
		item: {
			type: Object,
		}
	});

	const page = usePage();

	const isEdit = computed(() => {
		return page.url.startsWith('/person/inventory');
	});

	function unsetItem() {
		if (!props.item || !isEdit.value) {
			return;
		}

		router.get('', {
			unset: props.position,
		});
	}

	const width = computed(() => {
		switch (props.position) {
			case 1: case 2: case 3: case 4: case 5:
				return 60;
			case 6: case 7: case 8:
				return 20;
			case 9:
				return 60;
			case 10: case 11: case 12:
				return 20;
			case 13: case 14: case 15:
			case 16:
				return 60;
			case 17: case 18: case 19:
				return 40;
			case 20:
				return 120;
			case 21: case 22:
				return 60;
		}

		return 0;
	});

	const height = computed(() => {
		switch (props.position) {
			case 1: return 58;
			case 2: return 19;
			case 3: return 58;
			case 4: return 78;
			case 5: return 60;
			case 6: return 20;
			case 7: return 20;
			case 8: return 20;
			case 9: return 28;
			case 10: return 20;
			case 11: return 20;
			case 12: return 20;
			case 13: return 40;
			case 14: return 40;
			case 15: return 40;
			case 16: return 78;
			case 17: return 25;
			case 18: return 25;
			case 19: return 25;
			case 20: return 60;
			case 21: return 19;
			case 22: return 80;
		}

		return 0;
	});

	const positionHint = computed(() => {
		switch (props.position) {
			case 1: return 'Шлем';
			case 2: return 'Ожерелье';
			case 3: return 'Оружие';
			case 4: return 'Доспех';
			case 5: return 'Щит';
			case 6: case 7: case 8: case 10: case 11: case 12: return 'Кольцо';
			case 9: return 'Пояс';
			case 13: return 'Обувь';
			case 14: return 'Нарукавники';
			case 15: return 'Перчатки';
			case 17: case 18: case 19: return 'Магия';
			case 20: return 'Магический предмет';
			case 21: return 'Серьги';
			case 22: return 'Штаны';
			default: return '';
		}
	})

	const title = computed(() => {
		if (props.item) {
			return props.item.title;
		}

		return '';
	})

	const image = computed(() => {
		if (props.item) {
			return '/assets/images/items/' + props.item.type + '/' + props.item.code + '.gif';
		}

		return '/assets/images/items/w' + props.position + '.gif';
	});

	//if (($this->getPosition() == 17 || $this->getPosition() == 18) && $this->id > 0) {
	//	$html .= ' onclick="ShowForm(\'' . $info[1] . '\',\'/map/\',\'\',\'\',\'1\',\'' . $info[0] . '\',\'' . $this->id . '\',\'w' . $this->getPosition() . '\',\'\');"';
	//}
</script>