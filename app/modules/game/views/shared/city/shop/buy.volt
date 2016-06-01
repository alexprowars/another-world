<? if (count($objects)): ?>
	<div class="shop-items row">
		<? foreach ($objects as $i => $object): ?>
			<div class="col-xs-6 col-xl-3 col-lg-4">
				<? $this->view->partial('shared/shop_item', Array('object' => $object, 'type' => 1)); ?>
			</div>
		<? endforeach; ?>

		<? if (count($objects) == 0): ?>
			<div>В данном отделе нет товаров.</div>
		<? endif; ?>
	</div>
<? endif; ?>