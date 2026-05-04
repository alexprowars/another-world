<?php

namespace App;

class Vars
{
	protected array $data = [];

	public function __construct()
	{
		$this->init();
	}

	protected function init(): void
	{
		/** @var array<string, array> $data */
		$data = include(resource_path('data/main.php'));

		$this->data = $data;
	}

	public function getStats(): array
	{
		return $this->data['stats'] ?? [];
	}
}
