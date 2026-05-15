<template>
	<table>
		<tr>
			<td>
				<table width="100%">
					<tr>
						<td style="width:50%">
							<div align="right" class="hline"><img src="/assets/images/main/lhrline.png" width="34" height="1" alt="" style="vertical-align: top"></div>
						</td>
						<td style="padding: 0 10px">
							<table>
								<tr>
									<td><img src="/assets/images/main/lfl.gif" width="17" height="16" alt=""></td>
									<td valign="top" style="white-space: nowrap;" class="hitem">Комплекты</td>
									<td><img src="/assets/images/main/rfl.gif" width="17" height="16" alt=""></td>
								</tr>
							</table>
						</td>
						<td style="width:50%">
							<div class="hline"><img src="/assets/images/main/rhrline.png" width="34" height="1" alt="" style="vertical-align: top"></div>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td style="text-align:center;">
				<form method="POST" action="/person/inventory?do=compl">
					<table style="margin: 0 auto">
						<tr>
							<td>
								<table>
									<tr>
										<td><img src="/assets/images/main/in_l.gif" width="20" height="35" alt=""></td>
										<td class="in_f">
											<input type="text" value="" placeholder="имя комплекта" class="tint" style="width:205px;" name="name">
										</td>
										<td><img src="/assets/images/main/in_r.gif" width="20" height="35" alt=""></td>
									</tr>
								</table>
							</td>
							<td>
								<table>
									<tr>
										<td><img src="/assets/images/main/btn_l.gif" width="19" height="26" alt=""></td>
										<td class="btn_f"><input type="submit" style="width:80px;" value="сохранить" class="int"></td>
										<td><img src="/assets/images/main/btn_r.gif" width="19" height="26" alt=""></td>
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
					? foreach ($inventory as $c): ?
					<!-- готовый комплект начало -->
					<tr>
						<td style="white-space: nowrap;padding-right: 20px"><b>?= $c->name ?</b></td>
						<td style="width: 100%; white-space: nowrap;">
							<div align="right" class="hline"><img src="/assets/images/main/lhrline.png" width="34" height="1" style="vertical-align: top" alt=""/></div>
						</td>
						<td>
							<table border="0">
								<tr>
									<td><img src="/assets/images/main/btn_l.gif" width="19" height="26" alt="" border="0"/></td>
									<td class="btn_f">
										<input type="button" style="width:70px;" value="надеть" class="int" onclick="/person/inventory?do=wear&id=">
									</td>
									<td><img src="/assets/images/main/btn_r.gif" width="19" height="26" alt="" border="0"/></td>
								</tr>
							</table>
						</td>
						<td>
							<table border="0">
								<tr>
									<td><img src="/assets/images/main/btn_l.gif" width="19" height="26" alt="" border="0"/></td>
									<td class="btn_f">
										<input type="button" style="width:70px;" value="удалить" class="int" onclick="load('/person/inventory?do=wear&a=del&id=?=$c->id ?')">
									</td>
									<td><img src="/assets/images/main/btn_r.gif" width="19" height="26" alt="" border="0"/></td>
								</tr>
							</table>
						</td>
					</tr>
					? endforeach; ?
				</table>
			</td>
		</tr>
	</table>
</template>

<script setup>

</script>