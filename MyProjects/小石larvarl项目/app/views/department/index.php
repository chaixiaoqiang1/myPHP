<div class="col-xs-12">
	<table class="table table-striped">
		<tbody>
	<?php foreach (Department::organization()->get() as $k => $v) { ?>
		<tr>
			<td><?php echo $v->department_id?></td>
			<td><?php echo $v->department_name ?></td>
		</tr>
	<?php } ?>	
		</tbody>
	</table>
</div>