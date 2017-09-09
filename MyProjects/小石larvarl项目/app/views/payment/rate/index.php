<div class="col-xs-12">
	<div class="panel panel-primary">
		<div class="panel-heading">
			<h3 class="panel-title">
				<?php if ($platform) { ?>
				<i class="fa fa-flag-checkered"></i>
							<?php echo $platform->platform_name?>
							(<?php echo $platform->region->region_name ?>)
							 -
							<?php } ?>
						 	<?php if ($game) echo $game->game_name.'——————';?>
						 	<?php echo Lang::get('rate.current_rates')?>
						</li>
			</h3>
		</div>
		<div class="panel-body">
			<table class="table table-striped">
				<thead>
					<tr>
						<td><?php echo Lang::get('rate.rate_id') ?></td>
						<td><?php echo Lang::get('rate.from') ?></td>
						<td><?php echo Lang::get('rate.to') ?></td>
						<td><?php echo Lang::get('rate.multiplier_rate') ?></td>
						<td><?php echo Lang::get('rate.created_at') ?></td>
						<td><?php echo Lang::get('rate.updated_at') ?></td>
						<td><?php echo Lang::get('rate.user') ?></td>
					</tr>
				</thead>
				<tbody>

    <?php foreach ($current_exchange as $k => $v) { ?>
        <tr>
						<td><?php echo $v->rate_id ?></td>
						<td><?php echo Currency::where('currency_id',$v->from)->pluck('currency_name'); ?></td>
						<td><?php echo Currency::where('currency_id',$v->to)->pluck('currency_name'); ?></td>
						<td><?php echo $v->multiplier_rate ?></a></td>
						<td><?php echo $v->created_at?></td>
						<td><?php echo $v->updated_at?></td>
						<td><?php echo $v->user?></td>
					</tr>
    <?php } ?>  
        </tbody>
			</table>
		</div>
	</div>
	<div class="panel panel-success">
		<div class="panel-heading">
			<h3 class="panel-title">
				<?php echo Lang::get('rate.all_rates')?>
				</h3>
		</div>
		<div class="panel-body">
			<table class="table table-striped">
				<thead>
					<tr>
						<td><?php echo Lang::get('rate.rate_id') ?></td>
						<td><?php echo Lang::get('rate.from') ?></td>
						<td><?php echo Lang::get('rate.to') ?></td>
						<td><?php echo Lang::get('rate.multiplier_rate') ?></td>
						<td><?php echo Lang::get('rate.created_at') ?></td>
						<td><?php echo Lang::get('rate.updated_at') ?></td>
					</tr>
				</thead>
				<tbody>

    <?php foreach ($all_exchange as $k => $v) { ?>
        <tr>
						<td><?php echo $v->rate_id ?></td>
						<td><?php echo Currency::where('currency_id',$v->from)->pluck('currency_name'); ?></td>
						<td><?php echo Currency::where('currency_id',$v->to)->pluck('currency_name'); ?></td>
						<td><a href="/exchange-rate/<?php echo $v->rate_id?>/edit"><?php echo $v->multiplier_rate ?></a></td>
						<td><?php echo $v->created_at?></td>
						<td><?php echo $v->updated_at?></td>
					</tr>
    <?php } ?>  
        </tbody>
			</table>
		</div>
	</div>
</div>
