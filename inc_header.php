<?php require_once 'inc_functions.php'; ?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title><?php echo STORE_NAME . ( isset( $title ) ? ' &bull; ' . $title : '' ) ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<!-- Bootstrap -->
	<link rel="stylesheet" href="<?=BASE_URL ?>css/bootstrap.css" media="screen">
	<link rel="stylesheet" href="<?=BASE_URL ?>css/bootstrap-responsive.css" media="screen">
	<link rel="stylesheet" href="<?=BASE_URL ?>css/demo.css">
	<link rel="shortcut icon" href="http://www.paypalobjects.com/en_US/i/icon/pp_favicon_x.ico" />
</head>
<body>
	<header class="banner" role="banner">
		<div class="container">
			<div class="row-fluid">
				<h1 class="span2 brand"><a href="<?=BASE_URL ?>"><?=STORE_NAME ?></a></h1>
				<div class="span3 offset7 auth">
					<?php require 'inc_login.php'; ?>
				</div>
			</div>
		</div>
	</header>

	<?php if ( isset( $title ) ): ?>
		<div class="subtitle">
			<h2 class="container"><?=$title ?></h2>
		</div>
	<?php endif; ?>

	<div class="container guts">
		<div class="row-fluid">
