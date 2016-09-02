{{ getDoctype() }}
<html>
	<head>
		{{ getTitle() }}
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<link rel="shortcut icon" href="/favicon.ico">
		{{ assets.outputCss() }}
		{{ assets.outputJs() }}
		<script type="text/javascript">
			var timestamp = {{ time() }};
			var timezone = 0;
			var addToUrl = '';
		</script>
	</head>
	<body style="overflow: hidden">
		<div class="game scrollbox">
			<div class="content" id="gamediv">
				{{ content() }}
			</div>
		</div>
		<div id="windowDialog"></div>
		<div id="loadingOverlay">загрузка...<br><img src="/images/loading.gif" alt=""></div>
		<div id="preloadOverlay"><img src="/images/loading.gif" alt=""></div>
	</body>
</html>