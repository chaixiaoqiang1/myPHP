<div class="col-xs-12" ng-controller="SXDGetPlatformUserController">
	<div class="row">
		<div class="eb-content">
			<form action="/platform-api/user" method="post" role="form"
				ng-submit="processFrom('/platform-api/user')"
				onsubmit="return false;">
				<div class="form-group">
					<select class="form-control" name="choice" id="select_choice"
						ng-model="formData.choice" ng-init="formData.choice=0">
						<option value="0"><?php echo Lang::get('player.select_by_email_or_usernickname') ?></option>
						<option value="1"><?php echo Lang::get('player.select_by_uid') ?></option>
					</select>
				</div>
				<div class="form-group">
					<input type="text" class="form-control" id="select_email_or_uid"
						placeholder="<?php echo Lang::get('platformapi.enter_email_or_uid') ?>"
						required ng-model="formData.email_or_uid" name="email_or_uid" />
						
				</div>
				<input type="submit" class="btn btn-default"
					value="<?php echo Lang::get('basic.btn_submit') ?>" />
			</form>
		</div>
		<!-- /.col -->
	</div>
	<div class="row margin-top-10">
		<div class="eb-content">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
	<div class="row margin-top-10 col-xs-6">
		<div class="panel panel-success">
			<div class="panel-heading"><?php echo Lang::get('platformapi.show_user') ?></div>
			<div class="panel-body">
				<dl class="dl-horizontal">
					<dt><?php echo Lang::get('platformapi.user_email')?></dt>
					<dd>{{user.login_email}}</dd>
					<dt><?php echo Lang::get('platformapi.user_uid')?></dt>
					<dd>{{user.uid}}</dd>
					<dt><?php echo Lang::get('platformapi.user_name')?></dt>
					<dd>{{user.name}}</dd>
					<dt><?php echo Lang::get('platformapi.user_nickname')?></dt>
					<dd>{{user.nickname}}</dd>
					<dt><?php echo Lang::get('platformapi.user_contact_email')?></dt>
					<dd>{{user.contact_email}}</dd>
					<dt><?php echo Lang::get('platformapi.user_created_time')?></dt>
					<dd>{{user.created_time}}</dd>
					<dt><?php echo Lang::get('platformapi.user_last_visit_time')?></dt>
					<dd>{{user.last_visit_time}}</dd>
					<dt><?php echo Lang::get('platformapi.user_created_ip')?></dt>
					<dd>{{user.created_ip}}</dd>
					<dt><?php echo Lang::get('platformapi.user_last_visit_ip')?></dt>
					<dd>{{user.last_visit_ip}}</dd>
					<dt><?php echo Lang::get('platformapi.nums_created_player')?></dt>
					<dd>{{user.nums_created_player}}</dd>
					<?php if (Auth::user()->is_admin || Auth::user()->department_id == Department::ID_SHICHANG) { ?>
					<dt>u1:
					<dt>
					<dd>{{user.u}}</dd>
					<dt>u2:</dt>
					<dd>{{user.u2}}</dd>
					<dt>source:</dt>
					<dd>{{user.source}}</dd>
					<dt>is_anonymous:</dt>
					<dd>{{user.is_anonymous}}</dd>
					<?php }?>
				</dl>
			</div>
		</div>
	</div>
	<div class="col-xs-12">

		<table class="table table-striped">
			<thead>
				<tr class="info">
					<td><b><?php echo Lang::get('player.player_id');?></b></td>
					<td><b><?php echo Lang::get('player.player_name');?></b></td>
					<td><b><?php echo Lang::get('player.which_server');?></b></td>
					<td><b><?php echo Lang::get('player.created_time');?></b></td>
					<td><b><?php echo Lang::get('player.amount_recharge');?></b></td>
					<td><b><?php echo Lang::get('player.times_recharge');?></b></td>
					<td><b><?php echo Lang::get('player.average_rechage');?></b></td>
					<td><b><?php echo Lang::get('player.last_login');?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in created_players">
					<td>{{t.player_id}}</td>
					<td>{{t.player_name}}</td>
					<td>{{t.server_id}}</td>
					<td>{{t.created_time}}</td>
					<td>{{t.all_pay_amount}}</td>
					<td>{{t.all_pay_times}}</td>
					<td>{{t.avg_amount}}</td>
					<td>{{t.last_login}}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>