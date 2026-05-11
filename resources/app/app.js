import { createInertiaApp } from '@inertiajs/vue3';
import i18n from './i18n.js';
import './app.css';
import dayjs from 'dayjs';
import utc from 'dayjs/plugin/utc';
import timezone from 'dayjs/plugin/timezone';
import dayOfYear from 'dayjs/plugin/dayOfYear';
import weekOfYear from 'dayjs/plugin/weekOfYear';
import customParseFormat from 'dayjs/plugin/customParseFormat';
import relativeTime from 'dayjs/plugin/relativeTime';
import en from 'dayjs/locale/en';
import ru from 'dayjs/locale/ru';
import { createVfm } from 'vue-final-modal';
import AppLayout from '~/layouts/AppLayout.vue';

dayjs.extend(utc);
dayjs.extend(timezone);
dayjs.extend(dayOfYear);
dayjs.extend(weekOfYear);
dayjs.extend(customParseFormat);
dayjs.extend(relativeTime);

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
	title: (title) => (title ? `${title} - ${appName}` : appName),
	layout: () => {
		return [AppLayout];
	},
	defaults: {
		visitOptions: (href, options) => {
			return {
				headers: {
					...options.headers,
					'Locale': i18n.global.locale.value,
				},
			};
		},
	},
	withApp(app) {
		app.use(i18n);

		dayjs.locale(en, null, true);
		dayjs.locale(ru, null, true);

		app.use(createVfm());
	}
});
