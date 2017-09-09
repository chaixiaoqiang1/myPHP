<h3>Revenue</h3>
<br/>
<br/>

<table cellpadding="10" cellspacing="0" border="1">
	<thead>
		<tr bgcolor="#5bc0de">
			<td><?php echo Lang::get('system.report_server_name')?></td>
			<td><?php echo Lang::get('system.report_history') ?></td>
			<td>
				<?php echo Lang::get('system.report_today') ?>
				<?php echo date('Y-m-d', strtotime('-1 day')) ?>
			</td>
			<td><?php echo Lang::get('system.report_this_month')?></td>
			<td><?php echo Lang::get('system.report_prev_one_month')?></td>
			<td><?php echo Lang::get('system.report_prev_two_month')?></td>
			<td><?php echo Lang::get('system.report_prev_three_month')?></td>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($revenue as $v) { ?>
		<tr>
		<td><?php echo $v->server_name?></td>	
		<td><?php echo $v->total_dollar_amount_all ?></td>
		<td><?php echo $v->total_dollar_amount_day ?></td>
		<td><?php echo $v->total_dollar_amount_month ?></td>
		<td><?php echo $v->total_dollar_amount_month_last ?> </td>
		<td><?php echo $v->total_dollar_amount_month_ll ?></td>
		<td><?php echo $v->total_dollar_amount_month_lll ?></td>
		</tr>
		<?php } ?>
	</tbody>
</table>

<table cellpadding="10" cellspacing="0" border="1">
	<thead>
		<tr bgcolor="#5bc0de">
			<td><b><?php echo Lang::get("slave.start_time");?></b></td>
			<td><b><?php echo Lang::get("slave.end_time");?></b></td>
			<td><b><?php echo Lang::get("slave.pay_type");?></b></td>
			<td><b><?php echo Lang::get("slave.pay_method");?></b></td>
			<td><b><?php echo Lang::get("slave.money_flow_name");?></b></td>
			<td><b><?php echo Lang::get("slave.pay_amount_dollar");?></b></td>
			<td><b><?php echo Lang::get("slave.pay_type_method_rate");?></b></td>
			<td><b><?php echo Lang::get("slave.get_payment_count");?></b></td>
			<td><b><?php echo Lang::get("slave.all_order_count");?></b></td>
			<td><b><?php echo Lang::get("slave.get_payment_rate");?></b></td>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($channels as $key => $v) { ?>
			<tr>
				<td><?php echo $v->pay_time_first ?></td>
				<td><?php echo $v->pay_time_last ?></td>
				<td><?php echo $v->pay_type_name ?></td>
				<td><?php echo $v->pay_method_name ?></td>
				<td><?php echo $v->money_flow_name ?></td>
				<td><?php echo $v->total_dollar_amount ?></td>
				<td><?php echo $v->amount_rate ?>%</td>
				<td><?php echo $v->get_payment_count ?></td>
				<td><?php echo $v->count ?></td>
				<td><?php echo $v->get_payment_rate ?>%</td>
			</tr>
		<?php }?>
	</tbody>

</table>

