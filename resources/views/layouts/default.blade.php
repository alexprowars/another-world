<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>@yield('title') · Another World</title>
	<meta name="description" content="@yield('description', 'Another World — браузерная ролевая игра. Сражайтесь, развивайте персонажа и найдите свой путь в мире света и тьмы.')">
	<link rel="icon" href="/favicon.ico">
	@vite('resources/app/portal.js')
</head>
<body class="portal">
	<header class="portal-header">
		<div class="portal-container portal-header__inner">
			<a class="portal-brand" href="{{ route('index') }}">
				<span class="portal-brand__seal">AW</span>
				<span>Another World<small>Другая эпоха. Твоя история.</small></span>
			</a>
			<nav class="portal-nav">
				<a href="{{ route('index') }}#news">Новости</a>
				<a href="{{ route('index') }}#rankings">Рейтинги</a>
				<a href="{{ route('law') }}" @class(['is-current' => request()->routeIs('law')])>Законы</a>
			</nav>
			@auth
				<a class="portal-header__enter" href="{{ route('person.detail') }}">В игру <x-portal_icon name="arrow" /></a>
			@else
				<a class="portal-header__enter" href="{{ route('index') }}#login">Войти <x-portal_icon name="arrow" /></a>
			@endauth
		</div>
	</header>

	<main>
		@yield('content')
	</main>

	<footer class="portal-footer">
		<div class="portal-container portal-footer__inner">
			<a class="portal-brand portal-brand--footer" href="{{ route('index') }}">
				<span class="portal-brand__seal">AW</span>
				<span>Another World<small>Мир, в котором есть место твоей истории.</small></span>
			</a>
			<div class="portal-footer__links">
				<a href="{{ route('law') }}">Законы игры</a>
				<a href="{{ route('agreement') }}">Пользовательское соглашение</a>
				<span>© {{ now()->year }} Another World</span>
			</div>
		</div>
	</footer>
</body>
</html>
