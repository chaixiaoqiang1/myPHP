<html>
<body>
<h3>game_id为<?php echo $game_id ?>的游戏在<?php echo $start_time ?>至<?php echo $end_time ?>期间发现的异常订单如下：</h3>
<table cellpadding="10" cellspacing="0" border="1">
	<thead>
		<tr bgcolor="#5bc0de">
			<td><?php echo Lang::get('system.order_sn') ?></td>
			<td>pay_type_id</td>
			<td>method_id</td>
			<td>tradeseq</td>
			<td>UID</td>
			<td>玩家支付值</td>
			<td>订单创建时间</td>
			<td>支付时间</td>
			<td>是否支付</td>
			<td>应得元宝值</td>
			<td>礼包ID</td>
			<td>合并订单</td>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($mail_data as $value) { ?>
		<tr>
			<td><?php echo $value['order_sn']?></td>
			<td><?php echo $value['pay_type_id']?></td>
			<td><?php echo $value['method_id']?></td>
			<td><?php echo $value['tradeseq']?></td>
			<td><?php echo $value['pay_user_id']?></td>
			<td><?php echo $value['pay_amount']?></td>
			<td><?php echo $value['create_time']?></td>
			<td><?php echo $value['pay_time']?></td>
			<td><?php echo $value['get_payment']?></td>
			<td><?php echo $value['yuanbao_amount']?></td>
			<td><?php echo $value['giftbag_id']?></td>
			<td><?php echo $value['combined_order']?></td>
		</tr>
		<?php } ?>
	</tbody>
</table>
</body>
</html>