<template>
	<ContentBlock title="Тюрьма">
		<template #actions>
			<Link v-if="!page.until" href="/map/change/666" class="ui-icon-button" title="Назад">
				<GameIcon name="back" />
			</Link>
			<Link href="/map" class="ui-icon-button" title="Обновить">
				<GameIcon name="refresh" />
			</Link>
		</template>

		<section v-if="page.until" class="ui-panel service-panel service-panel--narrow">
			<header class="service-panel-heading">
				<GameIcon name="justice" />
				<h2>Вы отбываете наказание</h2>
			</header>
			<div class="service-panel-body">
				<div class="service-timer">
					<span>До освобождения</span>
					<Timer :key="page.until" :value="page.until" :callback="onTimeout" class="service-countdown" />
				</div>
				<div class="prison-reason">
					<h3>Причина заключения</h3>
					<p>{{ page.reason || 'Причина не указана.' }}</p>
				</div>
			</div>
		</section>
		<div v-else class="service-section">
			<div class="ui-notice ui-notice--green ui-notice--with-icon">
				<GameIcon name="shield" />
				<div>
					<strong>Вы на свободе</strong>
					<p>Соблюдайте правила игры, чтобы не оказаться среди заключённых.</p>
				</div>
			</div>
			<header class="service-heading">
				<h2>
					Список заключённых
					<span class="ui-badge">{{ page.prisoners.length }}</span>
				</h2>
			</header>
			<div v-if="page.prisoners.length" class="ui-table-wrap service-table-wrap">
				<table class="ui-table service-table">
					<thead>
						<tr>
							<th>Персонаж</th>
							<th>Причина</th>
							<th>Освобождение</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="prisoner in page.prisoners" :key="prisoner.id">
							<td>
								<strong>{{ prisoner.name }}</strong>
							</td>
							<td>{{ prisoner.reason || '—' }}</td>
							<td class="service-number">{{ $formatDate(prisoner.until, 'DD.MM.YYYY HH:mm') }}</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div v-else class="ui-empty" role="status">
				<GameIcon name="justice" />
				<h3>Камеры пустуют</h3>
				<p>Сейчас в тюрьме нет заключённых.</p>
			</div>
		</div>

		<template #footer>
			<GameIcon name="hourglass" />
			<span>Срок заключения завершается автоматически по истечении наказания.</span>
		</template>
	</ContentBlock>
</template>

<script setup>
	import { Link, router } from '@inertiajs/vue3';
	import ContentBlock from '~/components/ContentBlock.vue';
	import GameIcon from '~/components/Layout/GameIcon.vue';
	import Timer from '~/components/Timer.vue';

	defineProps({
		page: Object,
	});

	function onTimeout() {
		router.reload();
	}
</script>
