<template>
	<svg class="game-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
		<path :d="paths[name] || paths.shield" />
	</svg>
</template>

<script setup>
	defineProps({
		name: String
	});

	const paths = {
		church: 'M12 2v5m-2-3h4M4 21V11l8-5 8 5v10M2 21h20M9 21v-5a3 3 0 0 1 6 0v5M12 9v3M6 13v2m12-2v2',
		rings: 'M14 15a6 6 0 1 1-12 0 6 6 0 0 1 12 0Zm8-6a6 6 0 1 1-12 0 6 6 0 0 1 12 0ZM6 7l2-3 2 3m4-6 2 2 2-2',
		dice: 'M4 3h16a1 1 0 0 1 1 1v16a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1ZM7 7h.01M17 7h.01M12 12h.01M7 17h.01M17 17h.01',
		ticket: 'M3 5h18v4a3 3 0 0 0 0 6v4H3v-4a3 3 0 0 0 0-6V5ZM15 5v2m0 3v4m0 3v2M7 9h4m-4 4h4',
		city: 'M2 21V7l3-4 3 4v14M2 8h6M16 21V7l3-4 3 4v14M16 8h6M8 11V9h3v2h2V9h3M10 21v-5a2 2 0 0 1 4 0v5M5 12v2m14-2v2M1 21h22',
		character: 'M5 10V9a7 7 0 0 1 14 0v1M4 10h16l-1 8-7 4-7-4-1-8ZM12 2v8M4 10l8 3 8-3M12 13v9M8 14v3m8-3v3',
		shield: 'M5 3h14v7c0 5-3 8-7 11-4-3-7-6-7-11V3Z',
		clan: 'M5 21V3m0 1h14v13l-7-4-7 4',
		justice: 'M12 3v17M7 21h10M3 7h18M6 7l-4 8a4 4 0 0 0 8 0L6 7Zm12 0-4 8a4 4 0 0 0 8 0l-4-8Z',
		armor: 'M8 3 3 6l2 6 2-1-1 10h12l-1-10 2 1 2-6-5-3c0 4-8 4-8 0ZM7 16h10M12 7v9',
		gift: 'M3 8h18v4H3V8ZM5 12v9h14v-9M12 8v13M12 8H8a3 3 0 1 1 3-3l1 3Zm0 0h4a3 3 0 1 0-3-3l-1 3Z',
		gem: 'm3 8 4-5h10l4 5-9 13L3 8Zm0 0h18M7 3l2 5 3 13 3-13 2-5M9 8l3-5 3 5',
		tools: 'm14 5 5 5 3-3-5-5-3 3ZM16 8 4 20l-2-2L14 6M3 3l4 1 1 4-2 2-4-4 1-3Zm5 5 4 4m2 2 6 6 2-2-6-6',
		book: 'M12 6C9 3 5 3 2 4v15c4-1 7-1 10 2 3-3 6-3 10-2V4c-3-1-7-1-10 2Zm0 0v15',
		swords: 'm4 3 12 12M3 3l1 5 9 9M12 18l6-6m-3 3 6 6m-2 0 2-2M20 3l-7 7m8-7-1 5-3 3M6 12l6 6m-3-3-6 6m0-2 2 2',
		coins: 'M16 7c0 2-3 3-7 3S2 9 2 7s3-3 7-3 7 1 7 3ZM2 7v5c0 2 3 3 7 3m-7-3v5c0 2 3 3 7 3m7-13v4m6 3c0 2-3 3-6 3s-6-1-6-3 3-3 6-3 6 1 6 3Zm-12 0v5c0 2 3 3 6 3s6-1 6-3v-5',
		pin: 'M19 10c0 5-7 11-7 11S5 15 5 10a7 7 0 1 1 14 0ZM15 10a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z',
		chat: 'M21 11a9 8 0 0 1-9 8H7l-5 3 2-6a8 8 0 0 1-1-5 9 8 0 0 1 18 0ZM7 10h10M7 14h6',
		mail: 'M3 5h18v14H3V5Zm0 0 9 7 9-7M3 19l6-6m12 6-6-6',
		quill: 'm3 21 5-5m-3 3 2-7L17 2l5 5-10 10-7 2Zm2-7 5 5M14 5l5 5',
		users: 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M13 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0ZM17 3a4 4 0 0 1 0 8m2 4a4 4 0 0 1 3 4v2',
		send: 'm22 2-7 20-4-9-9-4L22 2Zm0 0L11 13',
		smile: 'M22 12a10 10 0 1 1-20 0 10 10 0 0 1 20 0ZM8 9h.01M16 9h.01M8 15s1 2 4 2 4-2 4-2',
		trash: 'M3 6h18M9 6V3h6v3M5 6l1 15h12l1-15M10 10v7m4-7v7',
		chevron: 'm6 9 6 6 6-6',
		back: 'M20 12H4m7-7-7 7 7 7',
		logout: 'M10 3H4v18h6M9 12h12m-5-5 5 5-5 5',
		forward: 'M4 12h16m-7-7 7 7-7 7',
		work: 'M4 5h16v16H4V5ZM9 5V2h6v3M8 10h8M8 14h8M8 18h5',
		hourglass: 'M6 3h12M6 21h12M7 3v4c0 2 5 5 5 5s5-3 5-5V3M7 21v-4c0-2 5-5 5-5s5 3 5 5v4M9 18h6',
		health: 'M9 3h6v6h6v6h-6v6H9v-6H3V9h6V3Z',
		energy: 'm13 2-9 12h7l-1 8 10-13h-8l1-7Z',
		refresh: 'M20 7a9 9 0 1 0 1 9M20 2v5h-5',
		shuffle: 'M3 5h3c5 0 7 14 12 14h3m-4-4 4 4-4 4M3 19h3c2 0 3-2 4-4m4-6c1-2 2-4 4-4h3m-4-4 4 4-4 4',
		transfer: 'M3 3h12v12H3V3Zm0 4h12M7 3v4m9 5 5 5-5 5m-7-5h12',
	};
</script>
