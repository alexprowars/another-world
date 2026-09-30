<?php

namespace App\Http\Controllers;

use App\Exceptions\Exception;
use App\Http\Controller;
use App\Http\Resources\InventoryItemResource;
use App\Http\Resources\UserFriendResource;
use App\Models\UserSet;
use App\Services\EquipmentSetService;
use App\Services\FriendService;
use App\Services\InventoryService;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class PersonController extends Controller
{
	public function index()
	{
		return Inertia::render('Person/Index');
	}

	public function inventory(Request $request)
	{
		$type = $request->integer('item_type', 1);
		$type = $type >= 1 && $type <= 8 ? $type : 1;

		if ($request->integer('onset')) {
			InventoryService::onsetObject($this->user, $request->integer('onset'));

			return to_route('person.inventory');
		}

		if ($request->input('unset') === 'all') {
			InventoryService::unsetAllObject($this->user);

			return to_route('person.inventory');
		}

		if ($request->integer('unset')) {
			InventoryService::unsetObject($this->user, $request->integer('unset'));

			return to_route('person.inventory');
		}

		$items = InventoryService::getInventoryObjects($this->user, $type);

		return Inertia::render('Person/Inventory', [
			'item_type' => $type,
			'items' => InventoryItemResource::collection($items),
		]);
	}

	public function drop(Request $request): RedirectResponse
	{
		$data = $request->validate([
			'id' => ['required', 'integer', 'min:1'],
			'item_type' => ['required', 'integer', 'between:1,8'],
		]);

		try {
			InventoryService::drop($request->user(), (int) $data['id']);

			flash('Предмет выброшен.');
		} catch (Exception $e) {
			return to_route('person.inventory', ['item_type' => $data['item_type']])->withErrors(['drop' => $e->getMessage()]);
		}

		return to_route('person.inventory', ['item_type' => $data['item_type']]);
	}

	public function sets(Request $request): Response|RedirectResponse
	{
		if ($request->isMethod('get')) {
			return Inertia::render('Person/Sets', [
				'sets' => UserSet::query()->whereBelongsTo($this->user)->orderByDesc('id')->get(['id', 'name']),
			]);
		}

		$data = $request->validate([
			'action' => ['required', 'in:save,wear,delete'],
			'name' => ['exclude_unless:action,save', 'required', 'string', 'max:255', 'regex:/^[А-Яа-яЁёa-zA-Z0-9_!~.@ \-]+$/u'],
			'id' => ['exclude_if:action,save', 'required', 'integer', 'min:1'],
		], [
			'name.required' => 'Введите название комплекта.',
			'name.max' => 'Название должно содержать не более 255 символов.',
			'name.regex' => 'В названии допустимы русские и латинские буквы, цифры, пробелы и символы _-!~.@.',
		]);

		try {
			switch ($data['action']) {
				case 'save':
					EquipmentSetService::save($request->user(), $data['name']);
					flash('Комплект сохранён.');
					break;
				case 'wear':
					$skipped = EquipmentSetService::wear($request->user(), (int) $data['id']);
					flash($skipped > 0 ? 'Комплект надет частично. Недоступных вещей: ' . $skipped . '.' : 'Комплект надет.');
					break;
				case 'delete':
					EquipmentSetService::delete($request->user(), (int) $data['id']);
					flash('Комплект удалён.');
					break;
			}
		} catch (Exception $e) {
			return to_route('person.inventory.sets')->withErrors(['set' => $e->getMessage()]);
		}

		return to_route('person.inventory.sets');
	}

	public function settings(Request $request): Response|RedirectResponse
	{
		$user = $request->user();

		if ($request->isMethod('post')) {
			$data = $request->validate([
				'action' => ['required', 'in:options,profile,password,email'],
			]);

			switch ($data['action']) {
				case 'options':
					$request->validate([
						'presence_status' => ['required', 'integer', 'between:0,4'],
					], [
						'presence_status.*' => 'Выберите статус из списка.',
					]);

					$user->update([
						'options' => array_merge($user->options ?? [], [
							'presence_status' => $request->integer('presence_status'),
						]),
					]);

					flash('Настройки сохранены');

					break;
				case 'profile':
					$data = $request->validate([
						'city' => ['present', 'nullable', 'string', 'max:255'],
						'about' => ['present', 'nullable', 'string', 'max:10000'],
					], [
						'city.*' => 'Город должен быть строкой длиной не более 255 символов.',
						'about.*' => 'Рассказ о себе должен быть текстом длиной не более 10 000 символов.',
					]);

					$user->update($data);

					flash('Анкета сохранена');

					break;
				case 'password':
					$data = $request->validate([
						'current_password' => ['required', 'string', 'current_password'],
						'password' => ['required', 'string', 'min:6', 'max:72', 'confirmed'],
					], [
						'current_password.required' => 'Введите текущий пароль.',
						'current_password.current_password' => 'Текущий пароль указан неверно.',
						'password.required' => 'Введите новый пароль.',
						'password.min' => 'Пароль не должен быть короче 6 символов.',
						'password.max' => 'Пароль не должен быть длиннее 72 символов.',
						'password.confirmed' => 'Введённые пароли не совпадают.',
					]);

					$user->update(['password' => Hash::make($data['password'])]);

					flash('Пароль изменён');

					break;
				case 'email':
					$data = $request->validate([
						'current_email' => ['required', 'string', Rule::in([$user->email])],
						'email' => ['required', 'email', 'max:50', Rule::unique('users', 'email')->ignore($user->id)],
					], [
						'current_email.required' => 'Введите текущий e-mail.',
						'current_email.in' => 'Текущий e-mail указан неверно.',
						'email.required' => 'Введите новый e-mail.',
						'email.email' => 'Введите корректный e-mail.',
						'email.max' => 'E-mail не должен быть длиннее 50 символов.',
						'email.unique' => 'Этот e-mail уже используется.',
					]);

					if ($data['email'] !== $user->email) {
						$user->update(['email' => $data['email'], 'email_verified_at' => null]);
					}

					flash('E-mail сохранён.');

					break;
			}

			return to_route('person.settings');
		}

		return Inertia::render('Person/Settings', [
			'options' => ['presence_status' => $user->options['presence_status'] ?? 0],
			'city' => $user->city,
			'about' => $user->about,
		]);
	}

	public function updates(Request $request): Response|RedirectResponse
	{
		if ($request->isMethod('post')) {
			$data = $request->validate([
				'update' => ['required', 'string'],
			]);

			try {
				UserService::upgradeStat($request->user(), $data['update']);

				flash('Удачно увеличили физический параметр "' . __('main.stats.' . $data['update']) . '"!');
			} catch (Exception $e) {
				flash($e->getMessage());
			}

			return to_route('person.updates');
		}

		return Inertia::render('Person/Updates');
	}

	public function friends(Request $request): Response|RedirectResponse
	{
		$user = $request->user();

		if ($request->isMethod('post')) {
			$data = $request->validate([
				'action' => ['required', 'in:add,remove'],
				'name' => ['required', 'string', 'max:100', 'regex:/\A[a-zA-Zа-яА-ЯёЁ0-9_.,!?* -]+\z/u'],
				'is_ignored' => ['required_if:action,add', 'boolean'],
			], [
				'name.required' => 'Введите ник персонажа',
				'name.regex' => 'Ник содержит запрещённые символы',
				'name.max' => 'Ник не должен быть длиннее 100 символов',
			]);

			try {
				if ($data['action'] === 'add') {
					FriendService::add($user, $data['name'], $request->boolean('is_ignored'));
					flash('Персонаж добавлен в ваш список');
				} else {
					FriendService::remove($user, $data['name']);
					flash('Персонаж удалён из вашего списка');
				}
			} catch (Exception $e) {
				flash($e->getMessage());
			}

			return to_route('person.friends');
		}

		$friends = $user->friends()
			->with('friend.tribe')
			->whereHas('friend')
			->orderBy('is_ignored')
			->orderBy('id')
			->get();

		return Inertia::render('Person/Friends', [
			'friends' => UserFriendResource::collection($friends),
		]);
	}

	public function abilities(Request $request): Response|RedirectResponse
	{
		$priem_full = require resource_path('data/battle.php');

		$priem_full = array_filter(
			$priem_full,
			fn(array $ab) => $ab['level'] <= $this->user->level
		);

		$active = $this->user->abilities()
			->pluck('ability', 'slot');

		try {
			if ($onset = $request->integer('onset')) {
				UserService::activateAbility($this->user, $onset);

				return back();
			}

			if ($unset = $request->integer('unset')) {
				UserService::deactivateAbility($this->user, $unset);

				return back();
			}
		} catch (Throwable $e) {
			flash($e->getMessage());
		}

		foreach ($priem_full as $k => $v) {
			if ($active->contains($k)) {
				$priem_full[$k]['onset'] = 'Y';
			}
		}

		return Inertia::render('Person/Abilities', [
			'items' => $priem_full,
			'active' => $active,
		]);
	}

	public function workAction()
	{
		$this->view->disableLevel(View::LEVEL_LAYOUT);

		$refers = $this->db->query("SELECT id, username, level, onlinetime FROM game_users WHERE refer = '" . $this->user->id . "'")->fetchAll();

		$this->view->setVar('refers', $refers);
	}
}
