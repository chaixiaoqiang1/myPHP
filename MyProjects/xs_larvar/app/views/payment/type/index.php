
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
						 	<?php echo Lang::get('type.platform_pay_type')?>
						</li>
			</h3>
		</div>
		<div class="panel-body">
			<table class="table table-striped">
				<thead>
					<tr>
						<td><?php echo Lang::get('type.id') ?></td>
						<td><?php echo Lang::get('type.pay_type_name') ?></td>
						<td><?php echo Lang::get('type.company') ?></td>
						<td><?php echo Lang::get('type.pay_type_id') ?></td>
					</tr>
				</thead>
				<tbody>

    <?php foreach ($platform_pay_types as $k => $v) { ?>
        <tr>
						<td><?php echo $v->id ?></td>
						<td><?php echo $v->pay_type_name?></td>
						<td><?php echo $v->company?></td>
						<td><?php echo $v->pay_type_id ?></td>
					</tr>
    <?php } ?>  
        </tbody>
			</table>
		</div>
	</div>
	<div class="panel panel-success">
		<div class="panel-heading">
			<h3 class="panel-title">
				<?php echo Lang::get('type.eastblue_pay_type')?>
				</h3>
		</div>
		<div class="panel-body">
			<table class="table table-striped">
        <thead>
            <tr>
                <td><?php echo Lang::get('type.type_id') ?></td>
                <td><?php echo Lang::get('type.platform_type_id') ?></td>
                <td><?php echo Lang::get('type.pay_type_name') ?></td>
                <td><?php echo Lang::get('type.pay_type_id') ?></td>
                <td><?php echo Lang::get('type.company') ?></td>
                <td><?php echo Lang::get('type.platform_id') ?></td>
                <td><?php echo Lang::get('type.created_at') ?></td>    
                <td><?php echo Lang::get('type.updated_at') ?></td>            
            </tr>
        </thead>
        <tbody>

    <?php foreach (PayType::where("platform_id", "=", $platform->platform_id)->orderBy('type_id','asc')->paginate(20) as $k => $v) { ?>
        <tr>
            <td><?php echo $v->type_id ?></td>
            <td><?php echo $v->platform_type_id ?></td>
            <td><a href="/pay-type/<?php echo $v->type_id ?>/edit"><?php echo $v->pay_type_name ?></a></td>
            <td><?php echo $v->pay_type_id ?></td>
            <td><?php echo $v->company?></td>
            <td><?php echo $v->platform_id?></td>            
            <td><?php echo $v->created_at?></td>
            <td><?php echo $v->updated_at?></td>
        </tr>
    <?php } ?>  
        </tbody>
    </table>
		</div>
	</div>
</div>
