<div class="col-xs-12" ng-controller="mobilePayController">
	<div class="panel panel-primary">
		<div class="panel-heading">
			<h3 class="panel-title">
				<i class="fa fa-flag-checkered">数据库内所有支付方式（通过点击payment_id下链接进行修改）</i>					
			</h3>
		</div>
		<div class="panel-body">
			<table class="table table-striped">
				<thead>
					<tr>
						<th>payment_id</th>
						<th>method_name</th>
						<th>pay_type</th>
						<th>pay_lib</th>
					</tr>
				</thead>

				<tbody>
                	<?php foreach ($data as $k => $v) { ?>
                		<tr>
							<td><a href="/platform-api/mobile_payment_method/modify?id=<?php echo $v->payment_id ?>"><?php echo $v->payment_id ?></a></td>
							<td><?php echo $v->method_name?></td>
							<td><?php echo $v->pay_type?></td>
							<td><?php echo $v->pay_lib?></td>
						</tr>
    				<?php } ?>  
        		</tbody>
			</table>
		</div>
	</div>
</div>