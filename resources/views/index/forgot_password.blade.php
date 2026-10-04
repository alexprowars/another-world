@extends('layouts.default')
@section('title', 'Восстановление пароля')
@section('content')
	<section class="portal-hero portal-auth">
		<div class="portal-container portal-auth__inner">
			<section class="portal-login portal-auth__form">
				<h1>Восстановление пароля</h1>
				<p class="portal-auth__hint">Укажите почту персонажа — отправим ссылку для смены пароля.</p>
				<form method="POST" action="{{ route('password.email') }}">
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

					<label for="forgot-email">Электронная почта</label>
					<input
						id="forgot-email"
						name="email"
						type="email"
						value="{{ old('email') }}"
						autocomplete="email"
						maxlength="50"
						required
					>

					<button class="portal-button portal-button--gold" type="submit">Отправить ссылку <x-portal_icon name="arrow" /></button>
				</form>
				<p class="portal-login__switch"><a href="{{ route('login') }}">Вернуться ко входу</a></p>
			</section>
		</div>
	</section>
@endsection
