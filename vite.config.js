import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite'

export default defineConfig({
	build: {
		chunkSizeWarningLimit: 5000,
		target: 'es2022',
	},
	plugins: [
		laravel({
			input: ['resources/css/styles.css', 'resources/js/game.js'],
			refresh: true,
		}),
		tailwindcss(),
	],
});
