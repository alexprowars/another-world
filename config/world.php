<?php

use App\Engine\Locations;

return [
	'locations' => [
		'royal-street' => [
			'controller' => Locations\StreetController::class,
			'actions' => [],
		],
		'market-square' => [
			'controller' => Locations\StreetController::class,
			'actions' => [],
		],
		'park' => [
			'controller' => Locations\StreetController::class,
			'actions' => [],
		],
		'arena-entrance' => [
			'controller' => Locations\StreetController::class,
			'actions' => [],
		],
		'industrial-street' => [
			'controller' => Locations\StreetController::class,
			'actions' => [],
		],
		'arena' => [
			'controller' => Locations\ArenaLobbyController::class,
			'actions' => [],
		],
		'training-arena' => [
			'controller' => Locations\TrainingArenaController::class,
			'actions' => [
				'fights' => 'fight',
			],
		],
		'shop' => [
			'controller' => Locations\ShopController::class,
			'pages' => [
				'sell' => 'selling',
			],
			'actions' => [
				'purchases' => 'buy',
				'sales' => 'sell',
			],
		],
		'boutique' => [
			'controller' => Locations\BoutiqueController::class,
			'pages' => [
				'sell' => 'selling',
			],
			'actions' => [
				'purchases' => 'buy',
				'sales' => 'sell',
			],
		],
		'magic-shop' => [
			'controller' => Locations\MagicShopController::class,
			'actions' => [
				'purchases' => 'buy',
			],
		],
		'gift-shop' => [
			'controller' => Locations\GiftShopController::class,
			'actions' => [
				'purchases' => 'buy',
				'gifts' => 'gift',
			],
		],
		'hospital' => [
			'controller' => Locations\HospitalController::class,
			'actions' => [
				'healing' => 'heal',
				'injuries/healing' => 'injury',
			],
		],
		'academy' => [
			'controller' => Locations\AcademyController::class,
			'actions' => [
				'training' => 'learn',
			],
		],
		'works' => [
			'controller' => Locations\WorksController::class,
			'actions' => [
				'jobs' => 'work',
			],
		],
		'bank' => [
			'controller' => Locations\BankController::class,
			'actions' => [
				'donations' => 'donate',
				'exchange' => 'exchange',
			],
		],
		'market' => [
			'controller' => Locations\MarketController::class,
			'actions' => [
				'sales' => 'sell',
				'purchases' => 'buy',
				'withdrawals' => 'withdraw',
			],
		],
		'pawn-shop' => [
			'controller' => Locations\PawnShopController::class,
			'actions' => [
				'deposits' => 'deposit',
				'withdrawals' => 'withdraw',
			],
		],
		'barn' => [
			'controller' => Locations\BarnController::class,
			'actions' => [
				'sales' => 'sell',
				'purchases' => 'buy',
			],
		],
		'smithy' => [
			'controller' => Locations\SmithyController::class,
			'actions' => [
				'repairs' => 'repair',
				'engravings' => 'engrave',
				'upgrades' => 'upgrade',
				'cuts' => 'cut',
				'inserts' => 'insert',
			],
		],
		'administration' => [
			'controller' => Locations\AdministrationController::class,
			'actions' => [
				'requests' => 'submit',
				'requests/withdraw' => 'withdraw',
				'images/purchases' => 'buy_image',
			],
		],
		'healer' => [
			'controller' => Locations\HealerController::class,
			'actions' => [
				'stats' => 'move_stat',
				'tribe/leave' => 'leave_tribe',
				'dispel' => 'dispel',
				'crafts' => 'craft',
			],
		],
		'gambling-house' => [
			'controller' => Locations\GamblingHouseController::class,
			'actions' => [
				'dice' => 'dice',
				'lottery/tickets' => 'ticket',
			],
		],
		'church' => [
			'controller' => Locations\ChurchController::class,
			'actions' => [
				'marriages' => 'marry',
				'divorces' => 'divorce',
			],
		],
		'post-office' => [
			'controller' => Locations\PostOfficeController::class,
			'actions' => [
				'letters' => 'letter',
				'transfers/items' => 'item',
				'transfers/gold' => 'gold',
			],
		],
		'prison' => [
			'controller' => Locations\PrisonController::class,
			'actions' => [],
		],
		'vault' => [
			'controller' => Locations\VaultController::class,
			'actions' => [
				'healing' => 'heal',
				'digging' => 'dig',
				'digging/cancel' => 'unwork',
			],
		],
	],
	'cities' => [
		'valmir' => [
			'name' => 'Вальмир',
			'image_path' => '/assets/images/world/city/',
			'locations' => [
				'administration' => [
					'name' => 'Замок святой инквизиции',
					'exit' => 'valmir.royal-street',
					'connections' => [
						'valmir.royal-street',
					],
				],
				'boutique' => [
					'shop_id' => 2,
					'name' => 'Бутик',
					'exit' => 'valmir.royal-street',
					'connections' => [
						'valmir.royal-street',
					],
				],
				'hospital' => [
					'name' => 'Больница',
					'exit' => 'valmir.royal-street',
					'connections' => [
						'valmir.royal-street',
					],
				],
				'church' => [
					'name' => 'Церковь',
					'exit' => 'valmir.royal-street',
					'connections' => [
						'valmir.royal-street',
					],
				],
				'healer' => [
					'name' => 'Домик Знахаря',
					'exit' => 'valmir.royal-street',
					'connections' => [
						'valmir.royal-street',
					],
				],
				'royal-street' => [
					'map' => [
						'image' => 'street1.jpg',
						'alt' => 'Карта Королевской улицы',
						'width' => 1200,
						'height' => 654,
					],
					'places' => [
						[
							'title' => 'Замок святой инквизиции',
							'description' => 'Обитель инквизиции',
							'number' => 1,
							'x' => 99,
							'y' => 53,
							'width' => 346,
							'height' => 346,
							'location' => 'valmir.administration',
						],
						[
							'title' => 'Бутик',
							'description' => 'Особые товары',
							'number' => 2,
							'x' => 636,
							'y' => 226,
							'width' => 155,
							'height' => 180,
							'location' => 'valmir.boutique',
						],
						[
							'title' => 'Больница',
							'description' => 'Восстановление здоровья',
							'number' => 3,
							'x' => 470,
							'y' => 226,
							'width' => 159,
							'height' => 180,
							'location' => 'valmir.hospital',
						],
						[
							'title' => 'Церковь',
							'description' => 'Заключение брака и развод',
							'number' => 4,
							'x' => 824,
							'y' => 12,
							'width' => 275,
							'height' => 400,
							'location' => 'valmir.church',
						],
						[
							'title' => 'Домик Знахаря',
							'description' => 'Перераспределение характеристик и алхимия',
							'number' => 5,
							'x' => 78,
							'y' => 378,
							'width' => 209,
							'height' => 180,
							'location' => 'valmir.healer',
						],
					],
					'exits' => [
						[
							'title' => 'Торговая площадь',
							'number' => '→',
							'x' => 1134,
							'y' => 410,
							'width' => 66,
							'height' => 120,
							'direction' => 'right',
							'location' => 'valmir.market-square',
						],
					],
					'name' => 'Королевская улица',
					'description' => 'Колокольный звон, величественные стены и важные дела. Здесь бьётся сердце королевского города.',
					'places_title' => 'Места на улице',
				],
				'bank' => [
					'name' => 'Банк',
					'exit' => 'valmir.market-square',
					'connections' => [
						'valmir.market-square',
					],
				],
				'shop' => [
					'shop_id' => 1,
					'name' => 'Магазин',
					'exit' => 'valmir.market-square',
					'connections' => [
						'valmir.market-square',
					],
				],
				'smithy' => [
					'name' => 'Кузница',
					'exit' => 'valmir.market-square',
					'connections' => [
						'valmir.market-square',
					],
				],
				'market' => [
					'name' => 'Рынок',
					'exit' => 'valmir.market-square',
					'connections' => [
						'valmir.market-square',
					],
				],
				'pawn-shop' => [
					'name' => 'Ломбард',
					'exit' => 'valmir.market-square',
					'connections' => [
						'valmir.market-square',
					],
				],
				'market-square' => [
					'map' => [
						'image' => 'street2.jpg',
						'alt' => 'Карта торговой площади',
						'width' => 1200,
						'height' => 656,
					],
					'places' => [
						[
							'title' => 'Арена',
							'description' => 'Поединки и тренировки',
							'number' => 1,
							'x' => 39,
							'y' => 53,
							'width' => 583,
							'height' => 350,
							'location' => 'valmir.arena-entrance',
						],
						[
							'title' => 'Банк',
							'description' => 'Счета и сбережения',
							'number' => 2,
							'x' => 689,
							'y' => 133,
							'width' => 251,
							'height' => 287,
							'location' => 'valmir.bank',
						],
						[
							'title' => 'Магазин',
							'description' => 'Оружие и экипировка',
							'number' => 3,
							'x' => 986,
							'y' => 258,
							'width' => 205,
							'height' => 184,
							'location' => 'valmir.shop',
						],
						[
							'title' => 'Кузница',
							'description' => 'Ремонт и улучшение вещей',
							'number' => 4,
							'x' => 555,
							'y' => 449,
							'width' => 240,
							'height' => 184,
							'location' => 'valmir.smithy',
						],
						[
							'title' => 'Рынок',
							'description' => 'Торговля между игроками',
							'number' => 5,
							'x' => 57,
							'y' => 414,
							'width' => 184,
							'height' => 149,
							'location' => 'valmir.market',
						],
						[
							'title' => 'Ломбард',
							'description' => 'Залог и выкуп вещей',
							'number' => 6,
							'x' => 931,
							'y' => 472,
							'width' => 252,
							'height' => 159,
							'location' => 'valmir.pawn-shop',
						],
					],
					'exits' => [
						[
							'title' => 'Королевская улица',
							'number' => '←',
							'x' => 0,
							'y' => 421,
							'width' => 60,
							'height' => 120,
							'direction' => 'left',
							'location' => 'valmir.royal-street',
						],
						[
							'title' => 'Парк',
							'number' => '→',
							'x' => 1134,
							'y' => 421,
							'width' => 66,
							'height' => 120,
							'direction' => 'right',
							'location' => 'valmir.park',
						],
					],
					'name' => 'Торговая площадь',
					'description' => 'Звон монет, голоса торговцев и стук кузнечного молота. Здесь город живёт своей жизнью.',
					'places_title' => 'Места на площади',
				],
				'gambling-house' => [
					'name' => 'Игорный дом',
					'exit' => 'valmir.park',
					'connections' => [
						'valmir.park',
					],
				],
				'magic-shop' => [
					'shop_id' => 3,
					'name' => 'Башня магов',
					'exit' => 'valmir.park',
					'connections' => [
						'valmir.park',
					],
				],
				'works' => [
					'name' => 'Центр занятости',
					'exit' => 'valmir.park',
					'connections' => [
						'valmir.park',
					],
				],
				'gift-shop' => [
					'shop_id' => 4,
					'name' => 'Сувениры',
					'exit' => 'valmir.park',
					'connections' => [
						'valmir.park',
					],
				],
				'post-office' => [
					'name' => 'Почта',
					'exit' => 'valmir.park',
					'connections' => [
						'valmir.park',
					],
				],
				'park' => [
					'map' => [
						'image' => 'street3.jpg',
						'alt' => 'Карта парка',
						'width' => 1200,
						'height' => 654,
					],
					'places' => [
						[
							'title' => 'Игорный дом',
							'description' => 'Кости и городская лотерея',
							'number' => 5,
							'x' => 78,
							'y' => 10,
							'width' => 398,
							'height' => 380,
							'location' => 'valmir.gambling-house',
						],
						[
							'title' => 'Башня магов',
							'description' => 'Магия и заклинания',
							'number' => 1,
							'x' => 625,
							'y' => 14,
							'width' => 102,
							'height' => 201,
							'location' => 'valmir.magic-shop',
						],
						[
							'title' => 'Центр занятости',
							'description' => 'Работа и заработок',
							'number' => 2,
							'x' => 569,
							'y' => 321,
							'width' => 205,
							'height' => 155,
							'location' => 'valmir.works',
						],
						[
							'title' => 'Сувениры',
							'description' => 'Подарки для друзей',
							'number' => 3,
							'x' => 777,
							'y' => 371,
							'width' => 198,
							'height' => 113,
							'location' => 'valmir.gift-shop',
						],
						[
							'title' => 'Почта',
							'description' => 'Письма и городские весточки',
							'number' => 4,
							'x' => 31,
							'y' => 367,
							'width' => 190,
							'height' => 181,
							'location' => 'valmir.post-office',
						],
					],
					'exits' => [
						[
							'title' => 'Торговая площадь',
							'number' => '←',
							'x' => 0,
							'y' => 448,
							'width' => 60,
							'height' => 120,
							'direction' => 'left',
							'location' => 'valmir.market-square',
						],
						[
							'title' => 'Промышленная зона',
							'number' => '→',
							'x' => 1134,
							'y' => 448,
							'width' => 66,
							'height' => 120,
							'direction' => 'right',
							'location' => 'valmir.industrial-street',
						],
					],
					'name' => 'Парк',
					'description' => 'Тихие аллеи, резные скамейки и свежий воздух. Место для отдыха и новых встреч на городских дорожках.',
					'places_title' => 'Места в парке',
				],
				'arena' => [
					'name' => 'Арена',
					'exit' => 'valmir.arena-entrance',
					'connections' => [
						'valmir.arena-entrance',
						'valmir.training-arena',
						'valmir.hospital',
					],
				],
				'academy' => [
					'name' => 'Академия',
					'exit' => 'valmir.arena-entrance',
					'connections' => [
						'valmir.arena-entrance',
					],
				],
				'arena-entrance' => [
					'map' => [
						'image' => 'street4.jpg',
						'alt' => 'Прихожая арены',
						'width' => 1200,
						'height' => 495,
					],
					'places' => [
						[
							'title' => 'Вход на арену',
							'description' => 'Поединки и тренировки',
							'number' => 1,
							'x' => 516,
							'y' => 0,
							'width' => 184,
							'height' => 317,
							'location' => 'valmir.arena',
						],
						[
							'title' => 'Академия',
							'description' => 'Обучение профессиям',
							'number' => 2,
							'x' => 92,
							'y' => 114,
							'width' => 184,
							'height' => 261,
							'location' => 'valmir.academy',
						],
					],
					'exits' => [
						[
							'title' => 'Торговая площадь',
							'number' => '←',
							'x' => 0,
							'y' => 234,
							'width' => 58,
							'height' => 129,
							'direction' => 'left',
							'subtitle' => 'Выход в город',
							'location' => 'valmir.market-square',
						],
					],
					'name' => 'Прихожая',
					'description' => 'За каменными сводами ждёт арена. Выберите путь к поединкам или отправляйтесь в академию.',
					'places_title' => 'Места на арене',
				],
				'prison' => [
					'name' => 'Тюрьма',
					'exit' => 'valmir.industrial-street',
					'connections' => [
						'valmir.industrial-street',
					],
				],
				'barn' => [
					'name' => 'Амбар',
					'exit' => 'valmir.industrial-street',
					'connections' => [
						'valmir.industrial-street',
					],
				],
				'industrial-street' => [
					'map' => [
						'image' => 'street5.jpg',
						'alt' => 'Карта промышленной улицы',
						'width' => 1200,
						'height' => 654,
					],
					'places' => [
						[
							'title' => 'Шахта',
							'description' => 'Добыча ресурсов',
							'number' => 1,
							'x' => 519,
							'y' => 173,
							'width' => 163,
							'height' => 124,
							'location' => 'valmir.vault.200',
						],
						[
							'title' => 'Тюрьма',
							'description' => 'Место заключения',
							'number' => 2,
							'x' => 770,
							'y' => 145,
							'width' => 343,
							'height' => 251,
							'location' => 'valmir.prison',
						],
						[
							'title' => 'Амбар',
							'description' => 'Сдача ресурсов и инструменты',
							'number' => 3,
							'x' => 410,
							'y' => 357,
							'width' => 276,
							'height' => 187,
							'location' => 'valmir.barn',
						],
					],
					'exits' => [
						[
							'title' => 'Парк',
							'number' => '←',
							'x' => 0,
							'y' => 304,
							'width' => 71,
							'height' => 127,
							'direction' => 'left',
							'location' => 'valmir.park',
						],
					],
					'name' => 'Промышленная улица',
					'description' => 'Грохот вагонеток, угольная пыль и чёрные горы на горизонте. В промышленном квартале работа не стихает.',
					'places_title' => 'Места в квартале',
				],
				'training-arena' => [
					'name' => 'Тренировочный зал',
					'exit' => 'valmir.arena-entrance',
					'connections' => [
						'valmir.arena-entrance',
						'valmir.arena',
					],
				],
				'vault' => [
					'name' => 'Подземелье',
					'entrance_id' => 200,
					'exit' => 'valmir.industrial-street',
					'connections' => [
						'valmir.industrial-street',
					],
				],
			],
		],
	],
];
