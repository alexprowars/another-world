<table class='w100 rating' cellspacing='0'>
<Tr class=c2>
<Td class=h width='75%' colspan='1' style='border-bottom:1px solid #D6AF92'>Игрок</td>
<td class='h value' width='25%' colspan='2' style='border-bottom:1px solid #D6AF92'>Рейтинг</td>
</tr>

<? foreach ($topUsers as $ii => $data): ?>
	<tr class="c<?=($ii%2 + 1) ?>">
		<Td colspan='2' style='border-right:0'><nowrap><a href='/inf.php?login=<?=$data['username'] ?>' target=_blank class=texts><?=$data['username'] ?></a></nowrap></td><td class=value style='border-left:0'><?=$data['reit'] ?></td>
	</tr>
<? endforeach; ?>

</table>