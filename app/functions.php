<?php

use App\Support\ToastType;
use Inertia\Inertia;

if (!function_exists('flash')) {
	function flash(string $message)
	{
		Inertia::flash('message', $message);
	}
}

if (!function_exists('toast')) {
	function toast(ToastType $type, string $message, ?string $title = null): void
	{
		$toasts = Inertia::getFlashed()['notifications'] ?? [];
		$toasts[] = [
			'id' => Str::uuid(),
			'type' => $type->value,
			'body' => $message,
			'title' => $title,
		];

		Inertia::flash('notifications', $toasts);
	}
}

function convertIp($ip)
{
	if (!is_numeric($ip)) {
		return sprintf("%u", ip2long($ip));
	} else {
		return long2ip($ip);
	}
}

function startOfDay($timestamp = 0)
{
	if (!$timestamp) {
		$timestamp = time();
	}

	return mktime(0, 0, 0, date("n", $timestamp), date("j", $timestamp), date("Y", $timestamp));
}

function endOfDay($timestamp = 0)
{
	if (!$timestamp) {
		$timestamp = time();
	}

	return mktime(23, 59, 59, date("n", $timestamp), date("j", $timestamp), date("Y", $timestamp));
}

function pretty_time($seconds, $separator = '')
{
	if ($seconds > time()) {
		$seconds = $seconds - time();
	}

	$day    = floor($seconds / (24 * 3600));
	$hh     = floor($seconds / 3600 % 24);
	$mm     = floor($seconds / 60 % 60);
	$ss     = floor($seconds / 1 % 60);

	$time = '';

	if ($day != 0) {
		$time .= $day . (($separator != '') ? $separator : ' д. ');
	}

	if ($hh > 0) {
		$time .= $hh . (($separator != '') ? $separator : ' ч. ');
	}

	if ($mm > 0) {
		$time .= $mm . (($separator != '') ? $separator : ' м. ');
	}

	if ($ss != 0) {
		$time .= $ss . (($separator != '') ? '' : ' с. ');
	}

	if (!$time) {
		$time = '-';
	}

	return $time;
}
