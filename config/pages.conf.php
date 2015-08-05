<?
Gbl::store('default_controller', 'HomeController');

$controller_array = array(
	'auth' => 'LoginController',
	'a' => 'AssetController',
	'asset' => 'AssetController',
	'mint' => 'MintController',
	'dashboard' => 'DashboardController',
	'logout' => 'LogoutController',
	'btc' => 'BTCController'
);

Gbl::store('controllers_array',$controller_array);

?>
