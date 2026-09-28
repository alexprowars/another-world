<template>
	<Head :title="'Энциклопедия — ' + page.title" />
	<ContentBlock title="Энциклопедия" class="library">
		<template #actions>
			<Link href="/map" class="ui-icon-button" title="Вернуться в город">
				<GameIcon name="back" />
			</Link>
			<Link :href="'/library?section=' + page.section" class="ui-icon-button" title="Обновить">
				<GameIcon name="refresh" />
			</Link>
		</template>

		<div class="library-layout">
			<label class="library-mobile-navigation">
				<span>Раздел энциклопедии</span>
				<select class="ui-input" :value="page.section" @change="router.get('/library', { section: Number($event.target.value) })">
					<optgroup v-for="group in page.groups" :key="group.title" :label="group.title">
						<option v-for="section in group.sections" :key="section.id" :value="section.id">{{ section.title }}</option>
					</optgroup>
				</select>
			</label>
			<nav class="ui-menu library-navigation">
				<p class="library-navigation-title">
					<GameIcon name="book" />
					Оглавление
				</p>
				<section v-for="group in page.groups" :key="group.title" class="library-navigation-group">
					<h2>{{ group.title }}</h2>
					<ul class="ui-menu-list">
						<li v-for="section in group.sections" :key="section.id">
							<Link class="ui-menu-link" :href="'/library?section=' + section.id" :class="{ 'is-active': page.section === section.id }">
								{{ section.title }}
							</Link>
						</li>
					</ul>
				</section>
			</nav>

			<main class="library-content">
				<header class="library-heading">
					<div>
						<p class="library-eyebrow">{{ activeGroup }}</p>
						<h1>{{ page.title }}</h1>
					</div>
					<label v-if="isCatalog" class="library-search">
						<span>Поиск в разделе</span>
						<input class="ui-input" v-model="search" type="search" placeholder="Название предмета" />
					</label>
				</header>

				<template v-if="page.section === 20">
					<div v-if="page.levels.length" class="ui-table-wrap library-table-wrap">
						<table class="ui-table library-table">
							<thead>
								<tr>
									<th>Уровень</th>
									<th>Ап</th>
									<th>Золото</th>
									<th>Сумма золота</th>
									<th>Характеристики</th>
									<th>Сумма характеристик</th>
									<th>Опыт</th>
									<th>Базовый опыт</th>
									<th>Побед ≈</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="level in page.levels" :key="level.id">
									<td>{{ level.level }}</td>
									<td>{{ level.up }}</td>
									<td>{{ level.gold }}</td>
									<td>{{ level.totalGold }}</td>
									<td>{{ level.updates }}</td>
									<td>{{ level.totalUpdates }}</td>
									<td>{{ level.exp }}</td>
									<td>{{ level.base }}</td>
									<td>{{ level.wins }}</td>
								</tr>
							</tbody>
						</table>
					</div>
					<p v-else class="text-center">Таблица опыта пока пуста.</p>
				</template>

				<div v-else-if="page.section === 21" class="space-y-4">
					<p>В центре занятости можно заработать золото. Работа расходует запас сил, который восстанавливается в боях.</p>
					<fieldset v-for="type in page.workTypes" :key="type.id" class="library-work">
						<legend class="px-2 font-bold">{{ type.title }}</legend>
						<p class="mb-3">Требуется уровень {{ type.level }}. Расход сил: {{ type.activity }} ед. за час.</p>
						<div v-if="type.works.length" class="ui-table-wrap library-table-wrap">
							<table class="ui-table library-table">
								<thead>
									<tr>
										<th>№</th>
										<th>Наименование</th>
										<th>Срок работы</th>
										<th>Зарплата</th>
									</tr>
								</thead>
								<tbody>
									<tr v-for="(work, index) in type.works" :key="work.id">
										<td>{{ index + 1 }}</td>
										<td>{{ work.title }}</td>
										<td>{{ $formatTime(work.duration) }}</td>
										<td>{{ work.price }} зол.</td>
									</tr>
								</tbody>
							</table>
						</div>
						<p v-else>В этом разделе пока нет работы.</p>
					</fieldset>
					<p v-if="!page.workTypes.length" class="text-center">Список работ пока пуст.</p>
				</div>

				<div v-else-if="page.section === 22" class="space-y-4">
					<p>В Академии можно получить профессию. Ниже приведены требования, срок и стоимость обучения.</p>
					<div v-if="page.professions.length" class="ui-table-wrap library-table-wrap">
						<table class="ui-table library-table">
							<thead>
								<tr>
									<th>№</th>
									<th>Наименование</th>
									<th>Уровень</th>
									<th>Срок обучения</th>
									<th>Стоимость обучения</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="(profession, index) in page.professions" :key="profession.id">
									<td>{{ index + 1 }}</td>
									<td>{{ profession.title }}</td>
									<td>{{ profession.level }}</td>
									<td>{{ $formatTime(profession.duration) }}</td>
									<td>{{ profession.price }} зол.</td>
								</tr>
							</tbody>
						</table>
					</div>
					<p v-else class="text-center">Список профессий пока пуст.</p>
				</div>

				<Modifiers v-else-if="page.section === 23" />

				<template v-else>
					<div v-if="filteredItems.length" class="library-items">
						<ItemCard v-for="entry in filteredItems" :key="entry.id" :item="entry.item" />
					</div>
					<div v-else class="ui-empty">
						<GameIcon name="book" />
						<h2>{{ search.trim() ? 'Ничего не найдено' : 'Раздел пока пуст' }}</h2>
						<p>{{ search.trim() ? 'Попробуйте другое название предмета.' : 'Здесь появятся предметы и их характеристики.' }}</p>
						<button v-if="search" type="button" class="ui-button" @click="search = ''">Сбросить поиск</button>
					</div>
				</template>
			</main>
		</div>
	</ContentBlock>
</template>

<script setup>
	import { Head, Link, router } from '@inertiajs/vue3';
	import ContentBlock from '~/components/ContentBlock.vue';
	import GameIcon from '~/components/Layout/GameIcon.vue';
	import { computed, ref, watch } from 'vue';
	import ItemCard from '~/components/Library/ItemCard.vue';
	import Modifiers from '~/components/Library/Modifiers.vue';

	const props = defineProps({ page: { type: Object, required: true } });
	const search = ref('');
	const isCatalog = computed(() => Array.isArray(props.page.items));
	const activeGroup = computed(() => props.page.groups.find(group => group.sections.some(section => section.id === props.page.section))?.title);
	const filteredItems = computed(() => {
		const query = search.value.trim().toLocaleLowerCase('ru');
		return (props.page.items ?? []).filter(entry => entry.item.title.toLocaleLowerCase('ru').includes(query));
	});

	watch(
		() => props.page.section,
		() => {
			search.value = '';
		},
	);
</script>
