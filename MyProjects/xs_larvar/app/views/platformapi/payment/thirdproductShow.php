<div class="col-xs-12" ng-controller="mobilePayController">
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
						 	<?php echo Lang::get('third_product列表(可以通过点击id下的链接修改数据)')?>
						</li>
			</h3>
		</div>
		<div class="panel-body">
			<table class="table table-striped">
				<thead>
					<tr>
						<th>id</th>
						<th>package_name</th>
						<th>product_type</th>
						<th>third_product_id</th>
						<th>game_id</th>
						<th>payment_id</th>
						<th>currency_id</th>
						<th>pay_amount</th>
					<?php if(array_key_exists('charge_id', $data)) {?>	
						<th>charge_id</th>
					<?php }?>	
					<?php if(array_key_exists('token_amount', $data)) {?>
						<th>token_amount</th>
					<?php }?>
					</tr>
				</thead>

				<tbody>
                	<?php foreach ($data as $k => $v) { ?>
                		<tr>
							<td><a href="/platform-api/third_product/modify?id=<?php echo $v->id ?>"><?php echo $v->id ?></a></td>
							<td><?php echo $v->package_name?></td>
							<td><?php echo $v->product_type?></td>
							<td><?php echo $v->third_product_id ?></td>
							<td><?php echo $v->game_id ?></td>
							<td><?php echo $v->payment_id ?></td>
							<td><?php echo $v->currency_id ?></td>
							<td><?php echo $v->pay_amount ?></td>
						<?php if(array_key_exists('charge_id', $data)) {?>
							<td><?php echo $v->charge_id ?></td>
						<?php }?>
						<?php if(array_key_exists('token_amount', $data)) {?>
							<td><?php echo $v->token_amount ?></td>
						<?php }?>
						</tr>
    				<?php } ?>  
        		</tbody>
			</table>
		</div>
	</div>
</div>
