<div class="col-xs-12">
	<table class="table table-striped">
		<tbody>
	<?php foreach (Game::all() as $k => $v) { ?>
		<tr>
			<td><?php echo $v->game_id?></td>
			<td><a href="/games/<?php echo $v->game_id?>/edit"><?php echo $v->game_name?></a></td>
			<td><?php echo $v->platform->platform_name;?></td>
			<td><?php echo $v->platform->region->region_name;?></td>
			<td><?php echo $v->platform->region->region_code;?></td>
		</tr>
	<?php } ?>	
		</tbody>
	</table>
</div>