<?php

// Боевые приёмы
$priem_full = [
	1 => ['type' => 1, 'level' => 0, 'block' => 0, 'hit' => 0, 'crit' => 3, 'magic' => 0, 'parry' => 0, 'damage' => 5, 'wait' => 1, 'time' => 3, 'name' => 'Аура исцеления', 'about' => 'В течении 3 ходов вы востанавливаете по 5 HP за ход'],
	5 => ['type' => 1, 'level' => 0, 'block' => 0, 'hit' => 0, 'crit' => 0, 'magic' => 5, 'parry' => 0, 'damage' => 5, 'wait' => 1, 'time' => 1, 'name' => 'Испепеление', 'about' => 'Наносит цели дополнительно 50 урона огнём'],
	6 => ['type' => 1, 'level' => 0, 'block' => 0, 'hit' => 0, 'crit' => 4, 'magic' => 0, 'parry' => 0, 'damage' => 4, 'wait' => 1, 'time' => 3, 'name' => 'Ярость', 'about' => 'На 3 хода ваш урон увеличен на 5'],
	7 => ['type' => 1, 'level' => 1, 'block' => 0, 'hit' => 0, 'crit' => 0, 'magic' => 0, 'parry' => 0, 'damage' => 1, 'wait' => 2, 'time' => 1, 'name' => 'Усиленный удар', 'about' => 'Следующий ваш удар нанесёт на 3 урона больше'],
	8 => ['type' => 1, 'level' => 1, 'block' => 0, 'hit' => 0, 'crit' => 0, 'magic' => 0, 'parry' => 0, 'damage' => 1, 'wait' => 2, 'time' => 1, 'name' => 'Блок', 'about' => 'Следующий удар по вам нанесет на 3 урона меньше'],
	9 => ['type' => 1, 'level' => 1, 'block' => 0, 'hit' => 0, 'crit' => 0, 'magic' => 0, 'parry' => 0, 'damage' => 1, 'wait' => 1, 'time' => 1, 'name' => 'Вытереть кровоподтек', 'about' => 'Восстановить 3 HP'],
	10 => ['type' => 1, 'level' => 2, 'block' => 0, 'hit' => 0, 'crit' => 0, 'magic' => 0, 'parry' => 0, 'damage' => 2, 'wait' => 2, 'time' => 1, 'name' => 'Усиленный удар II', 'about' => 'Следующий ваш удар нанесёт на 5 урона больше'],
	11 => ['type' => 1, 'level' => 2, 'block' => 0, 'hit' => 0, 'crit' => 0, 'magic' => 0, 'parry' => 0, 'damage' => 2, 'wait' => 2, 'time' => 1, 'name' => 'Блок II', 'about' => 'Следующий удар по вам нанесет на 5 урона меньше'],
	12 => ['type' => 1, 'level' => 2, 'block' => 0, 'hit' => 0, 'crit' => 0, 'magic' => 0, 'parry' => 0, 'damage' => 2, 'wait' => 1, 'time' => 1, 'name' => 'Вытереть кровоподтек II', 'about' => 'Восстановить 5 HP'],
	13 => ['type' => 1, 'level' => 3, 'block' => 0, 'hit' => 0, 'crit' => 0, 'magic' => 0, 'parry' => 0, 'damage' => 3, 'wait' => 2, 'time' => 1, 'name' => 'Точный удар', 'about' => 'Следующий ваш удар наносит на 10 урона больше'],
	14 => ['type' => 1, 'level' => 3, 'block' => 0, 'hit' => 0, 'crit' => 0, 'magic' => 0, 'parry' => 0, 'damage' => 4, 'wait' => 2, 'time' => 1, 'name' => 'Сильный Удар', 'about' => 'Следующий ваш удар наносит на 20 урона больше'],
	15 => ['type' => 1, 'level' => 3, 'block' => 0, 'hit' => 0, 'crit' => 0, 'magic' => 0, 'parry' => 0, 'damage' => 3, 'wait' => 1, 'time' => 1, 'name' => 'Вытереть Пот', 'about' => 'Восстанавливает 10 HP'],
	16 => ['type' => 1, 'level' => 4, 'block' => 0, 'hit' => 0, 'crit' => 0, 'magic' => 0, 'parry' => 0, 'damage' => 4, 'wait' => 2, 'time' => 1, 'name' => 'Точный удар II', 'about' => 'Следующий ваш удар нанесёт на 15 урона больше'],
	17 => ['type' => 1, 'level' => 4, 'block' => 0, 'hit' => 0, 'crit' => 0, 'magic' => 0, 'parry' => 0, 'damage' => 5, 'wait' => 2, 'time' => 1, 'name' => 'Сильный Удар II', 'about' => 'Следующий ваш удар нанесёт на 25 урона больше'],
	18 => ['type' => 1, 'level' => 4, 'block' => 0, 'hit' => 0, 'crit' => 0, 'magic' => 0, 'parry' => 1, 'damage' => 3, 'wait' => 1, 'time' => 1, 'name' => 'Вытереть Пот II', 'about' => 'Восстанавливает 20 HP'],
	19 => ['type' => 1, 'level' => 4, 'block' => 3, 'hit' => 0, 'crit' => 0, 'magic' => 0, 'parry' => 0, 'damage' => 1, 'wait' => 1, 'time' => 1, 'name' => 'Удачный Блок', 'about' => 'Урон по вам уменьшен на 10'],
	4 => ['type' => 1, 'level' => 4, 'block' => 1, 'hit' => 0, 'crit' => 0, 'magic' => 0, 'parry' => 3, 'damage' => 1, 'wait' => 2, 'time' => 1, 'name' => 'Промах', 'about' => 'Вы увернетесь от следующего удара по вам'],
	3 => ['type' => 1, 'level' => 4, 'block' => 0, 'hit' => 0, 'crit' => 2, 'magic' => 0, 'parry' => 0, 'damage' => 3, 'wait' => 2, 'time' => 1, 'name' => 'Удар по больному месту', 'about' => 'Следующий ваш удар будет критическим'],
	20 => ['type' => 1, 'level' => 5, 'block' => 0, 'hit' => 0, 'crit' => 0, 'magic' => 0, 'parry' => 0, 'damage' => 6, 'wait' => 2, 'time' => 1, 'name' => 'Точный удар III', 'about' => 'Следующий ваш удар нанесёт на 20 урона больше'],
	21 => ['type' => 1, 'level' => 5, 'block' => 0, 'hit' => 0, 'crit' => 0, 'magic' => 0, 'parry' => 0, 'damage' => 7, 'wait' => 2, 'time' => 1, 'name' => 'Сильныйй Удар III', 'about' => 'Следующий ваш удар нанесёт на 30 урона больше'],
	22 => ['type' => 1, 'level' => 5, 'block' => 0, 'hit' => 0, 'crit' => 0, 'magic' => 0, 'parry' => 1, 'damage' => 3, 'wait' => 1, 'time' => 1, 'name' => 'Вытереть Пот III', 'about' => 'Восстанавливает 30 HP'],
	2 => ['type' => 1, 'level' => 5, 'block' => 0, 'hit' => 0, 'crit' => 0, 'magic' => 0, 'parry' => 0, 'damage' => 11, 'wait' => 1, 'time' => 1, 'name' => 'Подлый удар', 'about' => 'Текущий удар увеличивается на 35 ед.'],
];

// Базовый опыт
$base_exp = [
	0 => 5,
	1 => 10,
	2 => 20,
	3 => 30,
	4 => 60,
	5 => 120,
	6 => 180,
	7 => 300,
	8 => 600,
	9 => 1200,
	10 => 2400,
	11 => 3600,
	12 => 5200,
];
