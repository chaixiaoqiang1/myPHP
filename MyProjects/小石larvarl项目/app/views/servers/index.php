<script>
	function serverActController($scope, $http, alertService) {
		$scope.server = {};
		$scope.open = function(url) {
			if (!confirm('Are you sure?')) {
				return;
			}
			$http({
				'method' : 'get',
				'url'	 : url,
			}).success(function(data) {
				location.href = location;
			}).error(function(data) {
				alert(data.error);
			});
		};
		$scope.close = function(url) {
			if (!confirm('Are you sure?')) {
				return;
			}
			$http({
				'method' : 'get',
				'url'	 : url,
			}).success(function(data) {
				location.href = location;
			}).error(function(data) {
				alert(data.error);
			});
		};
		$scope.init = function(url) {
			if (!confirm('Are you sure?')) {
				return;
			}
			$http({
				'method' : 'get',
				'url'	 : url,
			}).success(function(data) {
				alert(data.result);
			}).error(function(data) {
				alert(data.error);
			});
		};
	}
</script>
<div class="col-xs-12" ng-controller="serverActController">
	<table class="table table-striped">
		<thead>
			<tr class="info">
				<td><?php echo Lang::get('server.server_track_name')?></td>
				<td><?php echo Lang::get('server.server_name') ?></td>
				<td><?php echo Lang::get('server.server_uid') ?></td>
				<td><?php echo Lang::get('server.server_ip') ?></td>
				<td><?php echo Lang::get('server.server_port') ?></td>
				<td><?php echo Lang::get('server.server_version') ?></td>
				<td><?php echo Lang::get('server.server_internal_id') ?></td>
				<td><?php echo Lang::get('server.created_at')?></td>
				<td><?php echo Lang::get('server.open_server_time')?></td>
				<td><?php echo Lang::get('server.is_server_on')?></td>
				<td><?php echo Lang::get('server.on_recharge')?></td>
				<td><?php echo Lang::get('server.is_use_for_month_card')?></td>
				<td><?php echo Lang::get('server.open_server_action')?></td>
				<td><?php echo Lang::get('server.init_server')?></td>
				
				
			</tr>
		</thead>
		<tbody>
	<?php foreach ($server as $k => $v) { ?>
		<tr>
			<td><a href="servers/<?php echo $v->server_id?>/edit"><?php echo $v->server_track_name?></a></td>
			<td><a href="servers/<?php echo $v->server_id?>/edit"><?php echo $v->server_name?></a></td>
			<td><?php echo $v->server_uid?></td>
			<td><?php echo $v->server_ip?></td>
			<td><?php echo $v->server_port?></td>
			<td><?php echo $v->version?></td>
			<td><?php echo $v->server_internal_id?></td>
			<td><?php echo $v->created_at?></td>
			<td><?php if ($v->open_server_time) {?><?php echo $v->open_server_time ?><?php }?></td>
			<td><?php if ($v->is_server_on) { ?><?php echo '<span class="label label-success">' . $v->is_server_on . '</span>'?><?php } else { ?><?php echo '<span class="label label-danger">' . $v->is_server_on . '</span>'?><?php } ?></td>
			<td><?php if ($v->on_recharge) { ?><?php echo '<span class="label label-success">' . $v->on_recharge. '</span>'?><?php } else { ?><?php echo '<span class="label label-danger">' . $v->on_recharge. '</span>'?><?php } ?></td>
			<td><?php if ($v->use_for_month_card) { ?><?php echo '<span class="label label-success">' . $v->use_for_month_card. '</span>'?><?php } else { ?><?php echo '<span class="label label-danger">' . $v->use_for_month_card. '</span>'?><?php } ?></td>
			<td><?php if ($v->is_server_on) { ?><button class="btn btn-danger" ng-click="close('/servers/<?php echo $v->server_id?>/close')"><?php echo Lang::get('server.close_server')?></span><?php } else { ?><button class="btn btn-primary" ng-click="open('servers/<?php echo $v->server_id ?>/open')"><?php echo Lang::get('server.open_server') ?></span><?php } ?></td>
			<td><button class="btn btn-default" ng-click="init('/servers/<?php echo $v->server_id?>/init')"><?php echo Lang::get('server.init_server_btn')?></button></td>
		</tr>
	<?php } ?>	
		</tbody>
	</table>
</div>
<?php echo $server->links();?>