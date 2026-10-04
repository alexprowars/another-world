<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
	protected $table = 'news';
	protected $guarded = [];

	protected function plainText(): Attribute
	{
		return Attribute::get(function () {
			$text = preg_replace('/<br\s*\/?\s*>|<\/p>|<\/div>/i', "\n", $this->text);

			return html_entity_decode(strip_tags($text ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
		});
	}
}
