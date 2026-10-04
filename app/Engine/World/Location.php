<?php

namespace App\Engine\World;

use App\Models\Vault;

readonly class Location
{
	private function __construct(
		public string $city,
		public string $code,
		public ?int $roomId,
	) {
	}

	public static function fromCode(string $location): self
	{
		$parts = explode('.', $location);

		abort_unless(count($parts) === 2 || count($parts) === 3, 404);

		[$city, $code] = $parts;

		World::location($city, $code);

		$roomId = null;

		if ($code === 'vault') {
			abort_unless(
				isset($parts[2]) && ctype_digit($parts[2]) && (int) $parts[2] > 0,
				404,
			);

			$roomId = (int) $parts[2];

			abort_unless((string) $roomId === $parts[2], 404);
		} else {
			abort_unless(count($parts) === 2, 404);
		}

		return new self($city, $code, $roomId);
	}

	public function value(): string
	{
		return $this->city . '.' . $this->code . ($this->roomId !== null ? '.' . $this->roomId : '');
	}

	public function is(string $code): bool
	{
		return $this->code === $code;
	}

	public function inCity(string $code): self
	{
		return self::fromCode($this->city . '.' . $code);
	}

	public function url(array $query = []): string
	{
		$parameters = ['city' => $this->city];

		if ($this->roomId !== null) {
			$parameters['vaultRoom'] = $this->roomId;
		}

		return route('city.' . $this->code, [...$parameters, ...$query], false);
	}

	public function name(): string
	{
		$name = World::location($this->city, $this->code)['name'];

		return $name . ($this->roomId !== null ? ' — комната ' . $this->roomId : '');
	}

	public function ensureExists(): void
	{
		if ($this->roomId !== null) {
			abort_unless(Vault::query()->whereKey($this->roomId)->exists(), 404);
		}
	}

	public function toArray(): array
	{
		$definition = World::location($this->city, $this->code);
		$actions = [];

		foreach (World::handlers()[$this->code]['actions'] ?? [] as $path => $action) {
			$actions[$action] = $this->url() . '/' . $path;
		}

		return [
			'value' => $this->value(),
			'city' => $this->city,
			'code' => $this->code,
			'name' => $this->name(),
			'url' => $this->url(),
			'exit' => $definition['exit'] ?? null,
			'actions' => $actions,
		];
	}
}
