<table class="table np">
	<tr>
		{{ user.getPersonBlock() }}
		<td valign="top">
			<? if (isset($message) && $message != ''): ?>
				<br>
				<center><font color=red><b><?= $message ?></b></font></center><br>
			<? endif; ?>
			{{ partial('shared/person_menu') }}
			<div class="textblock">
				<table  style="width:100%">
					<tr>
						<td><img src="/images/main/ltmenu1.jpg" width="37" height="29" style="vertical-align: middle" alt=""></td>
						<td align="center" class="submenufon">
							<table >
								<tr>
									<? $i = 0; foreach (_getText('bag') as $id => $title): ?>
										<? if ($i > 0 && $i % 4 == 0): ?>
														</tr>
													</table>
												</td>
												<td><img src="/images/main/rtmenu1.jpg" width="37" height="29" style="vertical-align: middle" alt=""></td>
											</tr>
											<tr>
												<td><img src="/images/main/ltmenu1.jpg" width="37" height="29" style="vertical-align: middle" alt=""></td>
												<td align="center" class="submenufon">
													<table >
														<tr>
										<? elseif ($i > 0): ?>
											<td style="padding: 0 10px;"><img src="/images/main/sm.jpg" width="8" height="29"  style="vertical-align: middle" alt=""></td>
										<? endif; ?>
										<td>
											<a <?= ($item_type == $id ? 'disabled' : '') ?> href="<?=$this->url->get("edit/") ?>?item_type=<?= $id ?>" class="smenu"><?= $title ?></a>
										</td>
									<? $i++; endforeach; ?>
								</tr>
							</table>
						</td>
						<td><img src="/images/main/rtmenu1.jpg" width="37" height="29" style="vertical-align: middle" alt="" ></td>
					</tr>
				</table>

				<table  style="width:100%">
					<tr>
						<td style="width:50%">
							<div align="right" class="hline"></div>
						</td>
						<td nowrap>
							<div class="button"><a href="<?=$this->url->get("edit/") ?>?item_type=9" class="tm">комплекты</a></div>
							<div class="button"><a href="<?=$this->url->get("edit/") ?>?unset=all" class="tm">снять все</a></div>
						</td>
						<td style="width:50%">
							<div class="hline"></div>
						</td>
					</tr>
				</table>
				<br>

				<table width=100% cellspacing=0 cellpadding=0 border=0>
					<tr>
						<td id=menu align=center style='position: absolute; right: 50px'>&nbsp;</td>
					</tr>
				</table>

				<table width=100%>
					<? if ($kompl == 0): ?>
						<? if (count($inventory) > 0): ?>
							<? foreach ($inventory as $object): ?>
								<?

									$inf = $object->getInf();
									$min = $object->getMin();

								?>
								<tr>
									<td align="center">
										<div id="item_<?=$object->id ?>"></div>
										<script type="text/javascript">
											$('#item_<?=$object->id ?>').html(Show_Item('<?=$inf[0] ?>', '<?=$inf[1] ?>', <?=$object->id ?>, '<?=$inf[2] ?>', '<?=$inf[6] ?>', '<?=$inf[7] ?>', <?=$object->tip ?>, '<?=$inf[3] ?>', '<?=$min[0] ?>', '<?=$min[1] ?>', '<?=$min[2] ?>', '<?=$min[3] ?>', '<?=$min[4] ?>', '<?=$min[5] ?>', <?=$min[7] ?>, <?=$object->min_d ?>, <?=$object->max_d ?>, <?=$object->br1 ?>, <?=$object->br2 ?>, <?=$object->br3 ?>, <?=$object->br4 ?>, <?=$object->br5 ?>, <?=$object->br_m ?>, <?=$object->strength ?>, <?=$object->dex ?>, <?=$object->agility ?>, <?=$object->vitality ?>, <?=$object->razum ?>, <?=$object->krit ?>, <?=$object->unkrit ?>, <?=$object->uv ?>, <?=$object->unuv ?>, <?=$object->mkrit ?>, <?=$object->pblock ?>, <?=$object->mblock ?>, <?=$object->pbr ?>, <?=$object->kbr ?>, <?=$object->hp ?>, <?=$object->energy ?>, '<?=$object->about ?>', '<?=$object->otravl ?>', '<?=$object->use_mana ?>', ''));</script>
										<br>
									</td>
								</tr>
							<? endforeach; ?>
						<? else: ?>
							<tr>
								<td class="text-xs-center"><div class="alert alert-info" role="alert">Отдел рюкзака пуст.</div></td>
							</tr>
						<? endif; ?>
					<? else: ?>
						<tr>
							<td>
								<table width="100%">
									<tr>
										<td style="width:50%">
											<div align="right" class="hline"><img src="/images/main/lhrline.png" width="34" height="1" alt="" style="vertical-align: top"></div>
										</td>
										<td style="padding: 0 10px">
											<table>
												<tr>
													<td><img src="/images/main/lfl.gif" width="17" height="16" alt=""></td>
													<td valign="top" style="white-space: nowrap;" class="hitem">Комплекты</td>
													<td><img src="/images/main/rfl.gif" width="17" height="16" alt=""></td>
												</tr>
											</table>
										</td>
										<td style="width:50%">
											<div class="hline"><img src="/images/main/rhrline.png" width="34" height="1" alt="" style="vertical-align: top"></div>
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td style="text-align:center;">
								<form method="POST" action="<?=$this->url->get("edit/") ?>?do=compl">
									<table style="margin: 0 auto">
										<tr>
											<td>
												<table>
													<tr>
														<td><img src="/images/main/in_l.gif" width="20" height="35" alt=""></td>
														<td class="in_f">
															<input type="text" value="" placeholder="имя комплекта" class="tint" style="width:205px;" name="name">
														</td>
														<td><img src="/images/main/in_r.gif" width="20" height="35" alt=""></td>
													</tr>
												</table>
											</td>
											<td>
												<table>
													<tr>
														<td><img src="/images/main/btn_l.gif" width="19" height="26" alt=""></td>
														<td class="btn_f"><input type="submit" style="width:80px;" value="сохранить" class="int"></td>
														<td><img src="/images/main/btn_r.gif" width="19" height="26" alt=""></td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</form>
							</td>
						</tr>
						<tr>
							<td style="text-align:center;padding-top:5px;">
								<table border="0" cellspacing="1" cellpadding="1">
									<? foreach ($inventory as $c): ?>
									<!-- готовый комплект начало -->
									<tr>
										<td style="white-space: nowrap;padding-right: 20px"><b><?= $c->name ?></b></td>
										<td style="width: 100%; white-space: nowrap;">
											<div align="right" class="hline"><img src="/images/main/lhrline.png" width="34" height="1" style="vertical-align: top" alt=""/></div>
										</td>
										<td>
											<table border="0" >
												<tr>
													<td><img src="/images/main/btn_l.gif" width="19" height="26" alt="" border="0"/></td>
													<td class="btn_f">
														<input type="button" style="width:70px;" value="надеть" class="int" onclick="load('<?=$this->url->get("edit/") ?>?do=wear&id=<?=$c->id ?>')">
													</td>
													<td><img src="/images/main/btn_r.gif" width="19" height="26" alt="" border="0"/></td>
												</tr>
											</table>
										</td>
										<td>
											<table border="0" >
												<tr>
													<td><img src="/images/main/btn_l.gif" width="19" height="26" alt="" border="0"/></td>
													<td class="btn_f">
														<input type="button" style="width:70px;" value="удалить" class="int" onclick="load('<?=$this->url->get("edit/") ?>?do=wear&a=del&id=<?=$c->id ?>')">
													</td>
													<td><img src="/images/main/btn_r.gif" width="19" height="26" alt="" border="0"/></td>
												</tr>
											</table>
										</td>
									</tr>
									<? endforeach; ?>
								</table>
							</td>
						</tr>
					<? endif; ?>
				</table>
			</div>
		</td>
	</tr>
</table>