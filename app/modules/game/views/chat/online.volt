<div class="refresh">
	<a href="javascript:;" onclick="loadChatList()"><img src="/images/refresh.png" align="absmiddle" width="16" height="16" title="Обновить список игроков"></a>
	<input type="checkbox" value="1" name="autoreload" title="Автоматическое обновление списка игроков" id="autoreload" <?=($this->request->has('autoreload') && $this->request->get('autoreload') == '1' ? 'checked' : '') ?>>
</div>

<div class="actions">
	<div class="text-left">В игре: <?=count($users) ?> | <a href="?show=<?=(($show == 1) ? 2 : 1) ?>"><?=_getText('rooms', $this->user->room) ?></a> (<?=(($show == 1) ? 'мир' : 'комната') ?>)</div>
	<div class="text-xs-center"><a href="?sort=1" <?=($sort == 1 ? 'class="active"' : '') ?>>а-я</a> | <a href="?sort=2" <?=($sort == 2 ? 'class="active"' : '') ?>>я-а</a> | <a href="?sort=3" <?=($sort == 3 ? 'class="active"' : '') ?>>0-10</a> | <a href="?sort=4" <?=($sort == 4 ? 'class="active"' : '') ?>>10-0</a></div>
</div>
<?
	foreach ($users as $pl)
	{
		?>
			<div class="people">
				<a href="javascript:pp('<?=$pl['username'] ?>')" title="Приватно"><img src="/images/chat/private<?=(($pl['battle'] > 0) ? '_b' : '') ?>.gif" width="22" height="15"></a>

				<? if ($pl['tribe']): ?>
					<a href="javascript:cl('<?=$pl['tribe'] ?>')"><IMG SRC="/images/tribe/<?=$pl['tribe'] ?>.gif" WIDTH="24" HEIGHT="15" title="Клан <?=$pl['tribe'] ?>"></A>
				<? endif; ?>
				<? if ($pl['rank'] != 0): ?>
					<IMG SRC="/images/rank/<?=$pl['rank'] ?>.gif" title="<?=_getText('rank', $pl['rank']) ?>" width="12" height="15">
				<? endif; ?>

				<nobr><a href="javascript:to('<?=$pl['username'] ?>')" title="Послать сообщение"><?=$pl['username'] ?></a> <b>[<?=$pl['level'] ?>]</b> <a href="/info/?id=<?=$pl['id'] ?>&frame=Y" target="main" title="Посмотреть инфу" class="chat"><img src="/images/images/inf.gif" title="Посмотреть инфу" align="absmiddle"></a></nobr>

				<div class="pull-xs-right">
					<? if ($pl['status'] != 999): ?>
						 <img src="/images/chat/status<?=$pl['status'] ?>.gif" title="<?=_getText('status', $pl['status']) ?>" width="15" height="15">
					<? endif; ?>
					<? if ($pl['proff'] != 0): ?>
						 <img src="/images/guild/<?=$pl['proff'] ?>.gif" title="<?=_getText('proffessions', $pl['proff']) ?>">
					<? endif; ?>
					<? if ($pl['silence'] > time()): ?>
						 <img src="/images/chat/molch.gif" title="Молчанка до <?=date('d-m H:i', $pl['silence']) ?>" width="15" height="12">
					<? endif; ?>
					<? if ($pl['battle'] != 0): ?>
						<a href="/view_logs.php?log=<?=$pl['battle'] ?>" target="main"><img src=/images/chat/noweapon.gif alt="В бою" height="12" width="15"></a>
					<? endif; ?>
					<? if ($pl['travma'] > time()): ?>
						 <img src=/images/chat/travma.gif title="Травма">
					<? endif; ?>
				</div>
			</div>

		<?
	}
?>