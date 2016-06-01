<?php

function _getText ()
{
	return \App\Lang::getText(func_get_args());
}

function p ($array)
{
	echo '<pre>'; print_r($array); echo '</pre>';
}

function isMobile()
{
	return preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i',$_SERVER['HTTP_USER_AGENT'])||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
}

function allowMobileVersion ()
{
	$ua = strtolower($_SERVER['HTTP_USER_AGENT']);

	$result = true;

	if (!isMobile())
		$result = false;

	if (strpos($ua, 'webkit/5') === false)
		$result = false;

	if (strpos($ua, 'android 2') !== false || strpos($ua, 'android 3') !== false)
		$result = false;

	return $result;
}

function is_email ($email)
{
	if (!$email)
		return false;

	if (preg_match('#^[^\\x00-\\x1f@]+@[^\\x00-\\x1f@]{2,}\.[a-z]{2,}$#iu', $email) == 0)
		return false;

	return true;
}

function convertIp ($ip)
{
	if (!is_numeric($ip))
		return sprintf("%u", ip2long($ip));
	else
		return long2ip($ip);
}

function startOfDay ($timestamp = 0)
{
	if (!$timestamp)
		$timestamp = time();

	return mktime(0, 0, 0, date("n", $timestamp), date("j", $timestamp), date("Y", $timestamp));
}

function endOfDay ($timestamp = 0)
{
	if (!$timestamp)
		$timestamp = time();

	return mktime(23, 59, 59, date("n", $timestamp), date("j", $timestamp), date("Y", $timestamp));
}

function GetWPers ($now, $max)
{

	$width = ($now / $max) * 100;

	if ($width == 0) 	$width += 2;
	if ($width == 1) 	$width++;
	if ($width > 100) 	$width = 100;

	return $width;
}

function InsertItem ($name, $user)
{

	$status = 0;

	$buyitem_res = mysql_query("SELECT * FROM `items` WHERE `name` = '".addslashes($name)."'");

	if (mysql_num_rows($buyitem_res) == 1)
	{

		$buyitem = mysql_fetch_array($buyitem_res);

		if ($buyitem['tip'] == 1 && $buyitem['slot2'] == "w5")
			$secondary = 1;
		else $secondary = 0;

		$inf = "".$buyitem['name']."|".$buyitem['title']."|".$buyitem['price']."|0|".$secondary."|".$buyitem['art']."|0|".$buyitem['iznos']."";

		$min = "".$buyitem['min_level']."|".$buyitem['min_str']."|".$buyitem['min_dex']."|".$buyitem['min_ag']."|".$buyitem['min_vit']."|".$buyitem['min_razum']."|".$buyitem['min_rase']."|".$buyitem['min_proff']."";

		$insert = mysql_query("INSERT INTO `game_objects` (`user`,`inf`,`min`,`tip`,`br1`,`br2`,`br3`,`br4`,`br5`,`br_m`,`min_d`,`max_d`,`hp`,`energy`,`strength`,`dex`,`agility`,`vitality`,`razum`,`krit`,`mkrit`,`unkrit`,`uv`,`unuv`,`pblock`,`mblock`,`pbr`,`kbr`,`metk`,`time`,`about`,`class`,`mz_1`,`mz_2`) VALUES ('".$user."','".$inf."','".$min."','".$buyitem['tip']."','".$buyitem['br1']."','".$buyitem['br2']."','".$buyitem['br3']."','".$buyitem['br4']."','".$buyitem['br5']."','".$buyitem['br_m']."','".$buyitem['min']."','".$buyitem['max']."','".$buyitem['hp']."','".$buyitem['energy']."','".$buyitem['strength']."','".$buyitem['dex']."','".$buyitem['agility']."','".$buyitem['vitality']."','".$buyitem['razum']."','".$buyitem['krit']."','".$buyitem['mkrit']."','".$buyitem['unkrit']."','".$buyitem['uv']."','".$buyitem['unuv']."','".$buyitem['pblock']."','".$buyitem['mblock']."','".$buyitem['pbr']."','".$buyitem['kbr']."','".$buyitem['metk']."','".time()."','".$buyitem['about']."','".$buyitem['class']."','".$buyitem['mz']."','".$buyitem['mz']."')");

		if ($insert)
			$status = 1;
	}

	return $status;
}

function GetStrTime ( $time ) {

	$str = "";

	$t = floor(($time - time()) / 60);
	$n = floor($t / 1440);
	$h = floor(($t / 60) - $n * 24);
	$hm = floor($t / 60);
	$m = $t - $hm * 60;

	if ($n > 0) { $str .= "$n д. "; }
	if ($h > 0) { $str .= "$h ч. "; }
	if ($m < 0) { $m = 0; }

	$str .= "$m м.";

	return $str;
}

function pretty_time ($seconds, $separator = '')
{
	if ($seconds > time())
		$seconds = $seconds - time();

	$day    = floor($seconds / (24 * 3600));
	$hh     = floor($seconds / 3600 % 24);
	$mm     = floor($seconds / 60 % 60);
	$ss     = floor($seconds / 1 % 60);

	$time = '';

	if ($day != 0)
		$time .= $day.(($separator != '') ? $separator : ' д. ');

	if ($hh > 0)
		$time .= $hh.(($separator != '') ? $separator : ' ч. ');

	if ($mm > 0)
		$time .= $mm.(($separator != '') ? $separator : ' м. ');

	if ($ss != 0)
		$time .= $ss.(($separator != '') ? '' : ' с. ');

	if (!$time)
		$time = '-';

	return $time;
}

function pagination ($count, $per_page, $link, $page = 0)
{
	if (!is_numeric($page))
		return '';

	$pages_count = @ceil($count / $per_page);

	if ($page == 0 || $page > $pages_count)
		$page = 1;

	$pages = '<ul class="pagination pagination-sm">';
	$end = 0;

	if ($pages_count > 1)
	{
		for ($i = 1; $i <= $pages_count; $i++)
		{
			if (($page <= $i + 3 && $page >= $i - 3) || $i == 1 || $i == $pages_count || $pages_count <= 6)
			{
				$end = 0;

				if ($i == $page)
					$pages .= "<li class=\"active\"><a href=\"" . $link . "&p=" . $i . "\">" . $i . "</a></li>";
				else
					$pages .= "<li><a href=\"" . $link . "&p=" . $i . "\">" . $i . "</a></li>";
			}
			else
			{
				if ($end == 0)
					$pages .= '<li><a href="javascript:;">... | </a></li>';

				$end = 1;
			}
		}
	}
	else
		$pages .= '<li><a href="javascript:;">1</a></li>';

	$pages .= '</ul>';

	return $pages;
}