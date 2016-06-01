<?

/**
 * @var array $iteminfo
 * @var array $enemy
 * @var array $object
 */

switch ($iteminfo['name'])
{
	case 'addhp50':
	case 'addhp100':
	case 'addhp150':
	case 'addhp200':
	case 'addhp250':
	case 'addhp300':

		include(__DIR__."/spell/hp.php");

		break;

	case 'addenergy25':
	case 'addenergy50':
	case 'addenergy100':

		include(__DIR__."/spell/energy.php");

		break;

	case 'fireball30':
	case 'fireball40':
	case 'fireball50':
	case 'fireball65':

		include(__DIR__."/spell/fireball.php");

		break;

	case 'showstorm20':
	case 'showstorm30':
	case 'showstorm40':

		include(__DIR__."/spell/showstorm.php");

		break;

	case 'razdet':

		include(__DIR__."/spell/strip.php");

		break;

	case 'invisible':

		include(__DIR__."/spell/invisible.php");

		break;
}

/*

// ----- # Свиток нападения # ----- //
if ($iteminfo['name'] == "attack") {
	if (!$stat['battle']) include("includes/magic/attack.php");
	else $nms="Вы не можете использовать заклинание, т.к. Вы находитесь в поединке!";
}

// ----- # Свиток нападения # ----- //
if ($iteminfo['name'] == "blood_attack") {
	if (!$stat['battle']) include("includes/magic/blood_attack.php");
	else $nms="Вы не можете использовать заклинание, т.к. Вы находитесь в поединке!";
}

// ----- # Свиток нападения # ----- //
if ($iteminfo['name'] == "mirror") {
	if (!$stat['battle']) include("includes/magic/mirror.php");
	else $nms="Вы не можете использовать заклинание, т.к. Вы находитесь в поединке!";
}
// ----- # осиновый кол # ----- //
if ($iteminfo['name'] == "osinkol") {
	if (!$stat['battle']) include("includes/magic/osinkol.php");
	else $nms="Вы не можете использовать заклинание, т.к. Вы находитесь в поединке!";
}
// ----- # Зелье маны # ----- //
if ($iteminfo['name'] == "elikenergy") {
	if (!$stat['battle']) include("includes/magic/elikenergy.php");
	else $nms="Вы не можете использовать зелье, т.к. Вы находитесь в поединке!";
}
// ----- # Свитки восстановления активности # ----- //
if ($iteminfo['name'] == "addustal") {
	if (!$stat['battle']) include("includes/magic/addustal.php");
	else $nms="Вы не можете использовать заклинание, т.к. Вы находитесь в поединке!";
}
// ----- # Свитки ледяного удара # ----- //
if ($iteminfo['name'] == "lighting_bolt40" || $iteminfo['name'] == "lighting_bolt50" || $iteminfo['name'] == "lighting_bolt60" || $iteminfo['name'] == "lighting_bolt70") {
	if ($stat['battle'] == $chl['battle']) include("includes/magic/lighting_bolt.php");
        else $nms="Для использования нужно находиться в одном бою с персонажем!";
}
// ----- # Свитки удара магии # ----- //
if ($iteminfo['name'] == "magichand") {
	if ($stat['battle'] == $chl['battle']) include("includes/magic/magichand.php");
        else $nms="Для использования нужно находиться в одном бою с персонажем!";
}
// ----- # Вампиризм # ----- //
if ($iteminfo['name'] == "vampire") {
	if ($stat['battle'] == $chl['battle']) include("includes/magic/vampire.php");
        else $nms="Для использования нужно находиться в одном бою с персонажем!";
}
// ----- # Свиток доноса # ----- //
if ($iteminfo['name'] == "chains") {
	if (!$stat['battle']) include("includes/magic/chains.php");
	else $nms="Вы не можете использовать заклинание, т.к. Вы находитесь в поединке!";
}

// ----- # Свиток исцеления от травм # ----- //
if ($iteminfo['name'] == "healing2") {
	if (!$stat['battle']) include("includes/magic/healing1.php");
	else $nms="Вы не можете использовать заклинание, т.к. Вы находитесь в поединке!";
}
// ----- # Свиток исцеления от травм # ----- //
if ($iteminfo['name'] == "healing3") {
	if (!$stat['battle']) include("includes/magic/healing2.php");
	else $nms="Вы не можете использовать заклинание, т.к. Вы находитесь в поединке!";
}
// ----- # Свиток исцеления от травм # ----- //
if ($iteminfo['name'] == "healing1") {
	if (!$stat['battle']) include("includes/magic/healing3.php");
	else $nms="Вы не можете использовать заклинание, т.к. Вы находитесь в поединке!";
}
// ----- # Свиток исцеления от травм # ----- //
if ($iteminfo['name'] == "healing_m") {
	if (!$stat['battle']) include("includes/magic/healing_m.php");
	else $nms="Вы не можете использовать заклинание, т.к. Вы находитесь в поединке!";
}
// ----- # Свиток защиты от магии # ----- //
if ($iteminfo['name'] == "magearmor") {
	include("includes/magic/magearmor.php");
}
// ----- # Свиток запрета на общение # ----- //
if ($iteminfo['name'] == "mol15" || $iteminfo['name'] == "mol30" || $iteminfo['name'] == "mol60") {
	if (!$stat['battle']) include("includes/magic/mol.php");
	else $nms="Вы не можете использовать заклинание, т.к. Вы находитесь в поединке!";
}
// ----- # Свиток защиты от вампиров # ----- //
if ($iteminfo['name'] == "chesnok" || $iteminfo['name'] == "chesnok2") {
	if (!$stat['battle']) include("includes/magic/chesnok.php");
	else $nms="Вы не можете использовать заклинание, т.к. Вы находитесь в поединке!";
}
// ----- # Свиток Боевая Ярость # ----- //
if ($iteminfo['name'] == "jarost") {
	if (!$stat['battle']) include("includes/magic/jarost.php");
	else $nms="Вы не можете использовать заклинание, т.к. Вы находитесь в поединке!";
}
// ----- # Свиток защиты от нападение # ----- //
if ($iteminfo['name'] == "immun") {
	if (!$stat['battle']) include("includes/magic/immun.php");
	else $nms="Вы не можете использовать заклинание, т.к. Вы находитесь в поединке!";
}
// ----- # Свиток защиты от нападение 1 # ----- //
if ($iteminfo['name'] == "immun1") {
	if (!$stat['battle']) include("includes/magic/immun1.php");
	else $nms="Вы не можете использовать заклинание, т.к. Вы находитесь в поединке!";
}
// ----- # Свиток защиты от нападение 1 # ----- //
if ($iteminfo['name'] == "voskr") {
	if (!$stat['battle']) include("includes/magic/anti_otravl.php");
	else $nms="Вы не можете использовать заклинание, т.к. Вы находитесь в поединке!";
}
// ----- # Свиток защиты от нападение 1 # ----- //
if ($iteminfo['name'] == "perevod") {
	if ($stat['battle']) include("includes/magic/perevod.php");
	else $nms="Вы не можете использовать заклинание, т.к. Вы не находитесь в поединке!";
}

include("includes/magic/elik/magics.php");
include("includes/magic/rune/magics.php");
*/