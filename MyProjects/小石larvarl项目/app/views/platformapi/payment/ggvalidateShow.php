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
						 	<?php echo Lang::get('google_validate列表')?>
						</li>
			</h3>
		</div>
		<div class="panel-body">
			<table class="table table-striped">
				<thead>
					<tr>
						<th>ID</th>
						<th>game_id</th>
						<th>package_name</th>
						<th>refresh_token</th>
						<th>client_id</th>
						<th>client_secret</th>
					</tr>
				</thead>

				<tbody>
                	<?php foreach ($data as $k => $v) { ?>
                		<tr>
							<td><a href="/platform-api/ggvalidate/modify?id=<?php echo $v->id; ?>"><?php echo $v->id ?></a></td>
							<td><?php echo $v->game_id?></td>
							<td><?php echo $v->package_name?></td>
							<td><?php echo $v->refresh_token?></td>
							<td><?php echo $v->client_id?></td>
							<td><?php echo $v->client_secret?></td>
						</tr>
    				<?php } ?>  
        		</tbody>
			</table>
		</div>
	</div>

</div>
