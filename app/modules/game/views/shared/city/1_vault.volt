<? $this->view->partial('shared/city_header', Array('title' => 'Территория подземелья')); ?>
<div class="textblock">
	<table width=100% cellspacing=0 cellpadding=5 border=0>
		<tr>
			<td width=600 valign=top>
				<TABLE cellspacing=0 cellpadding=0>
					<tr>

						<TD valign=top>
							<script type="text/javascript">
								show_inf('<?=$this->user->username ?>', '<?=$this->user->id ?>', '<?=$this->user->level ?>', '<?=$this->user->rank ?>', '<?=$this->user->tribe ?>');
							</script>
						</TD>
						<TD valign=top>
							<table cellspacing=0 cellpadding=0 border=0 align=center height=12>
								<tr>
									<td width=200 align=left valign=bottom width=200>
										<img src=/images/images/vault/navigation/hp/_helth.gif width='10' height=10 border=0 alt=''><img src=/images/images/vault/navigation/hp/helth.gif height='10' width='<?= ceil(GetWPers($this->user->hp_now, $this->user->hp_max) / 100 * 170) ?>' border=0 alt=''><img src=/images/images/vault/navigation/hp/_helth_.gif width='10' height=10 border=0 alt=''>
									</td>
								</tr>
							</table>
						</TD>
						<TD valign=top>&nbsp;<FONT COLOR=RED><B><?=$this->user->hp_now ?> / <?=$this->user->hp_max ?></B></FONT>
						</TD>
					</TR>
				</TABLE>
			</td>
			<td align=right valign=top>
				<a href="<?=$this->url->get('map/') ?>"><img src='/images/images/refresh.gif' alt='Обновить'></a>
				<? if ($this->user->room == 200): ?>
					<a href="<?=$this->url->get('map/') ?>?refer=200"><img src='/images/images/back.gif' alt='Вернуться'></a>
				<? endif; ?>
			</td>
		</tr>
	</table>
	<table width=100% cellspacing=0 cellpadding=3 border=0>
		<tr>
			<td align=right>
				<center><u><b><?=_getText('rooms', $vault['id']) ?></b></u></center>
				<br>
				<? if ($message != ''): ?>
					<center><font color=red><b><?=$message ?></b></font></center>
					<br>
				<? endif; ?>

				<table width=100% cellspacing=0 cellpadding=5>
					<tr>
						<td align=center>
							<table cellspacing=0 cellpadding=0 border=0 width=100%>
								<tr>
									<td width=170 align=left valign=top>
										<!-- Навигация -->
										<table cellspacing=0 cellpadding=5 style='border: 2px outset;' border=1 width=150>
											<tr>
												<td align=center>
													<? if ($this->user->r_time == 0): ?>
														<b>Навигация</b>
														<HR color=silver>
														<table cellspacing=0 cellpadding=0 border=0>
															<tr height=45>
																<td width=45>&nbsp;</td>
																<td width=45 align=center valign=center>
																	<?=($vault['top_id'] ? '<a href="/map/?GoIn=top" title="Перейти в '._getText('rooms', $vault['top_id']).'">' : '') ?><IMG SRC="/images/images/vault/navigation/<?=($vault['top_id'] ? '' : 'n_') ?>active/top.gif"><?=($vault['top_id'] ? '</a>' : '') ?>
																</td>
																<td width=45>&nbsp;</td>
															</tr>
															<tr height=45>
																<td width=45 align=center valign=center>
																	<?=($vault['left_id'] ? '<a href="/map/?GoIn=left" title="Перейти в '._getText('rooms', $vault['left_id']).'">' : '') ?><IMG SRC="/images/images/vault/navigation/<?=($vault['left_id'] ? '' : 'n_') ?>active/left.gif"><?=($vault['left_id'] ? '</a>' : '') ?>
																</td>
																<td width=45 align=center valign=center><IMG SRC='/images/images/vault/navigation/center.gif'></td>
																<td width=45 align=center valign=center>
																	<?=($vault['right_id'] ? '<a href="/map/?GoIn=right" title="Перейти в '._getText('rooms', $vault['right_id']).'">' : '') ?><IMG SRC="/images/images/vault/navigation/<?=($vault['right_id'] ? '' : 'n_') ?>active/right.gif"><?=($vault['right_id'] ? '</a>' : '') ?>
																</td>
															</tr>
															<tr height=45>
																<td width=45>&nbsp;</td>
																<td width=45 align=center valign=center>
																	<?=($vault['bottom_id'] ? '<a href="/map/?GoIn=bottom" title="Перейти в '._getText('rooms', $vault['bottom_id']).'">' : '') ?><IMG SRC="/images/images/vault/navigation/<?=($vault['bottom_id'] ? '' : 'n_') ?>active/bottom.gif"><?=($vault['bottom_id'] ? '</a>' : '') ?>
																</td>
																<td width=45>&nbsp;</td>
															</tr>
														</table>
													<? endif; ?>
													<? if ($this->user->r_time > time() && $this->user->r_type == 10): ?>
														Топаем в <b><u><?=_getText('rooms', $this->user->v_room) ?></u></b>
														<HR color=silver>
														<TABLE cellspacing=0 cellpadding=0>
															<tr>
																<td>Ещё:&nbsp;</td>
																<td><b>
																		<small>
																			<span id=move></span>
																		</small>
																	</b>
																	<script>ShowTime('move', <?=($this->user->r_time - time()) ?>, 1);</script>
																</td>
															</tr>
														</table>
													<? endif; ?>
													<? if ($this->user->r_time > time() && $this->user->r_type == 8): ?>
														Добываем руду
														<HR color=silver>
														<TABLE cellspacing=0 cellpadding=0>
															<tr>
																<td>Ещё:&nbsp;</td>
																<td><b>
																		<small>
																			<span id=move></span>
																		</small>
																	</b>
																	<script>ShowTime('move', <?=($this->user->r_time - time()) ?>, 1);</script>
																</td>
															</tr>
														</table>
													<? endif; ?>
												</td>
											</tr>
										</table>
										<!-- Конец навигации -->
									</td>
									<td align=center valign=top>
										<?=$vault['text'] ?>
									</td>
									<td width=170 align=right valign=top>
										<? if ($this->user->r_time == 0): ?>
											<table style='border: 2px outset;' border=1 width=150>
												<tr>
													<td align="center" style="padding: 5px">
														<b>Действия</b><HR color=silver>
														<input type="button" class="input" value="Колодец Жизни" style="WIDTH: 120px"<?=($vault['heal'] >= time() ? " disabled" : " onclick=\"load('/map/?heal=Y')\"") ?>>
														<HR color="silver">
														<form action="<?=$this->url->get('map/') ?>" method="post">
															<input type="hidden" name="dig" value="Y">
															<img src="/captcha.php" width="120"><br>
															<input type="text" name="captcha" size="10" placeholder="код с картинки" maxlength="20" style="WIDTH: 120px">
															<input type="submit" class="input" value="Добыча руды" style="WIDTH: 120px" <?=($this->user->r_time >= time() ? "disabled" : "") ?>>
														</form>
													</td>
												</tr>
											</table>
										<? endif; ?>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>