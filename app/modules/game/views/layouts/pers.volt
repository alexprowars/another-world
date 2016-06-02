<table class="table np">
	<tr>
		{{ user.getPersonBlock() }}
		<td valign="top">
			<? if (isset($message) && $message != ''): ?>
				<br>
				<center><font color=red><b><?= $message ?></b></font></center><br>
			<? endif; ?>
			{{ partial('shared/person_menu') }}
			{{ content() }}
		</td>
	</tr>
</table>