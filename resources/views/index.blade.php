@extends('layouts.default')
@section('title', 'Браузерная ролевая игра')
@section('content')
	<section class="portal-hero">
		<div class="portal-container portal-hero__inner">
			<div class="portal-hero__story">
				<span class="portal-eyebrow"><span></span> Браузерная ролевая игра</span>
				<h1>Другой мир.<br>Твоя <em>легенда.</em></h1>
				<p>Между светом и тьмой всегда есть выбор.<br>Открой мир сражений, магии и союзов —<br>и реши, кем станешь ты.</p>
				<div class="portal-hero__actions">
					<a class="portal-button portal-button--gold" href="{{ route('register') }}">Начать приключение <x-portal_icon name="arrow" /></a>
				</div>
				<span class="portal-hero__note">Прямо в браузере · Без установки</span>
			</div>

			<section class="portal-login" id="login">
				<h2>Вход в игру</h2>
				<form method="POST" action="{{ route('login.store') }}">
					@csrf
					@if (session('status'))
						<div class="portal-login__status">{{ session('status') }}</div>
					@endif
					@if ($errors->any())
						<div class="portal-login__error">
							@foreach ($errors->all() as $error)
								<p>{{ $error }}</p>
							@endforeach
						</div>
					@endif
					<label for="login-email">Электронная почта</label>
					<input id="login-email" name="email" type="email" value="{{ old('email') }}" placeholder="Твоя почта" autocomplete="username" maxlength="50" required>
					<label for="login-password">Пароль</label>
					<input id="login-password" name="password" type="password" placeholder="Твой пароль" autocomplete="current-password" maxlength="255" required>
					<div class="portal-login__options">
						<label class="portal-login__remember"><input type="checkbox" name="remember" value="1" @checked(old('remember'))> Запомнить меня</label>
						<a href="{{ route('password.request') }}">Забыли пароль?</a>
					</div>
					<button class="portal-button portal-button--gold" type="submit">Войти в мир <x-portal_icon name="arrow" /></button>
				</form>
				<p class="portal-login__switch">Нет персонажа? <a href="{{ route('register') }}">Регистрация</a></p>
				@if (!empty($socialProviders))
					<div class="portal-login__divider"><span>или войти с помощью</span></div>
					<div class="portal-login__social">
						@foreach ($socialProviders as $driver => $provider)
							<a
								class="portal-button portal-button--social"
								href="{{ route('login.social', ['service' => $driver]) }}"
								title="Войти через {{ $provider['label'] }}"
							>
								<x-portal_icon :name="$provider['icon']" />
							</a>
						@endforeach
					</div>
				@endif
				<p class="portal-login__terms">Начиная игру, ты принимаешь <a href="{{ route('law') }}">законы</a><br>и <a href="{{ route('agreement') }}">пользовательское соглашение</a>.</p>
			</section>
		</div>
	</section>

	<div class="portal-pulse">
		<div class="portal-container portal-pulse__inner">
			<span class="portal-pulse__status"><i></i> Мир живёт прямо сейчас</span>
			<span><strong>{{ number_format($totalOnline, 0, ',', ' ') }}</strong> игроков онлайн</span>
			<span><strong>{{ number_format($registeredToday, 0, ',', ' ') }}</strong> новых героев сегодня</span>
		</div>
	</div>

	<div class="portal-container">
		<section class="portal-features">
			<article>
				<x-portal_icon name="sword" />
				<div>
					<h2>Сражайся с умом</h2>
					<p>Тактика, приёмы и командные бои.<br>Каждое решение имеет значение.</p>
				</div>
			</article>
			<article>
				<x-portal_icon name="compass" />
				<div>
					<h2>Найди свой путь</h2>
					<p>Профессии, магия и развитие героя.<br>Создай свой стиль игры.</p>
				</div>
			</article>
			<article>
				<x-portal_icon name="shield" />
				<div>
					<h2>Стань частью клана</h2>
					<p>Союзники, общие цели и победы.<br>Вместе можно больше.</p>
				</div>
			</article>
		</section>

		<div class="portal-feed">
			<section class="portal-news" id="news">
				<div class="portal-section-heading">
					<div>
						<span class="portal-kicker">Летопись Another World</span>
						<h2>Хроники мира</h2>
					</div>
					<x-portal_icon name="quill" />
				</div>
				@forelse ($news as $article)
					<article class="portal-news__item" id="news-{{ $article->id }}">
						<div class="portal-news__meta">
							<span>Новости мира</span>
							<time datetime="{{ $article->created_at?->toDateString() }}">{{ $article->created_at?->format('d.m.Y') }}</time>
						</div>
						<h3>{{ $article->title ?: 'Вести Another World' }}</h3>
						<div class="portal-news__text">{{ $article->plain_text }}</div>
						@if ($article->author)
							<p class="portal-news__author">Записал: {{ $article->author }}</p>
						@endif
					</article>
				@empty
					<div class="portal-empty">
						<x-portal_icon name="quill" />
						<h3>Новая глава уже близко</h3>
						<p>Здесь появятся новости, обновления<br>и истории из жизни Another World.</p>
					</div>
				@endforelse
				@if ($news->hasPages())
					<nav class="portal-pagination">
						@if ($news->onFirstPage())
							<span>← Новее</span>
						@else
							<a href="{{ $news->previousPageUrl() }}#news">← Новее</a>
						@endif
						<span>{{ $news->currentPage() }} / {{ $news->lastPage() }}</span>
						@if ($news->hasMorePages())
							<a href="{{ $news->nextPageUrl() }}#news">Ранее →</a>
						@else
							<span>Ранее →</span>
						@endif
					</nav>
				@endif
			</section>

			<aside class="portal-rankings" id="rankings">
				<section class="portal-ranking">
					<div class="portal-ranking__heading">
						<x-portal_icon name="crown" />
						<div>
							<span class="portal-kicker">Зал славы</span>
							<h2>Топ игроков</h2>
						</div>
					</div>
					<table>
						<caption>Лидеры по рейтингу</caption>
						<thead>
							<tr>
								<th scope="col">№</th>
								<th scope="col">Персонаж</th>
								<th scope="col">Рейтинг</th>
							</tr>
						</thead>
						<tbody>
							@forelse ($topUsers as $player)
								<tr>
									<td>{{ $loop->iteration }}</td>
									<td>
										<a href="{{ route('info', ['id' => $player->id]) }}">{{ $player->name }}</a>
										<small>{{ $player->level }} ур.</small>
									</td>
									<td>{{ number_format($player->rating, 0, ',', ' ') }}</td>
								</tr>
							@empty
								<tr>
									<td colspan="3" class="portal-ranking__empty">Первые герои ещё на пути к славе.</td>
								</tr>
							@endforelse
						</tbody>
					</table>
					<p class="portal-ranking__note">Сильнейшие герои Another World</p>
				</section>
				<section class="portal-ranking">
					<div class="portal-ranking__heading">
						<x-portal_icon name="shield" />
						<div>
							<span class="portal-kicker">Сила единства</span>
							<h2>Топ кланов</h2>
						</div>
					</div>
					<table>
						<caption>Лидеры по очкам клана</caption>
						<thead>
							<tr>
								<th scope="col">№</th>
								<th scope="col">Клан</th>
								<th scope="col">Очки</th>
							</tr>
						</thead>
						<tbody>
							@forelse ($topTribes as $tribe)
								<tr>
									<td>{{ $loop->iteration }}</td>
									<td>{{ $tribe->name }}</td>
									<td>{{ number_format($tribe->points, 0, ',', ' ') }}</td>
								</tr>
							@empty
								<tr>
									<td colspan="3" class="portal-ranking__empty">История великих союзов только начинается.</td>
								</tr>
							@endforelse
						</tbody>
					</table>
					<p class="portal-ranking__note">Общие цели. Общая слава.</p>
				</section>
			</aside>
		</div>

	</div>
@endsection
