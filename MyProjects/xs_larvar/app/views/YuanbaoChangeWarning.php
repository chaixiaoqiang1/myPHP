<html>
<body>
<h3>玩家元宝变动超过正常值：</h3>
<table cellpadding="10" cellspacing="0" border="1">
	<thead>
		<tr bgcolor="#5bc0de">
			<td><?php echo Lang::get('slave.platform_name') ?></td>
			<td><?php echo Lang::get('slave.game_name') ?></td>
			<td><?php echo Lang::get('slave.server_name') ?></td>
			<td><?php echo Lang::get('slave.player_id') ?></td>
			<td><?php echo Lang::get('slave.player_name') ?></td>
			<td><?php echo Lang::get('slave.pay_user_id') ?></td>
			<td><?php echo Lang::get('slave.yuanbao_increase') ?></td>
			<td><?php echo Lang::get('slave.recharge_dollar') ?></td>
			<td><?php echo Lang::get('slave.recharge_yuanbao') ?></td>
			<td><?php echo Lang::get('slave.increase_reduce_recharge') ?></td>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($data as $game_id => $game_info) {
			foreach ($game_info as $player_id => $player_info) {
			if(!$player_info['is_internal_uid'] && $player_info['send_mail']){  ?>
		<tr>
			<td><?php echo Platform::find($player_info['platform_id'])->platform_name; ?></td>
			<td><?php echo Game::find($game_id)->game_name; ?></td>
			<td><?php $server = Server::where('game_id', $game_id)->where('server_internal_id', $player_info['server_internal_id'])->first(); 
				if($server){ echo $server->server_name;}else{echo "";} ?></td>
			<td><?php echo $player_id; ?></td>
			<td><?php echo $player_info['player_name']; ?></td>
			<td><?php echo $player_info['uid']?></td>
			<td><?php echo $player_info['increase']?></td>
			<td><?php echo $player_info['dollar']?></td>
			<td><?php echo $player_info['recharge_yuanbao']?></td>
			<td><?php echo ($player_info['increase'] - $player_info['recharge_yuanbao']);?></td>
		</tr>
		<?php }
			}
		} ?>
	</tbody>
</table>
<br>
<h3>内玩元宝变动超过正常值：</h3>
<table cellpadding="10" cellspacing="0" border="1">
	<thead>
		<tr bgcolor="#5bc0de">
			<td><?php echo Lang::get('slave.platform_name') ?></td>
			<td><?php echo Lang::get('slave.game_name') ?></td>
			<td><?php echo Lang::get('slave.server_name') ?></td>
			<td><?php echo Lang::get('slave.player_id') ?></td>
			<td><?php echo Lang::get('slave.player_name') ?></td>
			<td><?php echo Lang::get('slave.pay_user_id') ?></td>
			<td><?php echo Lang::get('slave.yuanbao_increase') ?></td>
			<td><?php echo Lang::get('slave.recharge_dollar') ?></td>
			<td><?php echo Lang::get('slave.recharge_yuanbao') ?></td>
			<td><?php echo Lang::get('slave.increase_reduce_recharge') ?></td>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($data as $game_id => $game_info) {
			foreach ($game_info as $player_id => $player_info) {
			if($player_info['is_internal_uid'] && $player_info['send_mail']){  ?>
		<tr>
			<td><?php echo Platform::find($player_info['platform_id'])->platform_name; ?></td>
			<td><?php echo Game::find($game_id)->game_name; ?></td>
			<td><?php $server = Server::where('game_id', $game_id)->where('server_internal_id', $player_info['server_internal_id'])->first(); 
				if($server){ echo $server->server_name;}else{echo "";} ?></td>
			<td><?php echo $player_id; ?></td>
			<td><?php echo $player_info['player_name']; ?></td>
			<td><?php echo $player_info['uid']?></td>
			<td><?php echo $player_info['increase']?></td>
			<td><?php echo $player_info['dollar']?></td>
			<td><?php echo $player_info['recharge_yuanbao']?></td>
			<td><?php echo ($player_info['increase'] - $player_info['recharge_yuanbao']);?></td>
		</tr>
		<?php }
			}
		} ?>
	</tbody>
</table>
</body>
</html>