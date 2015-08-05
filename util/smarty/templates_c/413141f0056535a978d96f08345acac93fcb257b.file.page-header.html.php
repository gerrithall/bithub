<?php /* Smarty version Smarty-3.1.21-dev, created on 2015-07-30 14:25:13
         compiled from "/var/www/bithub/template/page-header.html" */ ?>
<?php /*%%SmartyHeaderCode:132005140255b9bc46147a88-03823495%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '413141f0056535a978d96f08345acac93fcb257b' => 
    array (
      0 => '/var/www/bithub/template/page-header.html',
      1 => 1438280711,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '132005140255b9bc46147a88-03823495',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.21-dev',
  'unifunc' => 'content_55b9bc461549d6_91161275',
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_55b9bc461549d6_91161275')) {function content_55b9bc461549d6_91161275($_smarty_tpl) {?><div class='u-full-width head'>
	<table width='100%'><tr><td> &nbsp; </td><td style='text-align: left;'>
		&nbsp; &nbsp; <a href='/' class='logo'>Bithub</a>
	</td>
		<td style='text-align: right;'>
		<a href='http://github.com/<?php echo $_SESSION['user']['github_login'];?>
' target='_blank' style='text-decoration: none;'>
			<img src='<?php echo $_SESSION['user']['avatar_url'];?>
' width='32' height='32'> 
			<strong><?php echo $_SESSION['user']['github_login'];?>
</strong>
		</a>
		<span style='font-size: 10px; margin-left: 5px;'><a href='/logout'>Logout</a> </span>
		
		</td>
		<td> &nbsp; </td>
	</table>
</div>


<?php }} ?>
