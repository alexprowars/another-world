{{ getDoctype() }}
<html>
	<head>
		{{ getTitle() }}
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<link rel="shortcut icon" href="/favicon.ico">
		{{ assets.outputCss('css') }}
		{{ assets.outputJs('js') }}
		<script type="text/javascript">
			var timestamp = {{ time() }};
			var timezone = 0;
			var addToUrl = '';
			var isAjax = false;
		</script>
	</head>
	<body>
		{{ content() }}
	</body>
</html>