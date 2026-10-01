<template>
	<section class="person-info-panel ui-panel">
		<h2 class="person-info-panel-heading"><GameIcon name="shield" />Характеристики</h2>
		<table class="person-info-table">
			<tbody>
				<tr v-for="stat in stats" :key="stat.code">
					<th scope="row">{{ $t('stats.' + stat.code) }}</th>
					<td>
						<Popper>
							<button type="button" class="person-info-stat-value" :title="'Подробнее: ' + $t('stats.' + stat.code)">{{ stat.value }}</button>
							<template #content>
								<div class="text-xs">
									<p>Своя: {{ stat.base }}</p>
									<p>Эффекты: {{ stat.value - stat.base }}</p>
								</div>
							</template>
						</Popper>
					</td>
				</tr>
			</tbody>
		</table>
	</section>
</template>

<script setup>
	import Popper from '~/components/Popper.vue';
	import GameIcon from '~/components/Layout/GameIcon.vue';

	defineProps({
		stats: Array,
	});
</script>
