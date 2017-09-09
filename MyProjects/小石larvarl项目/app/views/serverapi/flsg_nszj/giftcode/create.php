<div class="col-xs-12" ng-controller="createGiftCodeController">
	<div class="row" >
		<div class="eb-content">
			<form action="/game-server-api/gift-code/create" method="post" role="form" ng-submit="processFrom('/game-server-api/gift-code/create')" onsubmit="return false;">
				<div class="form-group">
					<select class="form-control" name="gift_type" id="form_type" ng-model="formData.type" ng-init="formData.type=0">
						<option value="0"><?php echo Lang::get('serverapi.select_gift_type') ?></option>
						<?php foreach ($gifts as $k => $v) { ?>
						<option value="<?php echo $v->id?>"><?php echo $v->id."-".$v->name;?></option>
						<?php } ?>		
					</select>
				</div>		
				<div class="form-group">
					<input type="text" class="form-control" placeholder="<?php echo Lang::get('serverapi.enter_gift_num')?>" ng-model="formData.num" name="num"?>
				</div>	

				<input type="submit" class="btn btn-default" value="<?php echo Lang::get('basic.btn_submit') ?>"/>	
			</form>	 
		</div><!-- /.col -->
	</div>
	<div class="row margin-top-10">
		<div class="eb-content">
			<alert ng-repeat="alert in alerts" type="alert.type" close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
	<div class="row margin-top-10 ">
		<div class="col-xs-6"> 
			<table class="table table-striped">
			<thead>
				<tr class="info">
					<td><?php echo Lang::get('serverapi.gift_code');?></td>
					<td><?php echo Lang::get('serverapi.gift_type');?></td>
				</tr>
			</thead>
			
			<tbody>
				<tr ng-repeat="code in codes">
				<td>{{code}}</td>
				<td>{{code_name}}</td>
				</tr>
			</tbody>
			</table>	
		</div>
	</div>
</div>