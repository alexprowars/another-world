<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1">

	@vite('resources/app/app.js')

	<x-inertia::head>
		<title>{{ config('app.name', 'Laravel') }}</title>
	</x-inertia::head>
</head>
<body>
	<x-inertia::app/>
</body>
</html>