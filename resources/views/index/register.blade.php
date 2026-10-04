@extends('layouts.default')
@section('title', 'Регистрация')
@section('content')
	<section class="portal-hero portal-auth">
		<div class="portal-container portal-auth__inner">
			<section class="portal-login portal-auth__form">
				<h1>Регистрация</h1>
				<form method="POST" action="{{ route('register.store') }}">
					@csrf
					@if ($errors->any())
						<div class="portal-login__error">
							@foreach ($errors->all() as $error)
								<p>{{ $error }}</p>
							@endforeach
						</div>
					@endif

					<label for="register-name">Имя персонажа</label>
					<input
						id="register-name"
						name="name"
						type="text"
						value="{{ old('name') }}"
						autocomplete="nickname"
						minlength="3"
						maxlength="100"
						required
					>

					<label for="register-email">Электронная почта</label>
					<input
						id="register-email"
						name="email"
						type="email"
						value="{{ old('email') }}"
						autocomplete="email"
						maxlength="50"
						required
					>

					<fieldset class="portal-registration__gender">
						<legend>Пол персонажа</legend>
						<label><input type="radio" name="gender" value="M" @checked(old('gender') === 'M') required> Мужской</label>
						<label><input type="radio" name="gender" value="F" @checked(old('gender') === 'F') required> Женский</label>
					</fieldset>

					<label for="register-password">Пароль</label>
					<input
						id="register-password"
						name="password"
						type="password"
						placeholder="Не менее 6 символов"
						autocomplete="new-password"
						minlength="6"
						maxlength="72"
						required
					>

					<label for="register-password-confirmation">Повтори пароль</label>
					<input
						id="register-password-confirmation"
						name="password_confirmation"
						type="password"
						autocomplete="new-password"
						minlength="6"
						maxlength="72"
						required
					>

					<label class="portal-registration__terms">
						<input type="checkbox" name="terms" value="1" @checked(old('terms')) required>
						<span>Принимаю <a href="{{ route('law') }}" target="_blank" rel="noopener">законы игры</a> и <a href="{{ route('agreement') }}" target="_blank" rel="noopener">пользовательское соглашение</a></span>
					</label>

					<button class="portal-button portal-button--gold" type="submit">Создать персонажа <x-portal_icon name="arrow" /></button>
				</form>
				<p class="portal-login__switch">Уже есть персонаж? <a href="{{ route('login') }}">Войти</a></p>
			</section>
		</div>
	</section>
@endsection
