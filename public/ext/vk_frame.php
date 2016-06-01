<html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>Untitled Document</title>

	<script type="text/javascript">
		window.onload = (function ()
		{
			document.getElementById('formiframe').submit();
		});
	</script>
</head>
<body>
<iframe src="" name="iframe" style="visibility:hidden" frameborder="0"></iframe>
<form method="POST" target="iframe" name="formiframe" id="formiframe" action="/login/">
	<? foreach ($_GET as $key => $value): ?>
		<input type="hidden" name="<?=$key ?>" value="<?=$value ?>">
	<? endforeach; ?>
</form>
<center>Загрузка...<br><img src="/images/loading.gif" alt=""></center>
</body>
</html>