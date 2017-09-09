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
						</li>
			</h3>
		</div>
		<div class="panel-body">
			<table class="table table-striped">
				<thead>
					<tr>
						<td><?php echo Lang::get('merchant.id') ?></td>
						<td><?php echo Lang::get('merchant.pay_type_id') ?></td>
						<td><?php echo Lang::get('merchant.method_id') ?></td>
						<td><?php echo Lang::get('merchant.currency_id') ?></td>
						<td><?php echo Lang::get('merchant.currency_order') ?></td>
						
					</tr>
				</thead>
				<tbody>

    <?php foreach ($platform_currency as $k => $v) { ?>
        <tr>
						<td><a href="/payment-currency/<?php echo $v->id ?>/edit"><?php echo $v->id ?></a></td>
						<td><?php echo $v->pay_type_id ?></td>
						<td><?php echo $v->method_id?></td>
						<td><?php echo $v->currency_id ?></td>
						<td><?php echo $v->currency_order ?></td>
					</tr>
    <?php } ?>  
        </tbody>
			</table>
		</div>
	</div>
</div>
