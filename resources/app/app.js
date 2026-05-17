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
import GameLayout from '~/layouts/Game.vue';
import { createState, StateSymbol } from '~/composables/useState.js';
import FloatingVue from 'floating-vue';
import toastPlugin from './plugins/toast';
import { time } from '~/utils/format.js';

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
		return [GameLayout];
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
		app.provide(StateSymbol, createState());

		app.use(i18n);

		dayjs.locale(en, null, true);
		dayjs.locale(ru, null, true);

		app.use(FloatingVue);

		app.config.globalProperties.$formatDate = (value, format) => {
			return dayjs(value).tz().format(format)
		};

		app.config.globalProperties.$formatTime = time;

		app.use(toastPlugin);

		app.use(createVfm());

		app.config.errorHandler = (error) => {
			console.error(error);
		}
	}
});
