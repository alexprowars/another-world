<?


$block = '';
if ($info['banaday'])
{
	$block = "<br><font class=bloked>Персонаж заблокирован!</font><br><b>Причина блокировки:</b> <font color=red class=ch><b>" . $info['bloked'] . "</b></font>";
}

$info['about'] = str_replace("\n", '<br>', $info['about']);

?>
<? if (!$this->request->hasQuery('frame')): ?>
	<table  width="100%" background="/images/images/inf/infbg2.jpg" border="0" height="49">
		<tr>
			<td width="70"><img src="/images/images/inf/inf3.jpg" width="70" height="49"></td>
			<td width="40%">&nbsp;</td>
			<td width="198"><img src="/images/images/inf/inf4.jpg" width="198" height="49"></td>
			<td width="40%">&nbsp;</td>
			<td width="70"><img src="/images/images/inf/inf6.jpg" width="70" height="49"></td>
		</tr>
	</table>
<? endif; ?>
<?

$mode = $this->request->getPost('mode');

if (!$mode || $mode == "1"){


?>
<div id="mgift" style="background-color:#FFFFCC; z-index:99; display: none; overflow:visible; position:absolute; border-color:#666666; border-style:solid; border-width: 1px; padding: 2px;">
	<table width=240 border=0 cellpadding=0 cellspacing=0>
		<tr>
			<td align=left id=mgift_title></td>
			<td align=right>
				<font color=red><a href="javascript:void(0);" onclick="HideGift(); return 0;"><b>x</b></a></font>
			</td>
		</tr>
		<tr>
			<td colspan=2><img src="/img/design_new/images/pix.gif" alt="" width=240 height=1 border=0></td>
		</tr>
		<tr>
			<td colspan=2 id=mgift_pict align=center bgcolor="#dedede">
			<td>
		</tr>
		<tr>
			<td colspan=2 id=mgift_sign></td>
		</tr>
	</table>
</div>
<div class="person-info">
	<table width="100%" height="100%" border="0">
		<tr>
			<? if (!$this->request->hasQuery('frame')): ?>
				<td width="12" height="341" rowspan="3" background="/images/images/inf/voc_left.jpg"><img src="/images/images/inf/spacer.gif" width="12" height="1"></td>
			<? endif; ?>
			<td width="50%" height="300" valign="top">
				<center>
					<table border="0"  width="270">
						<tr>
							<td background="/images/images/inf/vinf3.jpg"><img src="/images/images/inf/vinf1.jpg" width="40" height="28" border="0" alt=""/></td>
							<td valign="top" style="white-space: nowrap; padding-left:10px; padding-right:10px; padding-top:3px;" background="/images/images/inf/vinf3.jpg"><font color=FFFFFF><b>&nbsp;&nbsp;Характеристики</b></font></td>
							<td><img src="/images/images/inf/vinf2.jpg" width="40" height="28" border="0" alt=""/></td>
						</tr>
					</table>
				</center>
				<br>
				<table width="90%" align="center">
					<tr>
						<td style="border:solid windowtext 1.5pt; border-color: #e1d0b0;" bgcolor=efdcb8>
							<table class="table" bgcolor=#e1d0b0>
								<? foreach (_getText('stats') as $code => $name): ?>
									<tr>
										<td bgcolor="#efddb9" width="15"><img src="/images/images/inf/ico_<?=$code ?>.jpg" width="13" height="13"></td>
										<td bgcolor="#efddb9">&nbsp;&nbsp;<?=$name ?>:</td>
										<td bgcolor="#efddb9" class="text-xs-center" width="50">
											<b>
												<a href="#" style="font-size:11px;color:#E03504;" class="tooltip text" data-content="<table width=120><tr><td width=50% align=left><b>Сила:</b></td><td width=50% align=right><?= $info[$code] ?></td></tr><tr><td width=50% align=left><b>Своя:</b></td><td width=50% align=right><?= $info['~'.$code] ?></td></tr><tr><td width=50% align=left><b>Эффекты:</b></td><td width=50% align=right><?= ($info[$code] - $info['~'.$code]) ?></td></tr></table>"><?= $info[$code] ?></a>
											</b>
										</td>
									</tr>
								<? endforeach; ?>
							</table>
						</td>
					</tr>
				</table>

				<? if (count($info['prizes']) > 0): ?>
					<br>
					<center>
						<table border="0"  width="270">
							<tr>
								<td background="/images/images/inf/vinf3.jpg">
									<img src="/images/images/inf/vinf1.jpg" width="40" height="28" border="0" alt=""/></td>
								<td valign="top" style="white-space: nowrap; padding-left:10px; padding-right:10px; padding-top:3px;" background="/images/images/inf/vinf3.jpg">
									<font color=FFFFFF><b>&nbsp;&nbsp;&nbsp;Подарки</b></font></td>
								<td><img src="/images/images/inf/vinf2.jpg" width="40" height="28" border="0" alt=""/></td>
							</tr>
						</table>
					</center>
					<br>
					<table border="0" width="90%" align="center">
						<tr>
							<td style="border:solid windowtext 1.5pt; border-color: #e1d0b0;" bgcolor=efdcb8>
								<table cellSpacing="4" cellPadding="4" border="0" align="center" width="100%">
									<tr>
										<td>
											<div id="prizes"></div>
											<? foreach ($info['prizes'] as $prize): ?>
												<script>$('#prizes').append(DF('<?=$prize['name'] ?>',1, '<?=$prize['title'] ?>', '<?=$prize['text'] ?>' , '<?=$prize['sender'] ?>', '<?=$prize['who'] ?>'));</script>
											<? endforeach; ?>

											<? if (count($info['prizes']) > 17): ?>
												<a href='/info/?id=<?=$info['id'] ?>&prizes=Y'><font color=000000><i>Нажмите сюда, чтобы увидеть все подарки...</i></font></a>
											<? endif; ?>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				<? endif; ?>
			</td>

			<td width="300" height="300" align="center" valign="top">
				<table class="tmain personBlock">
					<tr>
						<td colspan="2" style="width:245px;">
							<div class="personName">
								<div id="person"></div>
								<script type="text/javascript">
									$('#person').html(show_inf('<?=$info['username'] ?>', '<?=$info['id'] ?>', '<?=$info['level'] ?>', '<?=$info['rank'] ?>', '<?=$info['tribe'] ?>'));
								</script>
							</div>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<div>
								<div class="dlfr">
									<table id="slotable">
										<tr>
											<td>
												<div class="bdg stbox">
													<div id="life" class="g_line" style="width:<?= $info['w_h'] ?>%">
														<img src="/images/main/empty.gif" width="1" height="10"/></div>
												</div>
											</td>
											<td align="right" class="fntc"><span id="text_life"><?= $info['hp_now'] ?></span></td>
											<td class="intf">|</td>
											<td class="minf"><?= $info['hp_max'] ?></td>
										</tr>
										<tr>
											<td>
												<div class="bdg stbox">
													<div id="mana" class="b_line" style="width:<?= $info['w_e'] ?>%">
														<img src="/images/main/empty.gif" width="1" height="10"/></div>
												</div>
											</td>
											<td align="right" class="fntc"><span id="text_mana"><?= $info['energy_now'] ?></span></td>
											<td class="intf">|</td>
											<td class="minf"><?= $info['energy_max'] ?></td>
										</tr>
									</table>
								</div>
								<div>
									<table class="person_slots" style="width:240px; height:260px;border:solid #e1d0b0 1.5pt;" bgcolor=bfbfbf>
										<tr>
											<td width="60" height="260" valign="top" class="left">
												<?=$info['slot_1']->view() ?>
												<?=$info['slot_21']->view() ?>
												<?=$info['slot_2']->view() ?>
												<?=$info['slot_3']->view() ?>
												<?=$info['slot_4']->view() ?>
												<?=$info['slot_9']->view() ?>
												<?=$info['slot_6']->view() ?>
												<?=$info['slot_7']->view() ?>
												<?=$info['slot_8']->view() ?>
											</td>
											<td width="120" valign="top">
												<img src="/images/avatar/<?= $info['obraz'] ?>.png" width="120" height="220" alt="<?= $info['username'] ?>">
											</td>
											<td width="60" height="260" valign="top" class="right">
												<?=$info['slot_14']->view() ?>
												<?=$info['slot_15']->view() ?>
												<?=$info['slot_5']->view() ?>
												<?=$info['slot_10']->view() ?>
												<?=$info['slot_11']->view() ?>
												<?=$info['slot_12']->view() ?>
												<?=$info['slot_22']->view() ?>
												<?=$info['slot_13']->view() ?>
											</td>
										</tr>
									</table>
								</div>
							</div>
						</td>
					</tr>
				</table>
			</td>
			<td width="50%" height="300" valign="top">
				<center>
					<table border="0"  width="270">
						<tr>
							<td background="/images/images/inf/vinf3.jpg">
								<img src="/images/images/inf/vinf1.jpg" width="40" height="28" border="0" alt=""/></td>
							<td valign="top" style="white-space: nowrap; padding-left:10px; padding-right:10px; padding-top:3px;" background="/images/images/inf/vinf3.jpg">
								<font color=FFFFFF><b>&nbsp;&nbsp;&nbsp;Статистика</b></font></td>
							<td><img src="/images/images/inf/vinf2.jpg" width="40" height="28" border="0" alt=""/></td>
						</tr>
					</table>
				</center>
				<br>
				<table width="90%" align="center">
					<tr>
						<td style="border:solid windowtext 1.5pt; border-color: #e1d0b0;" bgcolor=efdcb8>
							<table class="table" bgcolor=#e1d0b0>
								<tr>
									<td bgcolor="#efddb9">Уровень:</td>
									<td bgcolor="#efddb9" align="right"><b><?=$info['level'] ?></b></td>
								</tr>
								<tr>
									<td bgcolor="#efddb9">Побед:</td>
									<td bgcolor="#efddb9" align="right">
										<a href='index.php?type=top_user' target=_blank><font color=000000><b><?=$info['wins'] ?></b></font></a></td>
								</tr>
								<tr>
									<td bgcolor="#efddb9">Поражений:</td>
									<td bgcolor="#efddb9" align="right"><b><?=$info['losses'] ?></b></td>
								</tr>
								<tr>
									<td bgcolor="#efddb9">Ничьих:</td>
									<td bgcolor="#efddb9" align="right"><b><?=$info['drawn'] ?></b></td>
								</tr>
								<tr>
									<td bgcolor='#efddb9'>Крутизна:</td>
									<td bgcolor='#efddb9' align='right'><b><?=$info['reit'] ?></b></td>
								</tr>
								<tr>
									<td bgcolor="#efddb9">Дата рождения:</td>
									<td bgcolor="#efddb9" align="right"><b><?
											if ($info['admin'] == 1)
											{
												echo "<i>До начала времен</i>";
											}
											else
											{
												echo date("d.m.Y", $info['register_time']);
											}
											?></b></td>
								</tr>
								<tr>
									<td bgcolor="#efddb9">Профессия:</td>
									<td bgcolor="#efddb9" align="right"><b><?=_getText('proffessions', $info['proff']) ?></b></td>
								</tr>
								<tr>
									<td bgcolor="#efddb9">Статус:</td>
									<td bgcolor="#efddb9" align="right"><b><?=_getText('rank', $info['rank']) ?></b></td>
								</tr>
								<?
								if ($info['rank_info'])
								{
									echo "<tr><td bgcolor=efddb9>Должность:</td><td bgcolor=efddb9 align=right>" . $info['rank_info'] . "</td></tr>";
								}
								?>
								<?
								if ($info['tribe'])
								{
									echo "<tr><td bgcolor='#efddb9' colspan='2'>Клан: <IMG SRC='img/klan/" . $info['tribe'] . ".gif' width=24 height=14> <a href='clan_inf.php?clan=" . $info['tribe'] . "' target=_blank><b>" . $clan['name_short'] . "</b></a><br>Должность: ";

									if ($info['b_tribe'] == 1)
										echo "<b>Глава клана</b>";
									else
									{
										switch ($info['tribe_rank'])
										{
											case "1":
												$st = "Глава";
												break;
											case "2":
												$st = "Зам. главы";
												break;
											case "3":
												$st = "Казначей";
												break;
											case "4":
												$st = "Оружейник";
												break;
											case "5":
												$st = "Вербовщик";
												break;
											case "6":
												$st = "Командир группы";
												break;
											case "7":
												$st = "Судья";
												break;
											case "8":
												$st = "Леди";
												break;
											case "9":
												$st = "Дипломат";
												break;
											case "10":
												$st = "Журналист";
												break;
											case "11":
												$st = "Шут";
												break;
											default:
												$st = "Боец";
												break;
										}
										echo "<b>" . $st . "</b>";
									}

									echo "</td></tr>";
								}
								?>
							</table>
						</td>
					</tr>
				</table>
				<br>
				<center>
					<table border="0"  width="270">
						<tr>
							<td background="/images/images/inf/vinf3.jpg">
								<img src="/images/images/inf/vinf1.jpg" width="40" height="28" border="0" alt=""/></td>
							<td valign="top" style="white-space: nowrap; padding-left:10px; padding-right:10px; padding-top:3px;" background="/images/images/inf/vinf3.jpg">
								<font color=FFFFFF><b>&nbsp;&nbsp;&nbsp;Особенности</b></font></td>
							<td><img src="/images/images/inf/vinf2.jpg" width="40" height="28" border="0" alt=""/></td>
						</tr>
					</table>
				</center>
				<br>
				<table  border="0" width="90%" align="center">
					<tr>
						<td style="border:solid windowtext 1.5pt; border-color: #e1d0b0;" bgcolor=efdcb8>
							<table class="table">
								<tr>
									<td>
										<?
										function get_znak ($time)
										{
											$day 	= date("j", $time);
											$month 	= date("n", $time);

											$result = '';

											switch ($month)
											{
												case 1:
													$result = ($day <= 20) ? "1.gif' alt='Козерог - Земля'" : "2.gif' alt='Водолей - Воздух'";
													break;
												case 2:
													$result = ($day <= 18) ? "2.gif' alt='Водолей - Воздух'" : "3.gif' alt='Рыбы - Вода'";
													break;
												case 3:
													$result = ($day <= 20) ? "3.gif' alt='Рыбы - Вода'" : "4.gif' alt='Овен - Огонь'";
													break;
												case 4:
													$result = ($day <= 20) ? "4.gif' alt='Овен - Огонь'" : "5.gif' alt='Телец - Земля'";
													break;
												case 5:
													$result = ($day <= 21) ? "5.gif' alt='Телец - Земля'" : "6.gif' alt='Близнецы - Воздух'";
													break;
												case 6:
													$result = ($day <= 22) ? "6.gif' alt='Близнецы - Воздух'" : "7.gif' alt='Рак - Вода'";
													break;
												case 7:
													$result = ($day <= 22) ? "7.gif' alt='Рак - Вода'" : "8.gif' alt='Лев - Огонь'";
													break;
												case 8:
													$result = ($day <= 21) ? "8.gif' alt='Лев - Огонь'" : "9.gif' alt='Дева - Земля'";
													break;
												case 9:
													$result = ($day <= 23) ? "9.gif' alt='Дева - Земля'" : "10.gif' alt='Весы - Воздух'";
													break;
												case 10:
													$result = ($day <= 23) ? "10.gif' alt='Весы - Воздух'" : "11.gif' alt='Скорпион - Вода'";
													break;
												case 11:
													$result = ($day <= 21) ? "11.gif' alt='Скорпион - Вода'" : "12.gif' alt='Стрелец - Огонь'";
													break;
												case 12:
													$result = ($day <= 22) ? "12.gif' alt='Стрелец - Огонь'" : "1.gif' alt='Козерог - Земля'";
													break;
											}

											return $result;
										}

										echo "<img src='/images/zodiac/" . get_znak($info['register_time']) . "> - Знак зодиака.<br>";
										if ($info['banaday'])
											echo "<font color=red><b>Причина блокировки:<br>" . $info['bloked'] . " </font><br>";
										if ($info['t_time'] > time())
											echo "<font color=red><b>В тюрьме ещё <i><u>" . pretty_time($info['t_time']) . " </i></u></font><br>";
										if ($info['username'] == 'admin')
											echo "<img src='/images/images/inf/admin.gif'> - Администратор <b>Another World</b><br>";
										if ($info['silence'] > time())
											echo "<img src='/images/images/inf/molch.gif' alt='".pretty_time($info['silence'])."'> - Запрещено общение в чате<br>";
										if ($info['vip'] == 1)
											echo "<img src='/images/images/inf/vip.gif'>&nbsp;- <b>VIP</b> персона Another World <br>";
										if ($info['sign'] > time())
											echo "<img src='/images/images/inf/bj.gif' alt='".pretty_time($info['sign'])."'>&nbsp;- Под действием способности \"Боевая ярость\" <br>";
										if ($info['travma'] > time())
											echo "<img src='/images/images/inf/travma.gif' alt='".pretty_time($info['travma'])."'>&nbsp;- Персонаж травмирован<br>";
										?>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				<br>
				<center>
					<table border="0"  width="270">
						<tr>
							<td background="/images/images/inf/vinf3.jpg">
								<img src="/images/images/inf/vinf1.jpg" width="40" height="28" border="0" alt=""/></td>
							<td valign="top" style="white-space: nowrap; padding-left:10px; padding-right:10px; padding-top:3px;" background="/images/images/inf/vinf3.jpg">
								<font color=FFFFFF><b>&nbsp;&nbsp;&nbsp;Награды</b></font></td>
							<td><img src="/images/images/inf/vinf2.jpg" width="40" height="28" border="0" alt=""/></td>
						</tr>
					</table>
				</center>
			</td>
			<? if (!$this->request->hasQuery('frame')): ?>
				<td width="10" height="300" rowspan="3" background="/images/images/inf/voc_right.jpg"><img src="/images/images/inf/spacer.gif" width="10" height="1"></td>
			<? endif; ?>
		</tr>
		<tr>
			<td colspan="3">
				<hr color=efdcb8>
				include("includes_3/inf/friends.php");
				<hr color=efdcb8>
			</td>
		</tr>
		<tr>
			<td colspan="3">
				<center>
					<table border="0"  width="270">
						<tr>
							<td background="/images/images/inf/vinf3.jpg">
								<img src="/images/images/inf/vinf1.jpg" width="40" height="28" border="0" alt=""/></td>
							<td valign="top" style="white-space: nowrap; padding-left:10px; padding-right:10px; padding-top:3px;" background="/images/images/inf/vinf3.jpg">
								<font color=FFFFFF><b>&nbsp;&nbsp;&nbsp;Анкетные Данные</b></font></td>
							<td><img src="/images/images/inf/vinf2.jpg" width="40" height="28" border="0" alt=""/></td>
						</tr>
					</table>
				</center>
				<table bgcolor="efdcb8" style="border: solid 1px #e1d0b0" class="table" align="center">
					<tr>
						<td valign="top"><b>Имя:</b> <?= $info['name'] ?></td>
					</tr>
					<tr>
						<td>
						<b>Пол:</b>
						<?
							switch ($info['sex'])
							{
								case 1:
									echo "Мужской";
									break;
								case 2:
									echo "Женский";
									break;
							}
							?>
						</td>
					</tr>
					<? if ($info['city'] != ''): ?>
						<tr>
							<td><b>Город:</b> <?= $info['city'] ?></td>
						</tr>
					<? endif; ?>
					<? if ($info['about'] != ''): ?>
						<tr>
							<td><b>О себе:</b><br> <?= $info['about'] ?> <br></td>
						</tr>
					<? endif; ?>
					<?
					}
					elseif ($mode == "2")
					{
						if ((($this->user->rank >= 11 && $this->user->rank <= 14) || $this->user->rank >= 98) || ($this->user->admin == 1 || $this->user->id == $info['id']))
						{
							include('includes_3/inf/transfers.php');
						}
						else
							echo "Нет доступа";
					}
					elseif ($mode == "3")
					{
						if ((($this->user->rank >= 11 && $this->user->rank <= 14) || $this->user->rank >= 98) || ($this->user->admin == 1 || $this->user->id == $info['id']))
						{
							include('includes_3/inf/bank.php');
						}
						else
							echo "Нет доступа";
					}
					elseif ($mode == "4")
					{
						if ((($this->user->rank >= 11 && $this->user->rank <= 14) || $this->user->rank >= 98) || ($this->user->admin == 1 || $this->user->id == $info['id']))
						{
							include('includes_3/inf/bank.php');
						}
						else
							echo "Нет доступа";
					}
					elseif ($mode == "5")
					{
						if ((($this->user->rank >= 11 && $this->user->rank <= 14) || $this->user->rank >= 98) || ($this->user->admin == 1 || $this->user->id == $info['id']))
						{
							include('includes_3/inf/ld.php');
						}
						else
							echo "Нет доступа";
					}


					//
					if ((($this->user->rank >= 11 && $this->user->rank <= 14) || $this->user->rank == 36 || $this->user->rank >= 98) || $this->user->admin == 1)
					{
						?>
						<tr>
							<td><BR>
								<center><font face=Verdana size=2pt><u>Личное дело персонажа <b><?= $info['username'] ?></b></u></font></center>

								<table width=100% cellspacing=0 cellpadding=3 border=0>

									<tr>
										<td width=26%><b>IP при регистрации:</b></td>
										<td><b><i><?
													if ($info['admin'] == 1)
													{
														echo "127.0.0.1";
													}
													else
													{
														echo "" . $info['ip'] . "";
													}
													?></i></b></td>
									</tr>

									<tr>
										<td width=26%><b>IP последний:</b></td>
										<td><b><i><?
													if ($info['admin'] == 1)
													{
														echo "127.0.0.1";
													}
													else
													{
														echo "" . $info['last_ip'] . "";
													}
													?></i></b></td>
									</tr>

									<tr>
										<td><b>E-Mail адрес:</b></td>
										<td><a href='mailto:<?= $info['email'] ?>'><i><?= $info['email'] ?></i></a></td>
									</tr>

									<tr>
										<td><b>День рождения:</b></td>
										<td><i><?= $info['birth'] ?></i></td>
									</tr>

									<tr>
										<td><b>Мультиники:</b></td>
										<td>

											<? include('includes_3/inf/mults.php'); ?>

										</td>
									</tr>
									<tr>
										<td><b>Опыт:</b></td>
										<td><i><?= $info['exp'] ?></i></b></td>
									</tr>
									<tr>
										<td><b>KR:</b></td>
										<td><i><?= $info['credits'] ?></i></b></td>
									</tr>
									<tr>
										<td><b>EKR:</b></td>
										<td><i><?= $info['f_credits'] ?></i></b></td>
									</tr>
									<tr>
										<td><b>PeKR:</b></td>
										<td><i><?= $info['p_credits'] ?></i></b></td>
									</tr>
									<tr>
										<td><b>Реферал:</b></td>
										<td><i><?= $info['refer'] ?></i></b></td>
									</tr>
									<tr>
										<td><b>Неиспользованых UP-ов:</b></td>
										<td><i><?= $info['s_updates'] ?></i></b></td>
									</tr>
									<? echo " <tr><td><b><a href=view_logs.php?login=$info[user]>Логи боев</a></b></td></tr>"; ?>

								</table>

							</td>
						</tr>
					<?
					}
					?>
					<br>

				</table>
				<?

				if ((($this->user->rank >= 11 && $this->user->rank <= 14) || $this->user->rank >= 98) || ($this->user->admin == 1 || $this->user->id == $info['id']))
				{
					?>
					<br>
					<form action="" method="POST">
						<table align="center">
							<tr align="center">
								<td align="center">
									<select size="1" name="mode" class=input>
										<option value="1">Инфо</option>
										<option value="2">Передачи</option>
										<option value="3">Банковские переводы</option>
										<option value="4">Покупки</option>
										<option value="5">Личное дело</option>
									</select>
								</td>
								<td align="center">
									<input type="submit" value="Просмотр" name="action" class=input>
								</td>
							</tr>
						</table>
					</form>
				<?
				}
				?>
			</td>
		</tr>
	</table>
	<? if (!$this->request->hasQuery('frame')): ?>
		<TABLE height=12 cellSpacing=0 cellPadding=0 width="100%">
			<TR height=12>
				<TD background=/images/images/inf/Polosa1.jpg height=12>
					<IMG src="/images/images/inf/1.gif"></TD>
			</TR>
		</TABLE>
	<? endif; ?>
</div>