<?php

namespace App\Engine\Map;

use App\Http\Resources\MailLetterResource;
use App\Models\MailLetter;
use App\Services\PostOfficeService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PostOffice
{
	public function __invoke(): Response|RedirectResponse
	{
		$request = request();
		$user = $request->user();

		if ($request->isMethod('post')) {
			$data = $request->validate([
				'recipient' => ['required', 'string', 'max:100'],
				'subject' => ['required', 'string', 'max:100'],
				'body' => ['required', 'string', 'max:5000'],
			], [
				'recipient.required' => 'Укажите имя получателя.',
				'recipient.max' => 'Имя получателя должно содержать не более 100 символов.',
				'subject.required' => 'Введите тему письма.',
				'subject.max' => 'Тема должна содержать не более 100 символов.',
				'body.required' => 'Введите текст письма.',
				'body.max' => 'Текст письма должен содержать не более 5000 символов.',
			]);

			$letter = PostOfficeService::send($user, $data['recipient'], $data['subject'], $data['body']);

			flash('Письмо успешно отправлено. Списано ' . PostOfficeService::SEND_COST . ' зол.');

			return to_route('map', ['section' => 'sent', 'letter' => $letter->id]);
		}

		$section = $request->query('section', 'inbox');

		if (!in_array($section, ['inbox', 'sent', 'compose'], true)) {
			$section = 'inbox';
		}

		$letter = null;
		$draft = ['recipient' => '', 'subject' => ''];

		if ($section === 'compose' && $request->integer('reply') > 0) {
			$original = MailLetter::query()
				->with('sender:id,name')
				->where('recipient_id', $user->id)
				->findOrFail($request->integer('reply'));

			$subject = $original->subject;

			if (!str_starts_with($subject, 'Ответ: ')) {
				$subject = 'Ответ: ' . $subject;
			}

			$draft = [
				'recipient' => $original->sender->name,
				'subject' => mb_substr($subject, 0, 100),
			];
		} elseif ($section !== 'compose' && $request->integer('letter') > 0) {
			$letter = MailLetter::query()
				->with(['sender:id,name', 'recipient:id,name'])
				->where($section === 'sent' ? 'sender_id' : 'recipient_id', $user->id)
				->findOrFail($request->integer('letter'));

			if ($section === 'inbox' && !$letter->read_at) {
				$letter->update(['read_at' => now()]);
			}
		}

		$letters = $section !== 'compose'
			? MailLetter::query()
				->with(['sender:id,name', 'recipient:id,name'])
				->where($section === 'sent' ? 'sender_id' : 'recipient_id', $user->id)
				->orderByDesc('id')
				->paginate(15, ['id', 'sender_id', 'recipient_id', 'subject', 'read_at', 'created_at'])
				->appends(['section' => $section])
			: null;

		return Inertia::render('Map/PostOffice', [
			'section' => $section,
			'send_cost' => PostOfficeService::SEND_COST,
			'unread_count' => MailLetter::query()
				->where('recipient_id', $user->id)
				->whereNull('read_at')
				->count(),
			'letters' => $letters ? MailLetterResource::collection($letters->getCollection()) : [],
			'pagination' => $letters ? [
				'current_page' => $letters->currentPage(),
				'last_page' => $letters->lastPage(),
				'previous_url' => $letters->previousPageUrl(),
				'next_url' => $letters->nextPageUrl(),
				'total' => $letters->total(),
			] : null,
			'letter' => $letter ? array_merge(
				MailLetterResource::make($letter)->resolve(),
				['body' => $letter->body],
			) : null,
			'draft' => $draft,
		]);
	}
}
