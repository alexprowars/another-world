<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Cache;

/**
 * @mixin User
 * @property User $resource
 */
class UserResource extends JsonResource
{
	public function toArray($request): array
	{
		$photo = Cache::remember('media::user_' . $this->resource->id, 3600, function () {
			return $this->resource->getFirstMediaUrl(conversionName: 'thumb');
		});

		$data = [
			'id' => $this->resource->id,
			'name' => trim($this->resource->nickname),
			'email' => $this->resource->email,
			'photo' => $photo,
		];

		return $data;
	}
}
