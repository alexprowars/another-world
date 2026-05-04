<table align="center"><tr align="center"><td align="center">
<small>Хмурое небо со смутной луной<br>
И град стоит на грани войны<br>
Битвы кровавые, между светом и тьмой<br>
Крови пролито, и на могилах цветы<br>
<br>
Тьма сеет хаос, вампиры их слуги<br>
До смерти кусают обычных людей<br>
Приносят лишь ненависть, и страшные муки<br>
Они сродни разьяренных зверей<br>
<br>
Свет, охраняет и чистит от смуты<br>
Души людей проклятых тьмой<br>
Исцеляет,но судьбы неволей согнуты<br>
Искорежены, несмиренной войной<br>
<br>
Ты стоишь на развилке путей<br>
Есть выбор Добрый Свет, или Лживая Тьма<br>
А быть может будешь верен себе<br>
Другой Мир, все равно тут Судьба<br>
</small></td></tr></table><br><br>
<?

foreach ($news as $data)
{
	echo "
		<span class='title'>".$data['name']."</span>
		<span class='date'>
			<span class='author'>Автор: <a href='/info/?login=".$data['autor']."' target='_blank'>".$data['autor']."</a></span>
			".date("d-m-Y", $data['time'])."
		</span>
		<p>".stripslashes($data["text"])."</p>
		<br style='clear: both;'/>
		<div class='separator'></div>
		<br>";
}


if (!empty($pagination))
	echo "<br><div align='center'>Страницы: ".$pagination."</div>";

?>