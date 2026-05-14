<template>
	<div class="header">
		<div class="corner left"></div>
		<div class="corner right"></div>
		<div class="links">
			<div class="left">
				<span>
					<Link href="/map">
						<div class="icon_1"></div>
						<div class="icon_1_text">Город</div>
					</Link>
				</span>
				<span>
					<Link href="/person">
						<div class="icon_2"></div>
						<div class="icon_2_text">Персонаж</div>
					</Link>
				</span>
				<span>
					<Link href="/library">
						<div class="icon_4"></div>
						<div class="icon_4_text">Библиотека</div>
					</Link>
				</span>
			</div>
			<div class="center logo"></div>
			<div class="right">
				<span>
					<Link href="/battle">
						<div class="icon_5"></div>
						<div class="icon_5_text">Поединки</div>
					</Link>
				</span>
				<span>
					<Link href="/person/work">
						<div class="icon_6"></div>
						<div class="icon_6_text">Заработок</div>
					</Link>
				</span>
				<span>
					<Link href="/pay">
						<div class="icon_7"></div>
						<div class="icon_7_text">Платина</div>
					</Link>
				</span>
			</div>
		</div>
	</div>
</template>

<script setup>
	import { Link } from '@inertiajs/vue3';
</script>