<!DOCTYPE html>
<html>
<meta charset="UTF-8">
<body>
<table>
	<?php //支付美金信息 ?>
	<tr>
		<th><?php echo Lang::get('slave.pay_dollar_info') ?></th>
	</tr>
	<tr>
		<th><?php echo Lang::get('slave.server_name') ?></th>
		<th><?php echo Lang::get('slave.open_server_time') ?></th>
		<th><?php echo Lang::get('slave.first_pay_time') ?></th>
		<?php for ($i = $data['days_start']; $i <= $data['days_end']; $i++) { ?>
		<th><?php echo $i ?></th>
		<?php } ?>	
	</tr>
	<?php foreach ($data['servers_payment'] as $v) { ?>
	<tr>
		<td><?php echo $v->server_name?></td>
		<td><?php echo date('Y-m-d', $v->open_server_time) ?></td>
		<td>
			<?php if ($v->pay_time) {?>
			<?php echo date('Y-m-d', $v->pay_time) ?>
			<?php } ?>
		</td>
			<?php foreach ($v->days as $vv) {?> 
			<td><?php echo $vv->dollar ?></td>
			<?php } ?>
	</tr>
	<?php } ?>
	<?php if($data['all_data']){ //本文件适用于两个功能，如果此参数为0，则不显示以下内容 ?>
		<tr></tr>
		<tr></tr>
		<?php //付费用户数信息 ?>
		<tr>
			<th><?php echo Lang::get('slave.pay_user_num_info') ?></th>
		</tr>
		<tr>
			<th><?php echo Lang::get('slave.server_name') ?></th>
			<th><?php echo Lang::get('slave.open_server_time') ?></th>
			<th><?php echo Lang::get('slave.first_pay_time') ?></th>
			<?php for ($i = $data['days_start']; $i <= $data['days_end']; $i++) { ?>
			<th><?php echo $i ?></th>
			<?php } ?>	
		</tr>
		<?php foreach ($data['servers_payment'] as $v) { ?>
		<tr>
			<td><?php echo $v->server_name?></td>
			<td><?php echo date('Y-m-d', $v->open_server_time) ?></td>
			<td>
				<?php if ($v->pay_time) {?>
				<?php echo date('Y-m-d', $v->pay_time) ?>
				<?php } ?>
			</td>
				<?php foreach ($v->days as $vv) {?> 
				<td><?php echo $vv->user_num ?></td>
				<?php } ?>
		</tr>
		<?php } ?>
		<tr></tr>
		<tr></tr>
		<?php //arppu信息 ?>
		<tr>
			<th><?php echo Lang::get('slave.ARPPU_info') ?></th>
		</tr>
		<tr>
			<th><?php echo Lang::get('slave.server_name') ?></th>
			<th><?php echo Lang::get('slave.open_server_time') ?></th>
			<th><?php echo Lang::get('slave.first_pay_time') ?></th>
			<?php for ($i = $data['days_start']; $i <= $data['days_end']; $i++) { ?>
			<th><?php echo $i ?></th>
			<?php } ?>	
		</tr>
		<?php foreach ($data['servers_payment'] as $v) { ?>
		<tr>
			<td><?php echo $v->server_name?></td>
			<td><?php echo date('Y-m-d', $v->open_server_time) ?></td>
			<td>
				<?php if ($v->pay_time) {?>
				<?php echo date('Y-m-d', $v->pay_time) ?>
				<?php } ?>
			</td>
				<?php foreach ($v->days as $vv) {?> 
				<td><?php echo round($vv->dollar / ($vv->user_num ? $vv->user_num : 1), 2) ?></td>
				<?php } ?>
		</tr>
		<?php } ?>
		<tr></tr>
		<tr></tr>
		<?php //创建信息 ?>
		<tr>
			<th><?php echo Lang::get('slave.create_info') ?></th>
		</tr>
		<tr>
			<th><?php echo Lang::get('slave.server_name') ?></th>
			<th><?php echo Lang::get('slave.open_server_time') ?></th>
			<?php for ($i = $data['days_start']; $i <= $data['days_end']; $i++) { ?>
			<th><?php echo $i ?></th>
			<?php } ?>
		</tr>
		<?php foreach ($data['servers_log'] as $v) { ?>
		<tr>
			<td><?php echo $v['server_name'] ?></td>
			<td><?php echo date('Y-m-d', $v['open_server_time']) ?></td>
			<?php for ($i = $data['days_start']; $i <= $data['days_end']; $i++) { ?>
			<th><?php echo isset($v['num_info'][$i]['create_num']) ? $v['num_info'][$i]['create_num'] : '' ?></th>
			<?php } ?>
		</tr>
		<?php } ?>
		<tr></tr>
		<tr></tr>
		<?php //dau信息 ?>
		<tr>
			<th><?php echo Lang::get('slave.dau_info') ?></th>
		</tr>
		<tr>
			<th><?php echo Lang::get('slave.server_name') ?></th>
			<th><?php echo Lang::get('slave.open_server_time') ?></th>
			<?php for ($i = $data['days_start']; $i <= $data['days_end']; $i++) { ?>
			<th><?php echo $i ?></th>
			<?php } ?>
		</tr>
		<?php foreach ($data['servers_log'] as $v) { ?>
		<tr>
			<td><?php echo $v['server_name'] ?></td>
			<td><?php echo date('Y-m-d', $v['open_server_time']) ?></td>
			<?php for ($i = $data['days_start']; $i <= $data['days_end']; $i++) { ?>
			<th><?php echo isset($v['num_info'][$i]['login_num']) ? $v['num_info'][$i]['login_num'] : '' ?></th>
			<?php } ?>
		</tr>
		<?php } ?>
		<tr></tr>
		<tr></tr>
		<?php //留存信息 ?>
		<tr>
			<th><?php echo Lang::get('slave.first_4_days_retention_info') ?></th>
		</tr>
		<tr>
			<th><?php echo Lang::get('slave.server_name') ?></th>
			<th><?php echo Lang::get('slave.open_server_time') ?></th>
			<?php foreach (array('created_player_number', 'days_2', 'days_3', 'days_4', 'days_5', 'days_6', 'days_7', 'days_14') as $value) {
			 ?>
			<th><?php echo $value ?></th>
			<?php 
				unset($value);
				}?>
		</tr>
		<?php foreach ($data['servers_log'] as $v) { ?>
		<tr>
			<td><?php echo $v['server_name'] ?></td>
			<td><?php echo date('Y-m-d', $v['open_server_time']) ?></td>
			<?php foreach ($v['retention_info'] as $key => $value) {
			 ?>
			<th><?php echo $value.'('.round(round($value/(isset($v['retention_info']['created_player_number']) ? $v['retention_info']['created_player_number'] : 1)*100, 4), 2); ?>%)</th>
			<?php 
				}
			?>
		</tr>
		<?php } 
	}?>
</table>
</body>
</html>