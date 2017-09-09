<div class="col-xs-12">
	<table class="table table-striped">
		<thead>
			<tr class="info">
				<td><?php echo Lang::get('log.login_key') ?></td>
				<td><?php echo Lang::get('log.desc') ?></td>
			</tr>
		</thead>
		<tbody>
	<?php foreach ($logs as $k => $v) { ?>
		<tr>
				<td><?php echo $v->log_key?></td>
				<td><?php echo $v->desc?></td>
			</tr>
	<?php } ?>	
		</tbody>
	</table>
</div>
<?php echo $logs->links();?>