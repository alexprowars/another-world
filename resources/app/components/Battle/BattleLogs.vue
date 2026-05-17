<template>
	<div id="logsContent" class="mt-4 text-sm">
		<div v-for="items in grouped" class="flex flex-col gap-2">
			<div v-for="item in items">
				<b v-if="item.my" class="date2">{{ $formatDate(item['date'], 'DD MMM HH:mm:ss') }}</b>
				<b v-else class="date1">{{ $formatDate(item['date'], 'DD MMM HH:mm:ss') }}</b>

				<span v-if="item.user && item.enemy" class="inline-flex" v-html="hitSchema(item.side, item.hits, item.enemy_blocks)"></span>
				<span v-html="renderComment($formatDate(item['date'], 'DD MMM HH:mm:ss'), item.user, item.side, item.hits, item.damage, item.enemy, item.comment)"></span>
			</div>
			<hr>
		</div>
	</div>
</template>

<script setup>
	import { computed } from 'vue';
	import _groupBy from 'lodash-es/groupBy';

	const props = defineProps({
		logs: {
			type: Array,
			default: () => [],
		},
	});

	const grouped = computed(() => {
		return Object.values(_groupBy(props.logs, item => item.round)).reverse();
	});

	const imgHint = {
		1: 'Голова',
		2: 'Грудь',
		3: 'Живот',
		4: 'Пах',
		5: 'Ноги',
	};

	function hitSchema(side = 0, kick = [], block = []) {
		let result = '';

		for (let i = 1; i < 6; i++) {
			const blocked = block?.[0] === i || block?.[1] === i || block?.[2] === i;
			const kicked = kick?.[0] === i || kick?.[1] === i;
			const prefix = side === 0 ? 3 : 4;
			const suffix = blocked ? (kicked ? 3 : 1) : (kicked ? 2 : 0);

			result += `<img src="/assets/images/battle/log/${prefix}${suffix}.gif" title="${imgHint[i]}">`;
		}

		return result;
	}

	function renderComment(dates, attacker, side, hitType, attackerDamage, defender, cm) {
		let showStr = '';
		let showStr2 = '';
		let showStr5 = '';
		const statKick = String(hitType).split(',');

		let finalText = '';

		switch (parseInt(side)) {
			case 0:
				attacker = `<b style="color: #CFA87A">${attacker}</b>`;
				defender = `<b style="color: #142F98">${defender}</b>`;
				finalText = '<b style="color: #CFA87A">';
				break;
			case 1:
				attacker = `<b style="color: #142F98">${attacker}</b>`;
				defender = `<b style="color: #CFA87A">${defender}</b>`;
				finalText = '<b style="color: #142F98">';
				break;
		}

		showStr = hitText(statKick[0], false);
		showStr2 = hitText(statKick[1], true);
		showStr5 = hitText(statKick[1], false);

		const damage = cm >= 21 && cm <= 30 ? `<b style="color: red">-${attackerDamage}</b>` : `<b>-${attackerDamage}</b>`;
		const comments = [];

		comments[1] = `${attacker} ударил ${showStr}${showStr2}, хотя ${defender} пытался уйти от удара: ${damage}`;
		comments[2] = `${attacker} саданул точный удар ${showStr}${showStr2}, несмотря на то, что наглый ${defender} хотел уйти от удара: ${damage}`;
		comments[3] = `${attacker} влепил мощный удар ${showStr}${showStr2}, несмотря на все усилия ${defender} избежать этого: ${damage}`;
		comments[4] = `${defender} явно недооценил силы противника... Как результат: ${attacker} нанёс тяжелейший удар ${showStr}${showStr2}: ${damage}`;
		comments[5] = `Почувствовав нерешительность ${defender}, разъярённый ${attacker} со всего размаху ударил ${showStr}: ${damage}, но тот успел заблокировать удар ${showStr5}`;
		comments[6] = `${defender} совершил роковую ошибку, подойдя вплотную к ${attacker}, на что тот ответил незамедлительным ударом ${showStr}: ${damage}, но ${defender} героически заблокировал удар${showStr5}`;
		comments[7] = `${defender} предпринял неудачную попытку заблокировать удар, за что и поплатился. Яростный ${attacker} нанес точнейший удар ${showStr}: ${damage}, но тот успел заблокировать удар ${showStr5}`;
		comments[8] = `${attacker}, увидев страх в глазах противника, незамедлительно нанёс сокрушительный удар ${showStr5} ${defender}: ${damage}, на что тот ответил блокированием удара ${showStr}`;
		comments[9] = `Самоуверенный ${attacker}, подпрыгнув, нанёс точнейший удар ${showStr5} ${defender}: ${damage}, но от отчаяния противник успел заблокировать удар ${showStr}`;
		comments[10] = `Несмотря на корыстные планы ${defender}, непоколебимый ${attacker}, собравшись, ударил ${showStr5}: ${damage}, но тот успел заблокировать удар ${showStr}`;
		comments[11] = `${attacker} хотел вломить ${showStr}${showStr2}, но ${defender}, не напрягаясь, заблокировал удар`;
		comments[12] = `${attacker} изо всех сил пытался вломить, но ${defender} увел удар ${showStr}${showStr2}`;
		comments[13] = `${attacker} призадумался, благодаря чему сообразительный ${defender}, сменив тактику, заблокировал удар ${showStr}${showStr2}`;
		comments[14] = `Силы потраченные ${attacker} для удара ${showStr}${showStr2} не принесли ему успеха, и как следствие ${defender} заблокировал удар`;
		comments[15] = `${defender} ушел в глухую оборону и как следствие заблокировал удар ${attacker} ${showStr}${showStr2}`;
		comments[16] = `Замысел ${attacker} легко читался и прозорливый ${defender} увел удар ${showStr}${showStr2}`;
		comments[17] = `Силы были равны... Но обороняющийся ${defender} оказался немного хитрее и поэтому заблокировал удар ${attacker} ${showStr}${showStr2}`;
		comments[18] = `Атакующий ${attacker} размахнулся, но всё было сделано настолько медленно, что ${defender} заблокировал удар ${showStr}${showStr2}`;
		comments[19] = `Каким бы грозным не казался ${attacker}, это было не так, самовлюбленный ${defender} увел удар ${showStr}${showStr2}`;
		comments[20] = `${attacker} представил себе, каков вкус победы, но не тут то было, продуманный ${defender} парировал удар ${showStr}${showStr2}`;
		comments[21] = `Видимо, бывают в жизни чудеса... Взбешенный ${attacker} изо всех сил саданул ${defender} ${showStr}${showStr2}: ${damage}`;
		comments[22] = `Разъяренный ${attacker} нанес тяжелейший удар ${showStr}${showStr2} противника, в результате чего ${defender} получил тяжелейшие увечия: ${damage}`;
		comments[23] = `Разъяренный ${attacker} вмочил со всей силы ${showStr}${showStr2} противника, в результате чего у ${defender} аж глаза на лоб вылезли: ${damage}`;
		comments[31] = `${attacker} попытался нанести жестокий удар ${showStr}${showStr2}, но ловкий ${defender} увернулся от удара`;
		comments[32] = `${attacker} размахнулся и ударил ${showStr}${showStr2}, но ловкий ${defender}, показав язык, увернулся от удара`;
		comments[33] = `${attacker} попытался вмочить со всей силы ${showStr}${showStr2}, но ловкий ${defender} обладал даром предвидения и увернулся от удара`;
		comments[41] = `${attacker} размахнулся и с тупой улыбкой на лице вмачил со всей дури ${defender} ${showStr}${showStr2} и ослабленный ${defender} не смог сдержать удар ${showStr} за что и поплатился кровью: ${damage}`;
		comments[42] = `${attacker} размахнулся и набрав чит ударил бедного ${defender} ${showStr}${showStr2} и пробил его второй блок: ${damage}`;
		comments[43] = `${attacker} взял в руки меч и сплесал тектоник что привело ${defender} в замешательство, как результат у ${defender} выпало оружие от удивления и он не смог отбить летящее на него оружие: ${damage}`;
		comments[70] = `${attacker} повержен!`;
		comments[71] = `Часы показывали <B class=date2>${dates}</B>, когда завязался бой!`;
		comments[72] = Number(side) === 0 ? 'Поединок закончен. Ничья' : `Поединок закончен. Победа за ${finalText}</B>`;
		comments[73] = `Поединок закончен по таймауту. Победа за ${finalText}</B>`;
		comments[74] = `${attacker} со словами: "Я изменю исход боя!" вмешался в поединок!`;
		comments[75] = `${attacker}, не без помощи богов, воскресил ${finalText}</B>`;
		comments[76] = `Персонаж ${attacker} ударил в ${defender} магией из своего оружия ${damage}`;
		comments[77] = `Персонаж ${attacker} попытался противостоять магии ${defender} нанеся некоторые повреждения ${damage}`;
		comments[78] = `Персонаж ${attacker} решил слить бой и поэтому пропускает ход`;
		comments[79] = `Персонаж ${attacker} решил схитрить и слить бой, но злобные судьи выкинули его из боя`;
		comments[80] = `Персонаж ${attacker} использовал приём <b>uu</b>`;

		return comments[cm] || '';
	}

	function hitText(value, second) {
		const map = {
			1: second ? ' и в голову' : 'в голову',
			2: second ? ' и в грудь' : 'в грудь',
			3: second ? ' и в живот' : 'в живот',
			4: second ? ' и в пах' : 'в пах',
			5: second ? ' и по ногам' : 'по ногам',
		};

		return map[value] || '';
	}
</script>
