import { computed, ref } from 'vue'
import { useHttp } from '@inertiajs/vue3';

export const smiles = [
	'adolf','am','angel','angl','aplause','baby','boxing','bye','crazy','dollar','duel','evil','face1','face2','face5','fingal','fuu','girl','gun1','ha',
	'happy','heart','hello','help','hummer','hummer2','ill','inlove','jack','jedy','killed','king','kiss2','knut','lick','lips','lol','med','roze','mol',
	'ninja','nunchak','ogo','pare','police','prise','punk','ravvin','rip','rupor','scare','shut','sleep','song','strong','training','user','wall','rofl',
	'hunter','bratan','diskot','vglaz','duet','ff','smoke','bita','perec','popope','morpeh','naem','pirat','baraban','klizma','gamer2','pulemet','good2',
	'negative','quiet','ball','pooh','vv','fig1', 'spam', 'arbuz'
];

export function reformatMessage(message) {
	let j = 0;

	smiles.every((smile) => {
		while (message.indexOf(':' + smile + ':') >= 0) {
			message = message.replace(':' + smile + ':', '<img src="/assets/images/smile/' + smile + '.gif" alt="' + smile + '">')

			if (++j >= 3) {
				break;
			}
		}

		return j < 3;
	})

	return message;
}

export default function useChat () {
	const messages = ref([]);
	const unread = ref(0);

	const sortedMessages = computed(() => {
		return messages.value.sort((a, b) => a['time'] < b['time'] ? -1 : 1);
	});

	async function sendMessage (message) {
		message = message.replace('%', '%25');
		message = message.replaceAll('+', '%2B');
		message = message.replace('#', '%23');
		message = message.replace('&', '%26');
		message = message.replace('?', '%3F');
		message = message.replace('\'', '`');

		if (message.length === 0) {
			return;
		}

		await useHttp({ message }).post('/chat/send');
	}

	async function loadMessages () {
		if (messages.value.length) {
			return;
		}

		try {
			messages.value = await useHttp().get('/chat/last');
		} catch (error) {
			console.error(error);
		}

		clearUnread();
	}

	function clear () {
		setMessages([]);
		clearUnread();
	}

	function addMessage (message) {
		message = { ...message, text: reformatMessage(message['text']) };

		messages.value.push(message);
		unread.value += 1;
	}

	function setMessages (messages) {
		messages.value = messages.map((message) => ({ ...message, text: reformatMessage(message['text']) }));
	}

	function clearUnread () {
		unread.value = 0;
	}

	function incrementUnread () {
		unread.value += 1;
	}

	return { messages, unread, sortedMessages, sendMessage, loadMessages, clear, addMessage, setMessages, clearUnread, incrementUnread };
}