<template>
	<div class="grid w-full grid-cols-[40px_minmax(250px,1fr)_auto_minmax(250px,1fr)_40px]">
		<img class="h-7 w-10" src="/assets/images/main/lp.jpg" width="40" height="28" alt="">
		<div class="whitespace-nowrap bg-repeat-x flex items-center text-white text-sm" style="background-image: url('/assets/images/main/cp.jpg')">
			<div>
				У вас с собой денег:
				<span class="text-[#ede2be]">{{ user.moneys }} зол.</span>
				|
				<span class="text-[#ede2be]">{{ user.credits }} пл.</span>
			</div>
		</div>

		<div class="flex items-start justify-center bg-repeat-x text-white" style="background-image: url('/assets/images/main/cp.jpg')">
			<img class="h-7 w-10" src="/assets/images/main/lpl.jpg" width="40" height="28" alt="">
			<div class="whitespace-nowrap bg-repeat-x px-2.5 text-white h-full flex items-center" style="background-image: url('/assets/images/main/lpr.jpg')">
				{{ title }}
			</div>
			<img class="h-7 w-10" src="/assets/images/main/rpr.jpg" width="40" height="28" alt="">
		</div>

		<div class="bg-repeat-x" style="background-image: url('/assets/images/main/cp.jpg')"></div>
		<img class="h-7 w-10" src="/assets/images/main/rp.jpg" width="40" height="28" alt="">
	</div>
</template>

<script setup>
	import useState from '~/composables/useState.js';
	import { computed } from 'vue';

	defineProps({
		title: {
			type: String,
			default: '',
		}
	});

	const state = useState();
	const user = computed(() => state.user);
</script>
