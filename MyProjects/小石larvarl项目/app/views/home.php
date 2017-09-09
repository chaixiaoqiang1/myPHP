<!-- top row -->
<div class="row">
	<div class="col-xs-12 connectedSortable">
		<?php if ($platform) { ?>
		<div class="panel panel-info">
			<div class="panel-heading">
				<h3 class="panel-title"><?php echo Lang::get('basic.current_platform') ?></h3> 
			</div>
			<div class="panel-body">
				<p><?php echo $platform->platform_name;?></p>
				<p><?php echo $platform->region->region_name ?></p>
				<p><a href="<?php echo $platform->platform_url;?>" target="_blank"><?php echo $platform->platform_url?></a></p>
				<p><?php echo $platform->created_at ?></p>
			</div>
		</div>
		<?php } else if (Auth::user()->is_admin) { ?>
		<div class="panel panel-warning">
			<div class="panel-heading">
				<h3 class="panel-title"><?php echo Lang::get('basic.tip') ?></h3> 
			</div>
			<div class="panel-body">
				<?php echo Lang::get('basic.tip_create_platform'); ?>
			</div>
		</div>
		<?php } ?>
		<?php if ($game) { ?>
		<div class="panel panel-info">
			<div class="panel-heading">
				<h3 class="panel-title"><?php echo Lang::get('basic.current_game') ?></h3> 
			</div>
			<div class="panel-body">
				<p><?php echo $game->game_name;?></p>
				<p><?php echo $game->created_at ?></p>
			</div>
		</div>
		<?php } else if (Auth::user()->is_admin && $platform) { ?>
		<div class="panel panel-warning">
			<div class="panel-heading">
				<h3 class="panel-title"><?php echo Lang::get('basic.tip') ?></h3> 
			</div>
			<div class="panel-body">
				<?php echo Lang::get('basic.tip_create_game'); ?>
			</div>
		</div>
		<?php } ?>
		

		<?php if (!Auth::user()->is_admin && !Auth::user()->permissions) { ?>
		<div class="panel panel-warning">
			<div class="panel-heading">
				<h3 class="panel-title"><?php echo Lang::get('basic.tip') ?></h3> 
			</div>
			<div class="panel-body">
				<p><?php echo Lang::get('basic.tip_permission') ?></p>
			</div>
		</div>
		<?php } ?>
	</div><!-- /.col -->
</div>
<!-- /.row -->

<!-- Main row -->
<div class="row">
	<!-- Left col -->
	<section class="col-lg-12 connectedSortable"> 
	</section><!-- /.Left col -->
	<!-- right col (We are only adding the ID to make the widgets sortable)-->
	<section class="col-lg-6 connectedSortable">

	</section>
</div>