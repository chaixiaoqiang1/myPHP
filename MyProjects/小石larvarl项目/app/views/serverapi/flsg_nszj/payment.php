<div class="col-xs-12" ng-controller="mobilePayController">

    <div class="panel panel-success">
        <div class="panel-heading">
            <h3 class="panel-title">
                <?php echo Lang::get('当前平台下正在使用的支付方式(可以通过点击下面链接修改支付方式)')?>
            </h3>
        </div>

        <div class="panel-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>id</th>
                        <th>payment_id</th>
                        <th>platform_id</th>
                        <th>domain_name</th>
                        <th>pay_lib</th>
                        <th>method_name</th>
                        <th>pay_type_id</th>
                        <th>method_id</th>    
                        <th>currency</th>
                        <th>use_type</th>
                        <th>payment_type</th>
                        <th>method_order</th>
                        <th>img_source</th>
                        <th>extra</th>
                        <th>tips</th>
                        <th>special_type</th>            
                    </tr>
                </thead>

                <tbody>
                    <?php foreach (DB::table('mobile_game_payment_method')->where("platform_id", "=", $platform->platform_id)->orderBy('id','asc')->get() as $k => $v) { ?>
                        <tr>
                            <td><a href="/mobile_paytype/modify?id=<?php echo $v->id ?>"><?php echo $v->id ?></a></td>
                            <td><?php echo $v->payment_id ?></td>
                            <td><?php echo $v->platform_id ?></td>
                            <td><?php echo $v->domain_name ?></td>
                            <td><?php echo $v->pay_lib?></td>
                            <td><?php echo $v->method_name ?></td>
                            <td><?php echo $v->pay_type_id?></td>
                            <td><?php echo $v->method_id?></td>  
                            <td><?php echo $v->currency ?></td>
                            <td><?php echo $v->use_type ?></td>
                            <td><?php echo $v->payment_type ?></td>
                            <td><?php echo $v->method_order ?></td>
                            <td><?php echo $v->img_source ?></td>
                            <td><?php echo $v->extra ?></td>
                            <td><?php echo $v->tips ?></td>
                            <td><?php echo $v->special_type ?></td>
                        </tr>
                    <?php } ?>  
                </tbody>
            </table>
        </div>      
    </div>

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
						 	<?php echo Lang::get('支付方法列表').'(可以通过点击下面链接增加支付方式到当前网站)'?>
						</li>
			</h3>
		</div>
		<div class="panel-body">
			<table class="table table-striped">
				<thead>
					<tr>
						<th>payment_id</th>
						<td>method_name</td>
						<td>pay_type</td>
						<td>pay_lib</td>
					</tr>
				</thead>

				<tbody>
                	<?php foreach ($pay_type_info as $k => $v) { ?>
                		<tr>
							<td><a href="/mobile_paytype/add?id=<?php echo $v->payment_id ?>"><?php echo $v->payment_id ?></td>
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
