<div class="col-xs-12">
	<table class="table table-striped">
		<thead>
			<tr>
				<td><?php echo Lang::get('currency.currency_id') ?></td>
				<td><?php echo Lang::get('currency.currency_code') ?></td>
				<td><?php echo Lang::get('currency.currency_symbol') ?></td>
				<td><?php echo Lang::get('currency.currency_name') ?></td>
				<td><?php echo Lang::get('currency.created_at') ?></td>
				<td><?php echo Lang::get('currency.updated_at') ?></td>				
			</tr>
		</thead>
		<tbody>

	<?php foreach (Currency::orderBy('currency_id','asc')->paginate(10) as $k => $v) { ?>
		<tr>
			<td><?php echo $v->currency_id ?></td>
			<td><?php echo $v->currency_code ?></td>
			<td><?php echo $v->currency_symbol ?></td>
			<td><a href="/currency/<?php echo $v->currency_id?>/edit"><?php echo $v->currency_name ?></a></td>
			<td><?php echo $v->created_at?></td>
			<td><?php echo $v->updated_at?></td>
		</tr>
	<?php } ?>	
		</tbody>
	</table>
</div>
<?php echo Currency::orderBy('currency_id','asc')->paginate(10)->links();?>