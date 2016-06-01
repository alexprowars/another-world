<?php

use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Url as UrlProvider;
use Phalcon\Mvc\Application;
use Phalcon\DI\FactoryDefault;

define('APP_PATH', realpath('../..').'/');

ini_set('log_errors', 'On');
ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set('error_log', APP_PATH.'php_errors.log');

try
{
	$config = new \Phalcon\Config\Adapter\Ini(APP_PATH."app/config/config.ini");

	include (APP_PATH."app/config/loader.php");
	include (APP_PATH."app/config/services.php");

	$application = new Application($di);

	$result = [];

	$params = $application->request->getPost();
	ksort($params);
	unset($params['sig']);

	$s = '';

	foreach($params as $k => $v)
		$s .= $k.'='.$v;

	$signature = md5($s.$application->config->vk->secret);

	if (strcmp($application->request->getPost('sig'), $signature) == 0)
	{
		switch ($params['notification_type'])
		{
			case 'order_status_change':
			case 'order_status_change_test':

				if ($params['status'] == 'chargeable')
				{
					$orderId = intval($params['order_id']);

					$amount = intval($application->request->getPost('item_price'));

					$check = $application->db->query("SELECT id FROM game_users_payments WHERE transaction_id = '".$orderId."' AND user_id != 0")->fetch();

					if (!isset($check['id']))
					{
						$user = $application->db->query("SELECT u.id, u.username FROM game_users u, game_users_auth a WHERE u.id = a.user_id AND a.external_id LIKE '%vk.com/id".$params['user_id']."%'")->fetch();

						if (isset($user['id']))
						{
							if ($amount == 20 || $amount == 60 || $amount == 100 || $amount == 200 || $amount == 500)
								$amount += floor($amount * 0.1);

							if ($amount > 0)
							{
								$application->db->query("UPDATE game_users SET f_credits = f_credits + ".$amount." WHERE id = ".$user['id']."");
								$application->game->insertInChat('На ваш счет зачислено '.$amount.' платины', $user['username']);
								$application->db->query("INSERT INTO game_users_payments (user_id, call_id, method, transaction_id, transaction_time, uid, amount, product_code) VALUES (".$user['id'].", '".$params['receiver_id']."', 'vk:".$params['notification_type']."', '".$orderId."', '".date("Y-m-d H:i:s", $params['date'])."', '".$params['user_id']."', ".$amount.", '".addslashes(json_encode($_POST))."')");

								$result['response'] = ['order_id' => $orderId, 'app_order_id' => $application->db->lastInsertId()];
							}
							else
								$result['error'] = ['error_code' => 11, 'error_msg' => 'В запросе нет необходимых полей', 'critical' => true];
						}
						else
							$result['error'] = ['error_code' => 22, 'error_msg' => 'Пользователя не существует', 'critical' => true];
					}
					else
						$result['response'] = ['order_id' => $orderId, 'app_order_id' => $check['id']];
				}
				else
					$result['error'] = ['error_code' => 100, 'error_msg' => 'Передано непонятно что вместо chargeable.', 'critical' => true];

			break;
			default:
				$result['error'] = ['error_code' => 11, 'error_msg' => 'В запросе нет необходимых полей', 'critical' => true];
		}
	}
	else
		$result['error'] = ['error_code' => 10, 'error_msg' => 'Несовпадение вычисленной и переданной подписи', 'critical' => true];

	$application->response->setJsonContent($result);
	$application->response->setContentType('text/json', 'utf8');
	$application->response->send();
}
catch(\Exception $e)
{
	echo "PhalconException: ", $e->getMessage();
	echo "<br>".$e->getFile();
	echo "<br>".$e->getLine();
}

?>