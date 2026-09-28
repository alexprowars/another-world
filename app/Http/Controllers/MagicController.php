<?php

namespace App\Http\Controllers;

use App\Exceptions\Exception;
use App\Http\Controller;
use App\Services\MagicService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MagicController extends Controller
{
	public function store(Request $request): JsonResponse
	{
		$request->validate([
			'item' => ['required', 'integer', 'min:1'],
			'target' => ['required', 'string', 'max:100'],
			'battle' => ['nullable', 'integer', 'min:1'],
			'round' => ['required_with:battle', 'nullable', 'integer', 'min:1'],
		]);

		try {
			$message = MagicService::useMagic(
				$request->user(),
				$request->integer('item'),
				$request->input('target'),
				$request->filled('battle') ? $request->integer('battle') : null,
				$request->filled('round') ? $request->integer('round') : null,
			);
		} catch (Exception $e) {
			return response()->json(['message' => $e->getMessage()], 422);
		}

		$startedBattle = !$request->filled('battle') && $request->user()->fresh()->battle_id;

		return response()->json([
			'message' => $message,
			'redirect' => $startedBattle ? route('battle') : null,
		]);
	}
}
