import { usePage } from '@inertiajs/vue3';
import { computed, hasInjectionContext, inject, reactive } from 'vue';

export const StateSymbol = Symbol('state');

export function createState () {
	const page = usePage();
	const props = computed(() => page.props.state || {});

	return reactive({
		user: computed(() => props.value.user),
		locale: computed(() => props.value.locale),
	});
}

export default function useState () {
	const state = hasInjectionContext()
		? inject(StateSymbol, null) : null;

	if (state) {
		return state;
	}

	return createState();
}
