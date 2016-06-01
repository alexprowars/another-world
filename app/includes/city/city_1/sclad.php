<?

/**
 * @var \App\Game\Controllers\MapController $this
 */

$message = '';

$otdel = $this->request->get('otdel', 'int', 50);

if (isset($_GET['buy']))
        include("city/sclad/buy.php");
if (isset($_GET['sale']))
        include("city/sclad/sale_fnc.php");
if (isset($_GET['unsale']))
{
    $unsale = intval($_GET['unsale']);

	$this->db->query("UPDATE `game_objects` SET sclad = '0' WHERE `id` = '" . $unsale . "'");
	$this->db->query("DELETE FROM `game_sclad` WHERE `id`= '" . $unsale . "'");
}

$this->view->pick('shared/city/1_sclad');
$this->view->setVar('otdel', $otdel);
$this->view->setVar('message', $message);