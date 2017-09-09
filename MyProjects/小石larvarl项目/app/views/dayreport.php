<h3>Retention</h3>
<?php if(isset($is_yysg) && $is_yysg){ ?>
<table cellpadding="10" cellspacing="0" border="1">
	<thead>
		<tr bgcolor="#5bc0de">
			<td><?php echo Lang::get('slave.statistics_date') ?></td>
			<td><?php echo Lang::get('slave.create_player_number') ?></td>
			<?php for($i=2; $i<8; $i++) {?>
			<td><?php echo $i.Lang::get('system.report_day_retention')?></td>
			<?php }?>
			<td><?php echo '14'.Lang::get('system.report_day_retention')?></td>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($retention as $server_retention) {
			foreach ($server_retention as $v){ ?>
		<tr>
		<td><?php echo $v['retention_time'] ?> </td>
		<td><?php echo $v['created_player_number'] ?> </td>
		<?php if($v['created_player_number'] == '0'){ ?>
			<td>0</td>
			<td>0</td>
			<td>0</td>
			<td>0</td>
			<td>0</td>
			<td>0</td>
			<td>0</td>
			</tr>		
		<?php }else{ ?>
			<td><?php echo $v['days_2']; ?>(<?php echo round($v['days_2']*100/$v['created_player_number'], 2); ?>%)</td>
			<td><?php echo $v['days_3']; ?>(<?php echo round($v['days_3']*100/$v['created_player_number'], 2); ?>%)</td>
			<td><?php echo $v['days_4']; ?>(<?php echo round($v['days_4']*100/$v['created_player_number'], 2); ?>%)</td>
			<td><?php echo $v['days_5']; ?>(<?php echo round($v['days_5']*100/$v['created_player_number'], 2); ?>%)</td>
			<td><?php echo $v['days_6']; ?>(<?php echo round($v['days_6']*100/$v['created_player_number'], 2); ?>%)</td>
			<td><?php echo $v['days_7']; ?>(<?php echo round($v['days_7']*100/$v['created_player_number'], 2); ?>%)</td>
			<td><?php echo $v['days_14']; ?>(<?php echo round($v['days_14']*100/$v['created_player_number'], 2); ?>%)</td>
			</tr>
		<?php }
			} 
		}?>
	</tbody>
</table>
<?php }else{ ?>
<table cellpadding="10" cellspacing="0" border="1">
	<thead>
		<tr bgcolor="#5bc0de">
			<td><?php echo Lang::get('system.report_start_time') ?></td>
			<td><?php echo Lang::get('system.report_end_time') ?></td>
			<td><?php echo Lang::get('system.report_server_name')?></td>
			<?php for($i=2; $i<8; $i++) {?>
			<td><?php echo Lang::get('system.report_day_retention', array('day' => $i))?></td>
			<?php }?>
			<td><?php echo Lang::get('system.report_day_retention', array('day' => 14))?></td>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($retention as $v) {?>
		<tr>
		<td><?php echo $v->start_time ?> </td>
		<td><?php echo $v->end_time ?> </td>
		<td><?php echo $v->server_name ?></td>
		<td><?php echo $v->rate_2?>%</td>
		<td><?php echo $v->rate_3?>%</td>
		<td><?php echo $v->rate_4?>%</td>
		<td><?php echo $v->rate_5?>%</td>
		<td><?php echo $v->rate_6?>%</td>
		<td><?php echo $v->rate_7?>%</td>
		<td><?php echo $v->rate_14?>%</td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<?php } ?>

<br/>
<br/>
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
<br/>
<br/>
<h3>Levels<h3>
<br/>
<br/>
<table cellpadding="5" cellspacing="0" border="1">
	<thead>
		<tr bgcolor="#5bc0de">
			<td><?php echo Lang::get('system.report_server_name')?></td>
			<?php foreach(array(1,2,3,4,5,6,7,8,9,10,20,30,40,50,60,70,80,90) as $v) {?>
			<td><?php echo 'Level' . $v ?></td>
			<?php } ?>
		</tr>
		<tbody>
			
			<?php foreach ($levels as $v) { ?>
			<tr>
				<td><?php echo $v->server_name ?></td>
				<td><?php echo $v->rate_1?>%</td>
				<td><?php echo $v->rate_2?>%</td>
				<td><?php echo $v->rate_3?>%</td>
				<td><?php echo $v->rate_4?>%</td>
				<td><?php echo $v->rate_5?>%</td>
				<td><?php echo $v->rate_6?>%</td>
				<td><?php echo $v->rate_7?>%</td>
				<td><?php echo $v->rate_8?>%</td>
				<td><?php echo $v->rate_9?>%</td>
				<td><?php echo $v->rate_10?>%</td>
				<td><?php echo $v->rate_20?>%</td>
				<td><?php echo $v->rate_30?>%</td>
				<td><?php echo $v->rate_40?>%</td>
				<td><?php echo $v->rate_50?>%</td>
				<td><?php echo $v->rate_60?>%</td>
				<td><?php echo $v->rate_70?>%</td>
				<td><?php echo $v->rate_80?>%</td>
				<td><?php echo $v->rate_90?>%</td>
			</tr>
			<?php } ?>	
		</tbody>
	</thead>
</table>
<br/>
<br/>
<h3>Login</h3>
<br/>
<br/>

<table cellpadding="10" cellspacing="0" border="1">
	<thead>
		<tr bgcolor="#5bc0de">
			<td><?php echo Lang::get('system.report_server_name')?></td>
			<td>T-6</td>
			<td>T-5</td>
			<td>T-4</td>
			<td>T-3</td>
			<td>T-2</td>
			<td>T-1</td>
			<td>T</td>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($login as $v) { ?>
		<tr>
			<td><?php echo $v->server_name ?></td>
			<td><?php echo $v->t_6 ?></td>
			<td><?php echo $v->t_5 ?></td>
			<td><?php echo $v->t_4 ?></td>
			<td><?php echo $v->t_3 ?></td>
			<td><?php echo $v->t_2 ?></td>
			<td><?php echo $v->t_1 ?></td>
			<td><?php echo $v->t_0 ?></td>
		</tr>
		<?php } ?>
	</tbody>
</table>


<?php
	if (isset($channels)) { ?>
<br/>
<br/>
<h3>Pay_type</h3>
<br/>
<br/>

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

<?php }  ?>
		