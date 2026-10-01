<template>
	<section class="person-info-panel ui-panel">
		<h2 class="person-info-panel-heading"><GameIcon name="swords" />Статистика</h2>
		<table class="person-info-table">
			<tbody>
				<tr>
					<th scope="row">Уровень</th>
					<td>{{ person.level }}</td>
				</tr>
				<tr>
					<th scope="row">Побед</th>
					<td>{{ person.wins }}</td>
				</tr>
				<tr>
					<th scope="row">Поражений</th>
					<td>{{ person.losses }}</td>
				</tr>
				<tr>
					<th scope="row">Ничьих</th>
					<td>{{ person.draws }}</td>
				</tr>
				<tr>
					<th scope="row">Крутизна</th>
					<td>{{ person.rating }}</td>
				</tr>
				<tr>
					<th scope="row">Дата рождения</th>
					<td>
						{{ person.admin ? 'До начала времён' : person.created_at ? $formatDate(person.created_at, 'DD.MM.YYYY') : 'Не указана' }}
					</td>
				</tr>
				<tr>
					<th scope="row">Профессия</th>
					<td>{{ $t('profession.' + (person.profession || 0)) }}</td>
				</tr>
				<tr>
					<th scope="row">Статус</th>
					<td>{{ $t('rank.' + (person.rank || 0)) }}</td>
				</tr>
				<tr v-if="person.tribe">
					<th scope="row">Клан</th>
					<td>{{ person.tribe.name }}</td>
				</tr>
			</tbody>
		</table>
	</section>
</template>

<script setup>
	import GameIcon from '~/components/Layout/GameIcon.vue';

	defineProps({
		person: Object,
	});
</script>
