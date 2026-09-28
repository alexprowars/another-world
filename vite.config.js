import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import svgLoader from 'vite-svg-loader';
import inertia from '@inertiajs/vite';
import { resolve } from 'path';

export default defineConfig({
	build: {
		chunkSizeWarningLimit: 5000,
		target: 'es2022',
	},
	resolve: {
		alias: {
			'~': resolve(import.meta.dirname, 'resources/app'),
		},
	},
	plugins: [
		laravel({
			input: ['resources/app/app.js'],
			//refresh: true,
		}),
		tailwindcss(),
		svgLoader(),
		vue({
			template: {
				transformAssetUrls: {
					base: null,
					includeAbsolute: false,
				},
				compilerOptions: {
					whitespace: 'preserve',
				},
			},
		}),
		inertia({
			ssr: {
				host: process.env.INERTIA_SSR_HOST || '127.0.0.1',
			},
		}),
	],
});
