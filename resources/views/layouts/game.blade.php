<html lang="{{ App::getLocale() }}">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>@yield('title')</title>
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<link rel="shortcut icon" href="/favicon.ico">
		<meta name="csrf-token" content="{{ csrf_token() }}">
		@vite('resources/css/styles.css')
	</head>
	<body>
		<script type="text/javascript">
			var timestamp = {{ time() }};
			var timezone = 0;
			var addToUrl = '';
		</script>
		@yield('content')
	</body>
	@vite('resources/js/game.js')
	@stack('scripts')
</html>