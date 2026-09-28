<template>
	<Head title="Покупка платины" />
	<ContentBlock title="Покупка платины">
		<template #actions>
			<Link href="/map" class="ui-icon-button" title="Вернуться в город">
				<GameIcon name="back" />
			</Link>
			<Link href="/pay" class="ui-icon-button" title="Обновить">
				<GameIcon name="refresh" />
			</Link>
		</template>

		<div class="text-center">
			<p class="message mb-6 bg-blue-100 text-blue-800">
				Для развития проекта вы можете поддержать нас, приобретая платину по следующему курсу:
				<br />
				<br />
				1 платина — 1 ОК
			</p>

			<div class="grid gap-6 md:grid-cols-2">
				<fieldset>
					<legend class="mb-4 w-full font-bold">Выберите пакет платины</legend>
					<p class="message mb-4 bg-blue-100 text-blue-800">Приобретая платину пакетами, вы получаете бонус +10% от купленной платины</p>
					<div class="space-y-3">
						<div class="creditList flex items-center justify-center gap-4">
							<span class="number w-16">20</span>
							<button type="button" class="ui-button ui-button--compact">Купить</button>
						</div>
						<div class="creditList flex items-center justify-center gap-4">
							<span class="number w-16">50</span>
							<button type="button" class="ui-button ui-button--compact">Купить</button>
						</div>
						<div class="creditList flex items-center justify-center gap-4">
							<span class="number w-16">100</span>
							<button type="button" class="ui-button ui-button--compact">Купить</button>
						</div>
						<div class="creditList flex items-center justify-center gap-4">
							<span class="number w-16">200</span>
							<button type="button" class="ui-button ui-button--compact">Купить</button>
						</div>
						<div class="creditList flex items-center justify-center gap-4">
							<span class="number w-16">500</span>
							<button type="button" class="ui-button ui-button--compact">Купить</button>
						</div>
					</div>
				</fieldset>
				<fieldset>
					<legend class="mb-4 w-full font-bold">Введите число желаемой платины</legend>
					<div class="flex items-center justify-center gap-4">
						<label for="credits" class="sr-only">Количество платины</label>
						<input id="credits" type="text" inputmode="numeric" value="10" class="w-24" />
						<button type="button" class="ui-button ui-button--compact">Купить</button>
					</div>
				</fieldset>
			</div>
		</div>
	</ContentBlock>
</template>

<script setup>
	import { Head, Link } from '@inertiajs/vue3';
	import ContentBlock from '~/components/ContentBlock.vue';
	import GameIcon from '~/components/Layout/GameIcon.vue';
</script>
