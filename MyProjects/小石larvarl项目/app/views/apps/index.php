<?php if (empty($re)) {?>
<div class="col-xs-12">
	<div id="search" style="margin-bottom:20px;">
		<!-- action路径传参数like只为使用source-show方法去执行搜索功能操作，可修改为其他字符，无实际意义-->
		<form class="form-inline form-search" action="/apps" method="get">
			<div class="form-group">
				<select class="form-control" name="type" id="type">
						<option value="0">请选择检索方式</option>
						<option value="1">根据系统功能名称</option>
						<option value="2">根据系统功能描述</option>
				</select>
			</div>
			<div class="form-group">
				<label class="sr-only" for="exampleInputAmount">请输入系统功能名称</label>
				<div class="input-group">
					<input type="text" class="form-control " name="search" placeholder="请输入功能名称或描述">
				</div>
			</div>
			<button type="submit" class="btn btn-primary">搜索</button>
		</form>
	</div>
	<table class="table table-striped">
		<tbody>
			<?php foreach ($pg as $k => $v) { ?>
			<tr>
				<td><?php echo $v->app_id?></td>
				<td><a href="apps/<?php echo $v->app_id?>/edit"><?php echo $v->app_name?></a></td>
				<td><a href="<?php echo url($v->app_key)?>"><?php echo $v->app_key ?></a></td>
				<?php if (mb_strlen($v->description) > 40 ) {?>
				<td><a href="#" title="<?php echo $v->app_name ?>"  
					data-container="body" data-toggle="popover" data-placement="top" 
					data-content="<?php echo $v->description ?>"><?php echo mb_substr($v->description,0,40).'...点击查看详情' ?></a></td>
					<?php }else{?>
					<td><a href="#"><?php echo $v->description ?></a></td>
					<?php }?>
					<td>
						<?php if ($v->department) {?>
						<?php echo $v->department->department_name?>
						<?php }?>
					</td>
				</tr>
				<?php } ?>	
			</tbody>
		</table>
		<?php echo $pg->appends(array('type'=>$type))->links() ?>
	</div>
	<?php }else{?>
	<div class="col-xs-12">
		<div id="search" style="margin-bottom:20px;">
			<form class="form-inline form-search" action="/apps" method="get">
			<div class="form-group">
				<select class="form-control" name="type" id="type">
						<option value="0">请选择检索方式</option>
						<option value="1">根据系统功能名称</option>
						<option value="2">根据系统功能描述</option>
				</select>
			</div>
				<div class="form-group">
					<label class="sr-only" for="exampleInputAmount">请输入功能名称或描述</label>
					<div class="input-group">
						<input type="text" class="form-control " name="search" placeholder="<?php echo $keywords ?>">
					</div>
				</div>
				<button type="submit" class="btn btn-primary">搜索</button>
			</form>
		</div>
		<table class="table table-striped">
			<tbody>
				<?php foreach ($re as $k => $v) { ?>
				<tr>
					<td><?php echo $v->app_id?></td>
					<td><a href="apps/<?php echo $v->app_id?>/edit"><?php echo $v->app_name?></a></td>
					<td><a href="<?php echo url($v->app_key)?>"><?php echo $v->app_key ?></a></td>
					<?php if (mb_strlen($v->description) > 40 ) {?>
					<td><a href="#" title="<?php echo $v->app_name ?>"  
						data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $v->description ?>"><?php echo mb_substr($v->description,0,40).'......点击查看详情' ?></a></td>
						<?php }else{?>
						<td><a href="#"><?php echo $v->description ?></a></td>
						<?php }?>				<td>
						<?php if ($v->department) {?>
						<?php echo $v->department->department_name?>
						<?php }?>
					</td>
				</tr>
				<?php } ?>	
			</tbody>
		</table>
		<?php echo $re->appends(array('search' =>$keywords,'type'=>$type))->links() ?>
	</div>
	<?php }?>
	<!-- 最新的 Bootstrap 核心 JavaScript 文件 -->  
	<script src="http://cdn.bootcss.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
	<script>
		$(function () { 
			$("[data-toggle='popover']").popover();
		});
		$("#type option[value='<?php echo $type;  ?>']").attr('selected',true);
	</script>
