<template>
	<Head :title="'Энциклопедия — ' + page.title"/>
	<ContentBlock title="Энциклопедия">
		<div class="flex flex-col gap-6 md:flex-row">
			<main class="min-w-0 flex-1">
				<h1 class="mb-4 text-center text-lg font-bold">{{ page.title }}</h1>

				<template v-if="page.section === 20">
					<div v-if="page.levels.length" class="overflow-x-auto">
						<table class="library-table">
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
					<fieldset v-for="type in page.workTypes" :key="type.id" class="rounded border border-slate-300 p-3">
						<legend class="px-2 font-bold">{{ type.title }}</legend>
						<p class="mb-3">Требуется уровень {{ type.level }}. Расход сил: {{ type.activity }} ед. за час.</p>
						<div v-if="type.works.length" class="overflow-x-auto">
							<table class="library-table">
								<thead>
									<tr><th>№</th><th>Наименование</th><th>Срок работы</th><th>Зарплата</th></tr>
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
					<div v-if="page.professions.length" class="overflow-x-auto">
						<table class="library-table">
							<thead>
								<tr><th>№</th><th>Наименование</th><th>Уровень</th><th>Срок обучения</th><th>Стоимость обучения</th></tr>
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

				<Modifiers v-else-if="page.section === 23"/>

				<template v-else>
					<div v-if="page.items.length" class="shop-items grid grid-cols-1 gap-4 lg:grid-cols-2 xl:grid-cols-3">
						<Item v-for="item in page.items" :key="item.id" :item="item" read-only/>
					</div>
					<p v-else class="text-center">В данном отделе нет предметов.</p>
				</template>
			</main>

			<nav class="w-full shrink-0 space-y-4 md:w-52" aria-label="Библиотека">
				<section v-for="group in page.groups" :key="group.title" class="rounded border border-slate-300 p-3">
					<h2 class="mb-2 font-bold">{{ group.title }}</h2>
					<ul class="space-y-1">
						<li v-for="section in group.sections" :key="section.id">
							<Link :href="'/library?section=' + section.id"
								:class="{ 'font-bold underline': page.section === section.id }"
								:aria-current="page.section === section.id ? 'page' : undefined"
							>{{ section.title }}</Link>
						</li>
					</ul>
				</section>
			</nav>
		</div>
	</ContentBlock>
</template>

<script setup>
	import { Head, Link } from '@inertiajs/vue3';
	import ContentBlock from '~/components/ContentBlock.vue';
	import Item from '~/components/City/Shop/Item.vue';
	import Modifiers from '~/components/Library/Modifiers.vue';

	defineProps({ page: Object });
</script>

<style scoped>
	.library-table {
		width: 100%;
		border-collapse: collapse;
		text-align: center;
	}

	.library-table th,
	.library-table td {
		padding: 0.5rem;
		border-bottom: 1px solid #cbd5e1;
	}

	.library-table tbody tr:nth-child(odd) {
		background: rgb(241 245 249 / 50%);
	}
</style>
