<?php

namespace App\View\Components;

use App\Models\Level;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\Component;

class PersonView extends Component
{
	public function __construct(
		public User $user,
		public bool $isEdit = false,
	) {
	}

	public function render()
	{
		$parse = array();

		$up = Level::query()
			->select(['l1.up', 'l2.exp'])
			->from('levels as l1')
			->join('levels as l2', 'l2.id', '=', DB::raw('l1.id + 1'))
			->where('l1.up', $this->user->up)
			->where('l1.level', $this->user->level)
			->toBase()
			->first();

		$slots = $this->user->getSlotsInfo();

		$this->user->checkEffects();

		$parse['w_h'] = $this->getPercent($this->user->hp_now, $this->user->hp_max);
		$parse['w_e'] = $this->getPercent($this->user->energy_now, $this->user->energy_max);
		$parse['w_u'] = $this->getPercent($this->user->ustal_now, $this->user->ustal_max);

		/*$parse['ups_up'] 	= $up->up;
		$parse['ups_exp'] 	= $up->exp;

		if (!$this->obraz) {
			$parse['obraz'] = "1/" . $this->sex;
		}

		$parse['var_edit'] = $isEdit ? 1 : 0;

		$user = $this->toArray();

		foreach ($user as $key => $value) {
			$parse['~' . $key] = $value;
		}

		$parse += $this->getSlotsInfo();
		$this->checkEffects();

		$parse += $this->toArray();

		$parse['hp_now']		= round($this->hp_now);
		$parse['energy_now']	= round($this->energy_now);

		$parse['w_h'] = $this->getPercent($this->hp_now, $this->hp_max);
		$parse['w_e'] = $this->getPercent($this->energy_now, $this->energy_max);
		$parse['w_u'] = $this->getPercent($this->ustal_now, $this->ustal_max);

		$parse['sex'] = _getText('sex', $this->sex);

		$rait = $this->getUserRaiting();

		if ($this->reit != $rait) {
			$this->reit = $rait;
			$this->update();
		}

		$parse['rating'] = $this->reit;

		$parse['proffession'] = _getText('proffessions', $this->proff);
		$parse['actions'] = $this->renderUserStatus();*/

		return view('components.person-view')
			->with('user', $this->user)
			->with('slots', $slots)
			->with('up', $up)
			->with('parse', $parse);
	}

	protected function getPercent($current, $max)
	{
		if ($max <= 0) {
			return 0;
		}

		$result = ($current / $max) * 100;
		$result = min(100, max(0, $result));

		return $result;
	}
}
