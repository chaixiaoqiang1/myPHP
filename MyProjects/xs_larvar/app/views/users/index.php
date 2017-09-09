
<div class="col-xs-12">
	<table class="table table-striped">
		<thead>
			<tr class="info">
				<td><?php echo Lang::get('user.user_id') ?></td>
				<td><?php echo Lang::get('user.username') ?></td>
				<td><?php echo Lang::get('user.nickname') ?></td>
				<td><?php echo Lang::get('user.department_name') ?></td>
				<td><?php echo Lang::get('user.created_time') ?></td>
				<td><?php echo Lang::get('user.created_ip') ?></td>
				<td><?php echo Lang::get('user.last_login_ip') ?></td>
				<td><?php echo Lang::get('user.is_admin') ?></td>
				<td><?php echo Lang::get('user.operate_list') ?></td>
			</tr>
		</thead>
		<tbody>
	<?php foreach (User::organization()->where('is_closed',0)->get() as $k => $v) { ?>
		<tr>
				<td><?php echo $v->user_id ?></td>
				<td><a href="<?php echo '/users/'.$v->user_id.'/edit'?>"><?php echo $v->username ?></a></td>
				<td><?php echo $v->nickname ?></td>
				<td><?php echo $v->department->department_name?></td>
				<td><?php echo $v->created_at ?></td>
				<td><?php echo $v->created_ip ?></td>
				<td><?php echo $v->last_login_ip ?></td>
				<td><?php echo $v->isAdminStr()?></td>
				<td><button class="btn btn-danger" value="confirm"
						onclick='<?php echo "isSure(".$v->user_id.")"; ?>'><?php echo Lang::get("user.close_account");?></button></td>
			</tr>
	<?php } ?>	
		</tbody>
	</table>
</div>
<script>
function isSure(id){
    var t = confirm("<?php echo Lang::get('user.is_sure');?>");
    if (t == true)
    {
        window.location.href= "/users/close?id=" + id;
    }
}
</script>