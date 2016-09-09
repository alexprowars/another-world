<div id="contanier">
	<div id="centerBlock">
		<table class="table">
			<tr>
				<td valign="top" class="textBlock">
					<center>
						<font class=title>Энциклопедия</font><br>
						<? if ($otdel == 20)
							echo "Таблица опыта";
						elseif ($otdel == 21)
							echo "Работа";
						elseif ($otdel == 22)
							echo "Академия";
						elseif ($otdel == 100)
							echo "Открытки";
						elseif ($otdel == 101)
							echo "Букеты";
						elseif ($otdel == 102)
							echo "Подарки";
						elseif ($otdel > 0 && $otdel != 200)
							echo "Ассортимент товаров отдела \"" . _getText('weapon', $otdel) . "\"&nbsp;";
						else
							echo "Ассортимент Ваших предметов&nbsp;";
						?>
					</center>
					<br>
					<?

						if ($otdel == 21)
							include(ROOT_PATH.'/app/includes/library/works.php');
						elseif ($otdel == 22)
							include(ROOT_PATH.'/app/includes/library/academy.php');
						elseif ($otdel == 23)
							include(ROOT_PATH.'/app/includes/library/mf.php');
						else
							include(ROOT_PATH.'/app/includes/library/_otdels.php');

					?>
				</td>
				<td valign="top" class="rightColumn">
					<table border="0"  class="boxTable">
						<tr>
							<td class="menuTitle">Библиотека</td>
						</tr>
						<tr>
							<td class="boxContent">
								<div style="display:inline;">
									<ul class="uz">
										<li class="m">
											<font class="m" color="#B27D5D"><b><u>Оружие</b></u></font>
											<ul class="uz">
												<li class="m"><a class="m" href="{{ url('library/') }}?otdel=1">Ножи и кинжалы</a></li>
												<li class="m"><a class="m" href="{{ url('library/') }}?otdel=2">Мечи</a></li>
												<li class="m"><a class="m" href="{{ url('library/') }}?otdel=3">Топоры и Секиры</a></li>
												<li class="m"><a class="m" href="{{ url('library/') }}?otdel=4">Дубины и Булавы</a></li>
												<li class="m"><a class="m" href="{{ url('library/') }}?otdel=5">Луки и Арбалеты</a></li>
											</ul>
										</li>
										<li class="m">
											<font class="m" color="#B27D5D"><b><u>Доспехи</b></u></font>
											<ul class="uz">
												<li class="m"><a class="m" href="{{ url('library/') }}?otdel=6">Шлема</a></li>
												<li class="m"><a class="m" href="{{ url('library/') }}?otdel=7">Рубахи</a></li>
												<li class="m"><a class="m" href="{{ url('library/') }}?otdel=8">Броня</a></li>
												<li class="m"><a class="m" href="{{ url('library/') }}?otdel=16">Нарукавники</a></li>
												<li class="m"><a class="m" href="{{ url('library/') }}?otdel=9">Перчатки</a></li>
												<li class="m"><a class="m" href="{{ url('library/') }}?otdel=10">Щиты</a></li>
												<li class="m"><a class="m" href="{{ url('library/') }}?otdel=11">Пояса</a></li>
												<li class="m"><a class="m" href="{{ url('library/') }}?otdel=17">Штаны</a></li>
												<li class="m"><a class="m" href="{{ url('library/') }}?otdel=12">Обувь</a></li>
											</ul>
										</li>
										<li class="m">
											<font class="m" color="#B27D5D"><b><u>Ювелирные изделия</b></u></font>
											<ul class="uz">
												<li class="m"><a class="m" href="{{ url('library/') }}?otdel=13">Ожерелья</a></li>
												<li class="m"><a class="m" href="{{ url('library/') }}?otdel=14">Кольца</a></li>
												<li class="m"><a class="m" href="{{ url('library/') }}?otdel=15">Серьги</a></li>
											</ul>
										</li>
										<li class="m">
											<font class="m" color="#B27D5D"><b><u>Подарки</b></u></font>
											<ul class="uz">
												<li class="m"><a class="m" href="{{ url('library/') }}?otdel=101">Букеты</a></li>
												<li class="m"><a class="m" href="{{ url('library/') }}?otdel=100">Открытки</a></li>
												<li class="m"><a class="m" href="{{ url('library/') }}?otdel=102">Подарки</a></li>
											</ul>
										</li>
										<li class="m">
											<font class="m" color="#B27D5D"><b><u>Прочее</b></u></font>
											<ul class="uz">
												<li class="m"><a class="m" href="{{ url('library/') }}?otdel=30">Ресурсы</a></li>
												<li class="m"><a class="m" href="{{ url('library/') }}?otdel=31">Драг. камни</a></li>
											</ul>
										</li>
										<li class="m">
											<font class="m" color="#B27D5D"><b><u>Справочная</b></u></font>
											<ul class="uz">
												<li class="m"><a class="m" href="exp.php" target=_blank>Таблица опыта</a></li>
												<li class="m"><a class="m" href="{{ url('library/') }}?otdel=21">Центр занятости</a></li>
												<li class="m"><a class="m" href="{{ url('library/') }}?otdel=22">Академия</a></li>
												<li class="m"><a class="m" href="{{ url('library/') }}?otdel=23">Модификаторы</a></li>
											</ul>
										</li>
									</ul>
								</div>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>
</div>