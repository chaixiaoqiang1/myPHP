<div class="col-xs-12">
	<table class="table table-striped">
		<tbody>
	<?php foreach (Platform::all() as $k => $v) { ?>
		<tr>
			<td><?php echo $v->platform_id?></td>
			<td><a href="/platforms/<?php echo $v->platform_id?>/edit"><?php echo $v->platform_name?></a></td>
			<td><?php echo $v->platform_url;?></td>
			<td><?php echo $v->region->region_name;?></td>
			<td><?php echo $v->region->region_code;?></td>
			<td><?php echo Game::where('game_id', $v->default_game_id)->pluck('game_name')?></td>
		</tr>
	<?php } ?>	
		</tbody>
	</table>
</div>