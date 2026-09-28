<template>
	<section>
		<h2 class="mb-3 font-bold">Статистика</h2>
		<table class="table w-full">
			<tbody>
				<tr>
					<th class="text-left">Уровень</th>
					<td class="text-right">{{ person.level }}</td>
				</tr>
				<tr>
					<th class="text-left">Побед</th>
					<td class="text-right">{{ person.wins }}</td>
				</tr>
				<tr>
					<th class="text-left">Поражений</th>
					<td class="text-right">{{ person.losses }}</td>
				</tr>
				<tr>
					<th class="text-left">Ничьих</th>
					<td class="text-right">{{ person.draws }}</td>
				</tr>
				<tr>
					<th class="text-left">Крутизна</th>
					<td class="text-right">{{ person.rating }}</td>
				</tr>
				<tr>
					<th class="text-left">Дата рождения</th>
					<td class="text-right">
						{{ person.admin ? 'До начала времён' : person.created_at ? $formatDate(person.created_at, 'DD.MM.YYYY') : 'Не указана' }}
					</td>
				</tr>
				<tr>
					<th class="text-left">Профессия</th>
					<td class="text-right">{{ $t('profession.' + (person.profession || 0)) }}</td>
				</tr>
				<tr>
					<th class="text-left">Статус</th>
					<td class="text-right">{{ $t('rank.' + (person.rank || 0)) }}</td>
				</tr>
				<tr v-if="person.tribe">
					<th class="text-left">Клан</th>
					<td class="text-right">{{ person.tribe.name }}</td>
				</tr>
			</tbody>
		</table>
	</section>
</template>

<script setup>
	defineProps({
		person: Object,
	});
</script>
