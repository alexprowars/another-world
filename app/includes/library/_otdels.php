<?

/**
 * @var \App\Controllers\ControllerBase $this
 */

define('SHOP_ID', 1);

$objects = array();

if (!empty($otdel))
{
	$builder = $this->modelsManager->createBuilder();

	$objects =  $builder->from(['item' => 'App\Models\Items', 'shop' => 'App\Models\ShopItems'])
						->where('item.id = shop.item_id AND shop.shop_id = :shop: AND shop.group_id = :group:', Array('shop' => SHOP_ID, 'group' => $otdel))
						->orderBy('item.min_level ASC')
						->getQuery()->execute();
}

$this->view->setVar('otdel', $otdel);
$this->view->setVar('objects', $objects);
$this->view->partial('library/objects', Array('objects' => $objects, 'otdel' => $otdel));

?>