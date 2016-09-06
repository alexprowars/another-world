<html>
<head>
    <meta charset="utf-8" />
    <title>Install Game</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta content="width=device-width, initial-scale=1" name="viewport" />
	{{ assets.outputCss() }}
	{{ assets.outputJs() }}
</head>
<body>
	<header class="page-header">
		<nav class="navbar" role="navigation">
			<div class="container-fluid">
				<div class="havbar-header">
					<!--<a id="index" class="navbar-brand" href="/">
						<img src="../assets/layouts/layout6/img/logo.png" alt="Logo">
					</a>-->
				</div>
			</div>
		</nav>
	</header>
	<div class="container">
		<div class="page-content page-content-popup">
			<div class="page-fixed-main-content">
				{{ content() }}
			</div>
		</div>
	</div>
</body>
</html>