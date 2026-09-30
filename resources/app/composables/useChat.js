import { computed, ref } from 'vue';
import { useHttp } from '@inertiajs/vue3';

export const smiles = [
	'adolf',
	'am',
	'angel',
	'angl',
	'aplause',
	'baby',
	'boxing',
	'bye',
	'crazy',
	'dollar',
	'duel',
	'evil',
	'face1',
	'face2',
	'face5',
	'fingal',
	'fuu',
	'girl',
	'gun1',
	'ha',
	'happy',
	'heart',
	'hello',
	'help',
	'hummer',
	'hummer2',
	'ill',
	'inlove',
	'jack',
	'jedy',
	'killed',
	'king',
	'kiss2',
	'knut',
	'lick',
	'lips',
	'lol',
	'med',
	'roze',
	'mol',
	'ninja',
	'nunchak',
	'ogo',
	'pare',
	'police',
	'prise',
	'punk',
	'ravvin',
	'rip',
	'rupor',
	'scare',
	'shut',
	'sleep',
	'song',
	'strong',
	'training',
	'user',
	'wall',
	'rofl',
	'hunter',
	'bratan',
	'diskot',
	'vglaz',
	'duet',
	'ff',
	'smoke',
	'bita',
	'perec',
	'popope',
	'morpeh',
	'naem',
	'pirat',
	'baraban',
	'klizma',
	'gamer2',
	'pulemet',
	'good2',
	'negative',
	'quiet',
	'ball',
	'pooh',
	'vv',
	'fig1',
	'spam',
	'arbuz',
];

const smileNames = new Set(smiles);

export function reformatMessage(body) {
	const escaped = body
		.replace(/&/g, '&amp;')
		.replace(/</g, '&lt;')
		.replace(/>/g, '&gt;')
		.replace(/"/g, '&quot;')
		.replace(/'/g, '&#039;')
		.replace(/\r?\n/g, '<br>');
	let count = 0;

	return escaped.replace(/:([a-z0-9_]+):/g, (match, smile) => {
		if (!smileNames.has(smile) || count >= 3) {
			return match;
		}

		count += 1;

		return '<img src="/assets/images/smile/' + smile + '.gif" alt="' + smile + '">';
	});
}

export default function useChat(user) {
	const items = ref([]);
	const unread = ref(0);
	const loading = ref(false);
	const loadError = ref('');
	const seenMessageIds = new Set();

	let loaded = false;
	let loadPromise = null;
	let syncPromise = null;
	let syncAgain = false;
	let lastSyncedId = 0;
	let generation = 0;

	const messages = computed(() => {
		return items.value
			.toSorted((a, b) => {
				if (typeof a.id === 'number' && typeof b.id === 'number') {
					return a.id - b.id;
				}

				return Date.parse(a.created_at) - Date.parse(b.created_at) || String(a.id).localeCompare(String(b.id));
			})
			.map(message => {
				const my = message.sender?.id === user.value?.id;

				return {
					...message,
					my,
					me: !my && message.recipients.some(recipient => recipient.id === user.value?.id),
				};
			});
	});

	async function sendMessage(message) {
		const result = await useHttp({ message }).post('/chat/send', {
			onError(errors) {
				throw new Error(errors.message || 'Проверьте текст сообщения.');
			},
			onHttpException(response) {
				const payload = JSON.parse(response.data);

				if (payload.id) {
					addMessage(payload);
				}

				throw new Error(payload.body || payload.message || 'Сообщение не отправлено.');
			},
		});

		addMessage(result);
	}

	async function loadMessages() {
		if (loaded) {
			return;
		}

		if (loadPromise) {
			return loadPromise;
		}

		loading.value = true;
		loadError.value = '';

		const requestGeneration = generation;

		loadPromise = (async () => {
			try {
				const result = await useHttp().get('/chat/last');

				lastSyncedId = Math.max(lastSyncedId, result.at(-1)?.id || 0);

				if (requestGeneration === generation) {
					result.forEach(message => addMessage(message, false));
				}

				loaded = true;
			} catch {
				loadError.value = 'Не удалось загрузить сообщения.';
			} finally {
				loading.value = false;
				loadPromise = null;
			}
		})();

		return loadPromise;
	}

	async function syncMessages() {
		if (syncPromise) {
			syncAgain = true;

			return syncPromise;
		}

		syncPromise = (async () => {
			try {
				do {
					syncAgain = false;

					const needsCatchUp = loaded || loadPromise !== null;

					await loadMessages();

					if (!loaded) {
						return;
					}

					if (needsCatchUp) {
						let result;

						do {
							const requestGeneration = generation;

							result = await useHttp({ after_id: lastSyncedId }).get('/chat/last');

							if (requestGeneration === generation) {
								result.forEach(message => addMessage(message));
							}

							lastSyncedId = Math.max(lastSyncedId, result.at(-1)?.id || 0);
						} while (result.length === 100);
					}
				} while (syncAgain);

				loadError.value = '';
			} catch {
				loadError.value = 'Не удалось обновить сообщения.';
			} finally {
				syncPromise = null;
			}
		})();

		return syncPromise;
	}

	function clear() {
		generation += 1;

		lastSyncedId = items.value.reduce((lastId, item) => {
			return typeof item.id === 'number' ? Math.max(lastId, item.id) : lastId;
		}, lastSyncedId);

		items.value = [];

		clearUnread();
	}

	function addMessage(message, countUnread = true) {
		if (seenMessageIds.has(message.id)) {
			return false;
		}

		seenMessageIds.add(message.id);
		items.value.push(message);

		if (countUnread && message.sender?.id !== user.value?.id) {
			unread.value += 1;
		}

		return true;
	}

	function clearUnread() {
		unread.value = 0;
	}

	return { messages, unread, loading, loadError, sendMessage, loadMessages, syncMessages, clear, addMessage, clearUnread };
}
