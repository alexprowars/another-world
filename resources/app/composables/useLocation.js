import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

export default function useLocation() {
	const page = usePage();

	return computed(() => page.props.location);
}
