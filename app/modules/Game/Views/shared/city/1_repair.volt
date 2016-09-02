{{ partial('shared/city_header', ['title': 'Кузница', 'credits': user.credits]) }}

<div class="textblock">
	<div class="row">
		<div class="col-xs-12 text-xs-right">
			<a href="{{ url('map/') }}?otdel=<?=$otdel ?>"><img src='/images/images/refresh.gif' alt='Обновить'></a>
			<a href="{{ url('map/') }}?refer=11"><img src='/images/images/back.gif' alt='Вернуться'></a>
		</div>
	</div>
	<div class="clearfix"></div>

	<? if ($this->user->r_time > time()): ?>
		<script src='/img/js/time.js'></script>
		<center>
			<table cellspacing=0 cellpadding=3>
				<tr>
					<td><font color=red><b>Оставшееся время:</b></font></td>
					<td id=know style='COLOR: red; FONT-WEIGHT: Bold; TEXT-DECORATION: Underline'></td>
				</tr>
			</table>
		</center>
		<script>ShowTime('know', "<?=($this->user->r_time - time()) ?>");</script>
	<? endif; ?>

	{% if message is not empty %}
		<p class="message bg-danger">{{ message }}</p>
	{% endif %}

	<ul class="nav nav-tabs">
		<li role="presentation" class="<?=($otdel == 1 ? 'active' : '') ?>"><a href="{{ url('map/') }}?otdel=1">Починка вещей</a></li>
		<? if ($this->user->proff == 3): ?>
	 		<li role="presentation" class="<?=($otdel == 2 ? 'active' : '') ?>"><a href="{{ url('map/') }}?otdel=2">Огранка камней</a></li>
		<? endif; ?>
	  	<li role="presentation" class="<?=($otdel == 3 ? 'active' : '') ?>"><a href="{{ url('map/') }}?otdel=3">Гравировка и модернизация вещей</a></li>
		<? if ($this->user->proff == 2): ?>
			<li role="presentation" class="<?=($otdel == 4 ? 'active' : '') ?>"><a href="{{ url('map/') }}?otdel=4">Кузнечное дело</a></li>
		<? endif; ?>
	</ul>

	<div class="tab-content">
		<div class="tab-pane fade in active">
			<? if ($otdel > 0 && isset($objects)): ?>
				<? if ($otdel == 1): ?>
					{{ partial('shared/city/repair/repair', ['objects': objects]) }}
				<? elseif ($otdel == 2): ?>
					{{ partial('shared/city/repair/cut', ['objects': objects]) }}
				<? elseif ($otdel == 3): ?>
					{{ partial('shared/city/repair/etching', ['objects': objects]) }}
				<? elseif ($otdel == 4): ?>
					{{ partial('shared/city/repair/insert', ['objects': objects, 'weapons': weapons]) }}
				<? endif; ?>
			<? endif; ?>
		</div>
	</div>
	<div class="clearfix"></div>
</div>