<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPassword
{
	/** @param string $url */
	protected function buildMailMessage($url): MailMessage
	{
		$broker = config('auth.defaults.passwords');
		$expiresIn = config('auth.passwords.' . $broker . '.expire');

		return (new MailMessage())
			->subject('Восстановление пароля — Другой мир')
			->markdown('mail.reset_password', [
				'url' => $url,
				'expiresIn' => $expiresIn,
			]);
	}
}
