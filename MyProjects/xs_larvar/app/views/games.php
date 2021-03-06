<!DOCTYPE html>
<html class="bg-light-blue">
    <head>
        <meta charset="UTF-8">
        <title>East Blue System | Games</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <!-- bootstrap 3.0.2 -->
        <link href="/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <!-- font Awesome -->
        <link href="/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Theme style -->
        <link href="/css/adminlte.css" rel="stylesheet" type="text/css" />
        <link href="/css/eastblue.css" rel="stylesheet" type="text/css" />
		<script src="/js/angular.min.js"></script>
		<script src="/js/jquery.min.js"></script>
		<script src="/js/ui-bootstrap.min.js"></script>
		<script src="/js/ui-bootstrap-tpls.min.js"></script>
    </head>
    <body class="bg-light-blue">
		<div class="container platforms">
			<h2><?php echo Lang::get('basic.choose_game') ?></h2>
			<?php foreach(Game::currentPlatform()->userGames()->get() as $k => $v) { ?>
			<div class="list-group ">
				<a href="/games/<?php echo $v->game_id?>" class="list-group-item">
					<h4 class="list-group-item-heading"><?php echo $v->game_name?></h4>
					<p class="list-group-item-text">
						<?php echo $v->platform->region->region_name; ?> <br/>
						<?php echo $v->game_url; ?>
					</p>
  				</a>
			</div>	
			<?php } ?>
		</div>
    </body>
</html>