{{ getDoctype() }}
<html lang="ru">
<head>
	{{ getTitle() }}
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="shortcut icon" href="/favicon.ico">
	{{ assets.outputCss('css') }}
	{{ assets.outputJs('js') }}
</head>
<body>

<div style="display: flex;align-items: center;justify-content: center;height: 100%">
	<script src="//ulogin.ru/js/ulogin.js"></script>
	<div id="uLogin" data-ulogin="display=panel;fields=first_name,last_name,sex;providers=vkontakte,odnoklassniki,mailru,facebook;hidden=other;redirect_uri=%2Findex%2Flogin%2F"></div>
</div>

<style>
	html, body {
		height: 100%;
	}
</style>