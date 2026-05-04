<?php

namespace App\Exceptions;

class Exception extends \Exception
{
	public function __construct(string $message = '', int $code = 500)
	{
		parent::__construct($message, $code);
	}
}
