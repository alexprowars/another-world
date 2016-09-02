<?

/**
 * @var \Game\Controllers\MapController $this
 */

use Game\Models\ShopItems;

define('SHOP_ID', 3);

$otdel = $this->request->get('otdel', 'int', 0);

$message = '';

if ($this->request->has('buy') && $this->request->get('buy', 'int') > 0)
{
	$item = ShopItems::findFirst($this->request->get('buy', 'int'));
	$message = $item->buy();
}

$objects = array();

if ($otdel == 0)
{
	$builder = $this->modelsManager->createBuilder();

	$objects =  $builder->from(['item' => 'Game\Models\Items', 'shop' => 'Game\Models\ShopItems'])
						->where('item.id = shop.item_id AND shop.shop_id = :shop: AND shop.cnt > 0 AND item.min_level = :level:', Array('shop' => SHOP_ID, 'level' => $this->user->level))
						->orderBy('shop.group_id ASC')
						->getQuery()->execute();
}
elseif ($otdel < 40)
{
	$builder = $this->modelsManager->createBuilder();

	$objects =  $builder->from(['item' => 'Game\Models\Items', 'shop' => 'Game\Models\ShopItems'])
						->where('item.id = shop.item_id AND shop.shop_id = :shop: AND shop.group_id = :group:', Array('shop' => SHOP_ID, 'group' => $otdel))
						->orderBy('item.min_level ASC')
						->getQuery()->execute();
}

$this->view->pick('shared/city/1_mshop');

$this->view->setVar('otdel', $otdel);
$this->view->setVar('objects', $objects);
$this->view->setVar('message', $message);