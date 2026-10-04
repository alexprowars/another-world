@extends('layouts.default')
@section('title', 'Новый пароль')
@section('content')
	<section class="portal-hero portal-auth">
		<div class="portal-container portal-auth__inner">
			<section class="portal-login portal-auth__form">
				<h1>Новый пароль</h1>
				<form method="POST" action="{{ route('password.update') }}">
					@csrf
					<input type="hidden" name="token" value="{{ $token }}">
					@if ($errors->any())
						<div class="portal-login__error">
							@foreach ($errors->all() as $error)
								<p>{{ $error }}</p>
							@endforeach
						</div>
					@endif

					<label for="reset-email">Электронная почта</label>
					<input
						id="reset-email"
						name="email"
						type="email"
						value="{{ old('email', $email) }}"
						autocomplete="email"
						maxlength="50"
						required
					>

					<label for="reset-password">Новый пароль</label>
					<input
						id="reset-password"
						name="password"
						type="password"
						placeholder="Не менее 6 символов"
						autocomplete="new-password"
						minlength="6"
						maxlength="72"
						required
					>

					<label for="reset-password-confirmation">Повторите пароль</label>
					<input
						id="reset-password-confirmation"
						name="password_confirmation"
						type="password"
						autocomplete="new-password"
						minlength="6"
						maxlength="72"
						required
					>

					<button class="portal-button portal-button--gold" type="submit">Сохранить пароль <x-portal_icon name="arrow" /></button>
				</form>
				<p class="portal-login__switch"><a href="{{ route('password.request') }}">Запросить новую ссылку</a></p>
			</section>
		</div>
	</section>
@endsection
