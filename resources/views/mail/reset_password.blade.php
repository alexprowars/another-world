<x-mail::layout>
	<x-slot:header>
		<x-mail::header :url="config('app.url')">Другой мир</x-mail::header>
	</x-slot:header>

# Восстановление пароля

Вы запросили смену пароля в игре «Другой мир».

<x-mail::button :url="$url">Задать новый пароль</x-mail::button>

Срок действия ссылки: {{ $expiresIn }} мин.

Если вы не запрашивали смену пароля, просто проигнорируйте это письмо.

Команда «Другого мира»

	<x-slot:subcopy>
		<x-mail::subcopy>
Если кнопка не работает, откройте ссылку в браузере: [{{ $url }}]({{ $url }})
		</x-mail::subcopy>
	</x-slot:subcopy>

	<x-slot:footer>
		<x-mail::footer>© {{ now()->year }} Другой мир</x-mail::footer>
	</x-slot:footer>
</x-mail::layout>
