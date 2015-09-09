<?
Gbl::store('default_controller', 'HomeController');

$controller_array = array(
	'auth' => 'LoginController',
	'a' => 'AssetController',
	'asset' => 'AssetController',
	'mint' => 'MintController',
	'dashboard' => 'DashboardController',
	'logout' => 'LogoutController',
	'btc' => 'BTCController',
	'donotemail' => 'UnsubscribeController',
	'terms' => 'TermsController'
);

Gbl::store('controllers_array',$controller_array);

?>
