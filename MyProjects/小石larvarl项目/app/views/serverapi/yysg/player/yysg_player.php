<div class="col-xs-12" ng-controller="searchServerPlayerController">
	<div class="row">
		<div class="eb-content">
			<form action="/game-server-api/yysg/player" method="post" role="form"
				ng-submit="processFrom('/game-server-api/yysg/player')"
				onsubmit="return false;">
				<div class="form-group">
					<select class="form-control" name="server_id"
						id="select_game_server" ng-model="formData.server_id"
						ng-init="formData.server_id=<?php echo $server_init?$server_init:0 ; ?>">
						<option value="0"><?php echo Lang::get('serverapi.select_server')?></option>
						<?php foreach ($servers as $k => $v) { ?>
						<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
					</select>
				</div>

				<?php if('yysg' == $game_code){ ?>
				<div class="form-group">
					<select class="form-control" name="choice" id="select_choice"
						ng-model="formData.choice" ng-init="formData.choice=<?php echo $player_id==''?0:1; ?>">
						<option value="0"><?php echo Lang::get('player.select_by_player_name') ?></option>
						<option value="1"><?php echo Lang::get('player.select_by_player_id') ?></option>
					</select>
				</div>
				<?php } else{ ?>
				<b><?php if('mnsg' == $game_code) echo Lang::get('serverapi.select_server_by_name') ?></b>
				<div class="form-group">
					<select class="form-control" name="choice" id="select_choice"
						ng-model="formData.choice" ng-init="formData.choice=1">
						<option value="0"><?php echo Lang::get('player.select_by_player_name') ?></option>
						<option value="1"><?php echo Lang::get('player.select_by_player_id') ?></option>
					</select>
				</div>
				<?php } ?>
				<div class="form-group">
					<input type="text" class="form-control" id="id_or_name"
						placeholder="<?php echo Lang::get('player.enter_id_or_name') ?>"
						required ng-model="formData.id_or_name" name="id_or_name" ng-init="formData.id_or_name=<?php echo $player_id==0?'':$player_id; ?>" />
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
		<div ng-repeat="player in players">
			<div class="panel panel-success">
						<div class="panel-heading">
							<?php echo Lang::get('player.show_player') ?>
							<a href="/game-server-api/player/login?server_init={{formData.server_id}}&player_id={{player.player_id}}" target="view_window1"><?php echo Lang::get('player.player_query') ?></a>
							<a href="/slave-api/economy/yysg/player?server_init={{formData.server_id}}&player_id={{player.player_id}}" target="view_window2"><?php echo Lang::get('player.player_consume_data') ?></a>
							<a href="/game-server-api/mnsg/log/item?player_id={{player.player_id}}" target="view_window3"><?php echo Lang::get('player.player_prop_query') ?></a>
							<a href="/game-server-api/yysg/logindevice?uid={{player.uid}}" target="view_window4"><?php echo Lang::get('player.player_device_query') ?></a>
						</div>
						
				<div class="panel-body">
					<dl class="dl-horizontal">
						<dt><?php echo Lang::get('player.player_id')?></dt>
						<dd>{{player.player_id}}</dd>
						<dt><?php echo Lang::get('player.player_name')?></dt>
						<dd>{{player.player_name}}</dd>
						<dt><?php echo Lang::get('slave.vip_level')?></dt>
						<dd>{{player.vip}}</dd>
						<dt><?php echo Lang::get('player.which_server')?></dt>
						<dd>{{player.which_server}}</dd>
						<dt><?php echo Lang::get('player.level')?></dt>
						<dd>{{player.level}}</dd>
						<dt><?php echo Lang::get('player.rank')?></dt>
						<dd>{{player.rank}}</dd>
						<dt><?php echo Lang::get('player.exp')?></dt>
						<dd>{{player.exp}}</dd>
						<dt><?php echo Lang::get('player.yuanbao')?></dt>
						<dd>{{player.yuanbao}}</dd>
						<dt><?php echo Lang::get('player.tongqian')?></dt>
						<dd>{{player.tongqian}}</dd>
						<dt><?php echo Lang::get('player.energy')?></dt>
						<dd>{{player.energy}}</dd>
						<?php if('mnsg' == $game_code){ ?>
							<dt><?php echo Lang::get('slave.arena_coin')?></dt>
							<dd>{{player.arena_coin}}</dd>
							<dt><?php echo Lang::get('slave.march_coin')?></dt>
							<dd>{{player.march_coin}}</dd>
							<dt><?php echo Lang::get('slave.region_coin'); ?></dt>
							<dd>{{player.region_coin}}</dd>
							<dt><?php echo Lang::get('slave.guild_id')?></dt>
							<dd>{{player.guild_id}}</dd>
						<?php } ?>
						<?php if('yysg' == $game_code){ ?>
							<dt><?php echo Lang::get('player.point')?></dt>
							<dd>{{player.point}}</dd>
							<dt><?php echo Lang::get('player.glory')?></dt>
							<dd>{{player.glory}}</dd>
							<dt><?php echo Lang::get('player.player_location')?></dt>
							<dd>{{player.player_location}}</dd>
						<?php } ?>
						<dt><?php echo Lang::get('player.social')?></dt>
						<dd>{{player.social}}</dd>
						<dt><?php echo Lang::get('player.invitation')?></dt>
						<dd>{{player.invitation}}</dd>
						<dt><?php echo Lang::get('player.phone_model')?></dt>
						<dd>{{player.device_type}}</dd>
						<dt><?php echo Lang::get('player.last_login')?></dt>
						<dd>{{player.last_login}}</dd>
						<dt><?php echo Lang::get('player.active')?></dt>
						<dd>{{player.active}}</dd>
						<dt><?php echo Lang::get('slave.player_created_time')?></dt>
						<dd>{{player.player_time}}</dd>
						<dt><?php echo Lang::get('player.created_ip')?></dt>
						<dd ng-if="player.created_ip">{{player.created_ip}}</dd>
						<dd ng-if="player.created_ip == null">{{player.created_ip}}</dd>
						<dt><?php echo Lang::get('player.last_visit_ip')?></dt>
						<dd ng-if="player.last_visit_ip">{{player.last_visit_ip}}</dd>
						<dd ng-if="player.last_visit_ip == null">{{player.last_visit_ip}}</dd>
						<dt><?php echo Lang::get('player.uid')?></dt>
						<dd><a href="/slave-api/payment/order?uid={{player.uid}}" target="{{player.uid}}_blank">{{player.uid}}</a></dd>
						<dt><?php echo Lang::get('player.nickname')?></dt>
						<dd>{{player.nickname}}</dd>
						<dt><?php echo Lang::get('player.login_email')?></dt>
						<dd>{{player.login_email}}</dd>
						<dt><?php echo Lang::get('player.amount_recharge')?></dt>
						<dd>{{player.all_pay_amount}} $</dd>
						<dt><?php echo Lang::get('player.times_recharge')?></dt>
						<dd>{{player.all_pay_times}}</dd>
						<dt><?php echo Lang::get('player.average_rechage')?></dt>
						<dd>{{player.avg_amount}} $</dd>
						<dt><?php echo Lang::get('player.first_recharge_level')?></dt>
						<dd>{{player.first_lev}}</dd>
						<?php if (Auth::user()->is_admin || Auth::user()->department_id == Department::ID_SHICHANG) { ?>
						<dt>u1:<dt>
						<dd>{{player.u}}</dd>
						<dt>u2:</dt>
						<dd>{{player.u2}}</dd>
						<dt>source:</dt>
						<dd>{{player.source}}</dd>
						<dt>is_anonymous:</dt>
						<dd>{{player.is_anonymous}}</dd>
						<?php }?>
					</dl>
				</div>
			</div>
		</div>
	</div>
</div>