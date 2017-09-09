<div class="col-xs-12">
	<table class="table table-striped">
		<tbody>
	<?php foreach (Region::all() as $k => $v) { ?>
		<tr>
			<td><?php echo $v->region_id?></td>
			<td><a href="/regions/<?php echo $v->region_id?>/edit"><?php echo $v->region_name?></a></td>
			<td><?php echo $v->region_code;?></td>
		</tr>
	<?php } ?>	
		</tbody>
	</table>
</div>