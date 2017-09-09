<div class="col-xs-12">
	<table class="table table-striped">
		<tbody>
	<?php foreach (Group::all() as $k => $v) { ?>
		<tr>
			<td><?php echo $v->group_id ?></td>
			<td><a href="/groups/<?php echo $v->group_id ?>/edit"><?php echo $v->group_name ?></a></td>
		</tr>
	<?php } ?>	
		</tbody>
	</table>
</div>