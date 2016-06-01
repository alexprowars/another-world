<? if (count($objects)): ?>
	<table class='shopitems'>
		<tr>
			<? foreach ($objects as $i => $object): ?>
				<? if ($i > 0 && $i % 2 == 0): ?>
					</tr><tr>
				<? endif; ?>
				<td valign="top" height="100%" width="50%">
					<div style="height:100%;background: url(/images/inman_fon2.gif)">
						<? $this->view->partial('shared/shop_item', Array('object' => $object, 'type' => 1)); ?>
					</div>
				</td>
			<? endforeach; ?>
			<? if (count($objects) == 0): ?>
				<td>В данном отделе нет товаров.</td>
			<? endif; ?>
		</tr>
	</table>
<? endif; ?>