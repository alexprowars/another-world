<table width="100%">
	<tr>
		<td><img src='/images/main/lp.jpg' width='40' height='28' alt=''></td>
		<td class='nmenus' style='white-space: nowrap; width:50%'>
			<div style="width: 250px">
				{% if credits is defined or f_credits is defined %}
					У вас с собой денег:
					{% if credits is defined %}
						<font color='#EDE2BE'>{{ credits }} зол.</font>
					{% endif %}
					{% if credits is defined and f_credits is defined %}
						|
					{% endif %}
					{% if f_credits is defined %}
						<font color='#EDE2BE'>{{ f_credits }} пл.</font>
					{% endif %}
				{% endif %}
			</div>
		</td>
		<td align='center' valign='top' class='nmenus'>
			<table>
				<tr>
					<td><img src='/images/main/lpl.jpg' width='40' height='28' alt=''></td>
					<td class='l_z_f'>{{ title }}</td>
					<td><img src='/images/main/rpr.jpg' width='40' height='28' alt=''></td>
				</tr>
			</table>
		</td>
		<td class='nmenus' style='white-space: nowrap; width:50%; text-align:right;'><div style="width: 250px"></div></td>
		<td><img src='/images/main/rp.jpg' width='40' height='28' alt=''></td>
	</tr>
</table>