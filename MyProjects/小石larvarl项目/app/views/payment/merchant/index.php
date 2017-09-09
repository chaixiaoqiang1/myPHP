
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
						<td><?php echo Lang::get('merchant.merchant_name') ?></td>
						<td><?php echo Lang::get('merchant.merchant_key') ?></td>
						<td><?php echo Lang::get('merchant.merchant_key2') ?></td>
						<td><?php echo Lang::get('merchant.merchant_key3') ?></td>
						<?php foreach ($platform_merchant_data as $k => $v) {
							if(isset($v->merchant_key4)){ ?>
							<td><?php echo Lang::get('merchant.merchant_key4') ?></td>
						<?php 					
							}
							break;
						} ?>  
						<td><?php echo Lang::get('merchant.pay_type_id') ?></td>
						<td><?php echo Lang::get('merchant.method_id') ?></td>
						<td><?php echo Lang::get('merchant.domain_name') ?></td>
					</tr>
				</thead>
				<tbody>

    <?php foreach ($platform_merchant_data as $k => $v) { ?>
        <tr>
						<td><a href="/merchant-data/<?php echo $v->id ?>/edit"><?php echo $v->id ?></a></td>
						<td><a href="/merchant-data/<?php echo $v->id ?>/edit"><?php echo $v->merchant_name ?></a></td>
						<td><?php echo $v->merchant_key?></td>
						<td><?php echo $v->merchant_key2 ?></td>
						<td><?php echo $v->merchant_key3 ?></td>
						<?php echo isset($v->merchant_key4) ? "<td>$v->merchant_key4</td>" : ''; ?>
						<td><?php echo $v->pay_type_id ?></td>
						<td><?php echo $v->method_id ?></td>
						<td><?php echo $v->domain_name ?></td>
					</tr>
    <?php } ?>  
        </tbody>
			</table>
		</div>
	</div>
</div>
