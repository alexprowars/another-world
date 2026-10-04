<?php

return [
	'max_slots' => 22,

	'healer' => [
		'move_stat_price' => 15,
		'dispel_price' => 1,
		'leave_tribe_price' => 50,
	],

	'barn' => [
		'tool_type' => 18,
		'resource_types' => [19, 20],
	],

	'church' => [
		'marriage_price' => 350,
		'divorce_price' => 50,
	],

	'postoffice' => [
		'send_cost' => 1,
	],

	'gamblinghouse' => [
		'dice_stakes' => [10, 25, 50, 100, 200],
		'ticket_price' => 1,
		'ticket_limit' => 500,
	],
];
