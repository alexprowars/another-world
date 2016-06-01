<?= $this->tag->getDoctype() ?>
<html>
	<head>
		<?php echo $this->tag->getTitle(); ?>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<link rel="shortcut icon" href="/favicon.ico">
		<?php $this->assets->outputCss('css') ?>
		<?php $this->assets->outputJs('js') ?>
		<script type="text/javascript">
			var timestamp = <?=time() ?>;
			var timezone = 0;
			var addToUrl = '';
		</script>
	</head>
	<body style="overflow: hidden">
		<div class="game scrollbox">
			<div class="content" id="gamediv">
				<?php echo $this->getContent() ?>
			</div>
		</div>
		<div id="windowDialog"></div>
		<div id="loadingOverlay">загрузка...<br><img src="/images/loading.gif" alt=""></div>
		<div id="preloadOverlay"><img src="/images/loading.gif" alt=""></div>
	</body>
</html>