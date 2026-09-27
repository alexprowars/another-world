<template>
	<section>
		<h2 class="mb-3 font-bold">Характеристики</h2>
		<table class="table w-full">
			<tbody>
				<tr v-for="stat in stats" :key="stat.code">
					<th class="text-left">{{ $t('stats.' + stat.code) }}</th>
					<td class="text-right">
						<Popper>
							<span class="font-bold text-red-700">{{ stat.value }}</span>
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

	defineProps({
		stats: Array,
	});
</script>
