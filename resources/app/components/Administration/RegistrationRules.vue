<template>
	<div class="service-section">
		<header class="service-heading">
			<h2>Регистратура кланов</h2>
			<p>Соберите союзников и ознакомьтесь с условиями регистрации своего клана.</p>
		</header>
		<div class="service-terms">
			<div>
				<span>Регистрационная пошлина</span>
				<strong>300 пл.</strong>
			</div>
			<div>
				<span>Уровень главы клана</span>
				<strong>{{ minLevel }}</strong>
			</div>
			<div>
				<span>Допуск</span>
				<strong>Проверка инквизиторов</strong>
			</div>
		</div>
		<div class="service-columns">
			<section class="ui-panel service-panel">
				<header class="service-panel-heading">
					<GameIcon name="clan" />
					<h2>Правила регистрации</h2>
				</header>
				<div class="service-panel-body">
					<ul class="service-list">
						<li>Глава клана оплачивает пошлину, достигает {{ minLevel }} уровня и проходит проверку у инквизиторов.</li>
						<li>Подготовьте значок клана: 24 × 14 пикселей, прозрачный GIF.</li>
						<li>Предоставьте историю клана для информационного отдела.</li>
						<li>Склонность для клана покупается отдельно.</li>
					</ul>
				</div>
			</section>
			<section class="ui-panel service-panel">
				<header class="service-panel-heading">
					<GameIcon name="work" />
					<h2>Порядок регистрации</h2>
				</header>
				<div class="service-panel-body">
					<ol class="service-list">
						<li>Соберите игроков с общими интересами и выберите главу клана.</li>
						<li>Подайте заявку в отдел регистрации кланов.</li>
						<li>После одобрения заявки глава проходит проверку у инквизиторов.</li>
						<li>Оплатите регистрационную пошлину.</li>
						<li>После проверки клан открывается и вносится в государственный реестр.</li>
					</ol>
				</div>
			</section>
		</div>
	</div>
</template>

<script setup>
	import GameIcon from '~/components/Layout/GameIcon.vue';

	defineProps({
		minLevel: Number,
	});
</script>
