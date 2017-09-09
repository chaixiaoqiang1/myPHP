<div class="col-xs-12" ng-controller="searchServerPlayerController">
	<div class="row">
		<div class="eb-content">
			<form action="/game-server-api/player" method="post" role="form"
				ng-submit="processFrom('/game-server-api/player')"
				onsubmit="return false;">
                <p class="text-danger"><?php echo Lang::get('player.query_notice')?></p>
				<div class="form-group">
					<select class="form-control" name="server_id"
						id="select_game_server" ng-model="formData.server_id"
						ng-init="formData.server_id=<?php echo $server_init?$server_init:0; ?>">
						<option value="0"><?php echo Lang::get('player.all_servers') ?></option>
						<?php foreach ($servers as $k => $v) { ?>
						<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
					</select>
				</div>

				<div class="form-group">
					<select class="form-control" name="choice" id="select_choice"
						ng-model="formData.choice" ng-init="formData.choice=<?php echo $server_init==0 ? 0:1; ?>">
						<option value="0"><?php echo Lang::get('player.select_by_player_name') ?></option>
						<option value="1"><?php echo Lang::get('player.select_by_player_id') ?></option>
					</select>
				</div>
				<div class="form-group">
					<input type="text" class="form-control" id="id_or_name"
						placeholder="<?php echo Lang::get('player.enter_id_or_name') ?>"
						required ng-trim="false" ng-model="formData.id_or_name" name="id_or_name" 
						ng-init="formData.id_or_name=<?php echo $player_id==0?'':$player_id; ?>" />
				</div>
				<p><font color="red">注意：由于游戏中有些玩家的昵称是以空格开头的，所以输入的时候要注意输入参数的前后空格</font></p>
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
				<div class="panel-heading"><?php echo Lang::get('player.show_player') ?></div>
				<div class="panel-body">
					<dl class="dl-horizontal">
						<dt><?php echo Lang::get('player.player_id')?></dt>
						<dd>{{player.player_id}}</dd>
						<dt><?php echo Lang::get('player.name')?></dt>
						<dd>{{player.name}}</dd>
						<dt><?php echo Lang::get('player.which_server')?></dt>
						<dd>{{player.which_server}}</dd>
						<dt><?php echo Lang::get('player.main_server')?></dt>
						<dd>{{player.main_server}}</dd>
						<dt><?php echo Lang::get('player.level')?></dt>
						<dd>{{player.level}}</dd>
						<dt><?php echo Lang::get('player.vip_level')?></dt>
						<dd>{{player.vip_level}}</dd>
						<dt><?php echo Lang::get('player.rank')?></dt>
						<dd>{{player.rank}}</dd>
						<dt><?php echo Lang::get('player.league_id')?></dt>
						<dd>{{player.league_id}}</dd>
						<dt><?php echo Lang::get('player.league_name')?></dt>
						<dd>{{player.league_name}}</dd>

						<dt><?php echo Lang::get('player.tili')?></dt>
						<dd>{{player.tili}}</dd>
						<dt><?php echo Lang::get('player.shengwang')?></dt>
						<dd>{{player.shengwang}}</dd>
						<dt><?php echo Lang::get('player.yueli')?></dt>
						<dd>{{player.yueli}}</dd>
						<dt><?php echo Lang::get('player.lingshi')?></dt>
						<dd>{{player.lingshi}}</dd>
						<dt><?php echo Lang::get('player.qiyundian')?></dt>
						<dd>{{player.jingjiedian}}</dd>

						<dt><?php echo Lang::get('player.exp')?></dt>
						<dd>{{player.exp}}</dd>
						<dt><?php echo Lang::get('player.yuanbao')?></dt>
						<dd>{{player.yuanbao}}</dd>
						<dt><?php echo Lang::get('player.power')?></dt>
						<dd>{{player.nei_li}}</dd>
						<dt><?php echo Lang::get('player.xinfa')?></dt>
						<dd>{{player.xian_ling}}</dd>
						
						<dt><?php echo Lang::get('player.tongqian')?></dt>
						<dd>{{player.tongqian}}</dd>
						<dt><?php echo Lang::get('player.active')?></dt>
						<dd>{{player.active}}</dd>
						<dt><?php echo Lang::get('player.first_login')?></dt>
						<dd ng-if="player.first_login">{{player.first_login}}</dd>
						<dd ng-if="player.first_login == null">{{player.first_login}}</dd>
						<dt><?php echo Lang::get('player.last_login')?></dt>
						<dd ng-if="player.last_login">{{player.last_login}}</dd>
						<dd ng-if="player.last_login == null">{{player.last_login}}</dd>

						<dt><?php echo Lang::get('player.created_ip')?></dt>
						<dd ng-if="player.created_ip">{{player.created_ip}}</dd>
						<dd ng-if="player.created_ip == null">{{player.created_ip}}</dd>
						<dt><?php echo Lang::get('player.last_visit_ip')?></dt>
						<dd ng-if="player.last_visit_ip">{{player.last_visit_ip}}</dd>
						<dd ng-if="player.last_visit_ip == null">{{player.last_visit_ip}}</dd>

						<dt><?php echo Lang::get('player.is_online')?></dt>
						<dd>{{player.is_online}}</dd>
						<dt><?php echo Lang::get('player.nickname')?></td>
						<dd>{{player.nickname}}</dd>
						<dt><?php echo Lang::get('player.login_email')?></dt>
						<dd>{{player.login_email}}</dd>
						<dt><?php echo Lang::get('player.uid')?></dt>
						<dd>{{player.uid}}</dd>
						<dt><?php echo Lang::get('player.facebook_id')?></dt>
						<dd>{{player.tp_user_id}}</dd>
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