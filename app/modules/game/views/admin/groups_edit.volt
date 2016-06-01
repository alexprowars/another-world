<? if (isset($info)): ?>
	<div class="portlet box green">
		<div class="portlet-title">
			<div class="caption">Редактирование группы "<?=$info['name'] ?>"</div>
		</div>
		<div class="portlet-body form">
			<form action="/admin/groups/action/edit/?id=<?=$info['id'] ?>" method="post" class="form-horizontal form-row-seperated">
				<div class="form-body">
					<div class="form-group">
						<label class="col-md-3 control-label">Имя</label>
						<div class="col-md-9">
							<input type="text" class="form-control" name="name" value="<?=$info['name'] ?>">
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label">Администраторские права</label>
						<div class="col-md-9">
							<div class="row">
								<div class="col-xs-6"></div>
								<div class="col-xs-3 text-xs-center">Чтение</div>
								<div class="col-xs-3 text-xs-center">Изменение</div>
							</div>
							<? foreach ($modules as $module): if ($module['is_admin'] == 0) continue; ?>
								<input type="hidden" name="module[<?=$module['id'] ?>]" value="0">
								<div class="row">
									<div class="col-xs-6">
										<label for="module_<?=$module['id'] ?>"><?=$module['name'] ?></label>
									</div>
									<div class="col-xs-3 text-xs-center">
										<input id="module_<?=$module['id'] ?>" <?=(isset($rights[$module['id']]) && $rights[$module['id']]['right_id'] >= 1 ? 'checked' : '') ?> type="checkbox" name="module[<?=$module['id'] ?>]" value="1">
									</div>
									<div class="col-xs-3 text-xs-center">
										<input <?=(isset($rights[$module['id']]) && $rights[$module['id']]['right_id'] == 2 ? 'checked' : '') ?> type="checkbox" name="module[<?=$module['id'] ?>]" value="2">
									</div>
								</div>
							<? endforeach; ?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label">Доступ к контроллерам</label>
						<div class="col-md-9">
							<? foreach ($modules as $module): if ($module['is_admin'] == 1) continue; ?>
								<input type="hidden" name="module[<?=$module['id'] ?>]" value="0">
								<div class="row">
									<div class="col-xs-6">
										<label for="module_<?=$module['id'] ?>"><?=$module['name'] ?></label>
									</div>
									<div class="col-xs-6 text-xs-center">
										<input id="module_<?=$module['id'] ?>" <?=(isset($rights[$module['id']]) && $rights[$module['id']]['right_id'] >= 1 ? 'checked' : '') ?> type="checkbox" name="module[<?=$module['id'] ?>]" value="1">
									</div>
								</div>
							<? endforeach; ?>
						</div>
					</div>
					<div class="form-actions">
						<button type="submit" name="save" class="btn green" value="Y">Сохранить</button>
					</div>
				</div>
			</form>
		</div>
	</div>
<? endif; ?>