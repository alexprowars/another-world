import { onBeforeUnmount, onMounted } from 'vue';

const newbieMessages = [
	'Здравствуйте, вы попали в славный мир Another World. Я помогу вам освоиться здесь.',
	'Прежде всего вы должны распределить свободные параметры, такие как: сила, удача, ловкость и выносливость. Разум на нулевом уровне качать нет смысла. Чтобы увеличить параметры надо нажать на "Есть свободные статы!", и в появившемся окне, сделать выбор статов.',
	'Не спешите выбирать между энергией и выносливостью, т.к. этот выбор определит вашу будущую раскачку под мага или воина соответственно. Выбор можно сделать в любой момент.',
	'Теперь следует приобрести тренировочный нож за 2 золота, он значительно увеличит наносимый вами урон. Чтобы попасть в любое здание вначале надо нажать  кнопку "Город", расположенную в верхнем фрэйме, затем выбрать здание, в нашем случае "Магазин", он находится на "Торговой площади".',
	'Возращаемся на Арену и начинаем свой путь к первому уровню! Опыт игроки набирают в поединках, на нулевом уровне доступны физические поединки (1х1) и бои с вашим клоном в тренировочной комнате. В бою вы можете сделать 1 удар и поставить 1 блок (без щита).',
	'Удачи вам, на этом не лёгком пути к славе и победам.',
];

export default function useNewbieMessages(user, chat) {
	let messageIndex = 0;
	let timer;

	onMounted(() => {
		if (user.value?.exp === 0) {
			timer = setTimeout(sendMessage, 30000);
		}
	});

	onBeforeUnmount(() => clearTimeout(timer));

	function sendMessage() {
		const currentUser = user.value;

		if (!currentUser || messageIndex >= newbieMessages.length) {
			return;
		}

		chat.addMessage({
			id: 'newbie-' + messageIndex,
			sender: null,
			system_name: 'Комментатор',
			recipients: [{ id: currentUser.id, name: currentUser.name }],
			body: newbieMessages[messageIndex],
			kind: 'system',
			visibility: 'private',
			created_at: new Date().toISOString(),
			redirect: null,
		});

		messageIndex += 1;

		if (messageIndex < newbieMessages.length) {
			timer = setTimeout(sendMessage, 45000);
		}
	}
}
