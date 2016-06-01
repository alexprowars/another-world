<div class="portlet box red">
	<div class="portlet-title">
		<div class="caption">
			<i class="fa fa-cogs"></i>Список групп
		</div>
	</div>
	<div class="portlet-body">
		<div class="table-responsive">
			<table class="table table-striped table-bordered table-hover">
				<thead>
					<tr>
						<th>ID</th>
						<th>Имя группы</th>
					</tr>
				</thead>
				<? foreach ($list as $l): ?>
					<tr>
						<td><a href="/admin/groups/action/edit/?id=<?=$l['id'] ?>"><?=$l['id'] ?></a></td>
						<td><?=$l['name'] ?></td>
					</tr>
				<? endforeach; ?>
			</table>
		</div>
	</div>
</div>