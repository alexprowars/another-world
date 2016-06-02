{{ partial('shared/city_header', ['title': 'Складское Помещение']) }}
<table border='0' cellspacing='0' cellpadding='0' class='tmain'>
	<tr>
		<td valign='top' style='text-align:center;'>

			<table align='center' class='ltable' style='width:98%;' background='/img/game/main/bodyfon.jpg'>
				<tr>
					<td colspan='3' valign='top' style='width:100%;'>
						<table style="width: 100%">
							<tr>
								<td><img src='/img/game/main/lp.jpg' width='40' height='28' border='0' alt=''/></td>
								<td class='nmenus' style='white-space: nowrap; width:50%'></td>
								<td align='center' valign='top' class='nmenus'>
									<table border='0' cellspacing='0' cellpadding='0'>
										<tr>
											<td><img src='/img/game/main/lpl.jpg' width='40' height='28' alt='' border='0'/></td>
											<td class='l_z_f'>Складское Помещение</td>
											<td><img src='/img/game/main/rpr.jpg' width='40' height='28' alt='' border='0'/></td>
										</tr>
									</table>
								</td>
								<td class='nmenus' style='white-space: nowrap; width:50%; text-align:right;'></td>
								<td><img src='/img/game/main/rp.jpg' width='40' height='28' border='0' alt=''/></td>
							</tr>
						</table>
						<br>
					</td>
				</tr>
				<tr>
					<td valign=top bgcolor=efdcb8 class=textblock>
						<table width=100% cellspacing=0 cellpadding=5 border=0>
							<tr>
								<td align=right valign=top>
									<img src='/images/images/back.gif' style='CURSOR: Hand' alt='Вернуться на склад' onclick='window.location.href="/map/?room=28"'>
									<img src='/images/images/shop_sale.gif' style='CURSOR: Hand' alt='Поместить на хранение' onclick='window.location.href="/map/?otdel=100"'>
									<img src='/images/images/refresh.gif' style='CURSOR: Hand' alt='Обновить' onclick='window.location.href="/map/?otdel=<?=$otdel ?>'>
									<img src='/images/images/back.gif' style='CURSOR: Hand' alt='Вернуться' onclick='window.location.href="/map/?refer=28"'>
								</td>
							</tr>
						</table>
						<table width=100% cellspacing=0 cellpadding=3 border=0>
							<tr>
								<td align=right>

									{% if message is not empty %}
										<p class="message alert-danger">{{ message }}</p>
									{% endif %}

									<table width=100% cellspacing=0 cellpadding=3 border=0>
										<tr>
											<td valign=top>
												<FIELDSET>
													<LEGEND><a class=ch>
															<? if ($otdel != 100) echo" Содержимое склада&nbsp;";
															else echo"Ассортимент Ваших предметов&nbsp;"; ?>

															</a></LEGEND>

													<table width=100% cellspacing=0 cellpadding=5 border=0>
														<tr>
															<td>
																<table width=100% cellspacing=0 border=0 cellpadding=5 style='border-style: outset; border-width: 2'>
																	<tr>
																		<td>

																			<? if ($otdel == 100)
																				include(ROOT_DIR.APP_PATH . 'controllers/mapPage/city_1/sclad/sale.php');
																			else
																				include(ROOT_DIR.APP_PATH . 'controllers/mapPage/city_1/sclad/_otdels.php');
																			?>
																		</td>
																	</tr>
																</table>

															</td>
														</tr>
													</table>
												</FIELDSET>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>