<?= $this->tag->getDoctype() ?>
<html lang="ru">
<head>
	<?php echo $this->tag->getTitle(); ?>
	<?= $this->tag->tagHtml('meta', ['name' => 'description', 'content' => 'Отличная RPG онлайн игра посвященная боям и магии. Тысячи жизней, миллионы смертей, два бога, сотни битв между Светом и Тьмой.']) ?>
	<?= $this->tag->tagHtml('meta', ['name' => 'keywords', 'content' => 'игра, играть, игрушки, онлайн, диалоговый, интернет, Интернет, RPG, фантазия, фэнтези, меч, топор, магия, кулак, удар, блок, атака, защита, мистери оф хероес, бой, битва, отдых, обучение, развлечение, виртуальная реальность, рыцарь, маг, знакомства, чат, лучший, форум, свет, тьма, bk, игры, клан, банк, магазин, клан']) ?>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="shortcut icon" href="/favicon.ico">
	<?php $this->assets->outputCss('css') ?>
	<?php $this->assets->outputJs('js') ?>
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