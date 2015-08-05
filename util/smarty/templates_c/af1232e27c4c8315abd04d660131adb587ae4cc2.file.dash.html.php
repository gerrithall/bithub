<?php /* Smarty version Smarty-3.1.21-dev, created on 2015-08-05 17:27:45
         compiled from "/var/www/bithub/template/dash.html" */ ?>
<?php /*%%SmartyHeaderCode:56023519355aed7087f1908-74702396%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'af1232e27c4c8315abd04d660131adb587ae4cc2' => 
    array (
      0 => '/var/www/bithub/template/dash.html',
      1 => 1438810058,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '56023519355aed7087f1908-74702396',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.21-dev',
  'unifunc' => 'content_55aed7088346a7_92104671',
  'variables' => 
  array (
    'repos' => 0,
    'r' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_55aed7088346a7_92104671')) {function content_55aed7088346a7_92104671($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>

<?php echo $_smarty_tpl->getSubTemplate ("page-header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>

<div class='container'>
  <div class='row'>
  	<div class='twelve columns centered' style='margin-top: 30px;'>
		<h1>Dashboard</h1>
	</div>
  </div>

  <div class='row'>
  	<div class='twelve columns centered'>
		<table class='dash'>
		<tr><th style='padding: 5px;'>Repo</th><th>Notes</th><th>Status</th></tr>
<?php  $_smarty_tpl->tpl_vars["r"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["r"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['repos']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["r"]->key => $_smarty_tpl->tpl_vars["r"]->value) {
$_smarty_tpl->tpl_vars["r"]->_loop = true;
?>
			<tr>
			  <td style='padding: 5px;'><a target='_blank' style='color: #B3E5FC' href='<?php echo $_smarty_tpl->tpl_vars['r']->value['url'];?>
'> <?php echo $_smarty_tpl->tpl_vars['r']->value['fullname'];?>
</a></td>
			  <?php if ($_smarty_tpl->tpl_vars['r']->value['status']==4) {?>
			  <td>
			  	<a href='https://www.coinprism.info/tx/<?php echo $_smarty_tpl->tpl_vars['r']->value['transaction_hash'];?>
' target='_blank'>Minted!</a>
			  </td>
			   <td>
			  	<a class='button' href='/mint/<?php echo $_smarty_tpl->tpl_vars['r']->value['fullname'];?>
'>View</a>
			  </td>

			  <?php } elseif ($_smarty_tpl->tpl_vars['r']->value['status']==3) {?>
			  <td>
			  	<a href='https://www.coinprism.info/tx/<?php echo $_smarty_tpl->tpl_vars['r']->value['transaction_hash'];?>
' target='_blank'>View Progress</a>
			  </td>
			  <td>
			  	<a class='button' href='/mint/<?php echo $_smarty_tpl->tpl_vars['r']->value['fullname'];?>
'>Coloring</a>
			  </td>
			  <?php } elseif ($_smarty_tpl->tpl_vars['r']->value['status']==2) {?>
			  <td>
			  	<a target='_blank' href='http://btc.blockr.io/tx/info/<?php echo $_smarty_tpl->tpl_vars['r']->value['transaction_hash'];?>
'>Transaction detected.</a>  <?php echo $_smarty_tpl->tpl_vars['r']->value['confirmations'];?>
 confirmation<?php if ($_smarty_tpl->tpl_vars['r']->value['confirmations']!=1) {?>s<?php }?> out of 6 necessary.
			  </td>
			  <td>
			  	<a class='button' href='/mint/<?php echo $_smarty_tpl->tpl_vars['r']->value['fullname'];?>
'>Confirming</a>
			  </td>	
			  <?php } elseif ($_smarty_tpl->tpl_vars['r']->value['status']) {?>
			  <td>
				Receiving BTC at <?php echo $_smarty_tpl->tpl_vars['r']->value['address'];?>

			  </td>
			  <td>
			  	<a class='button' href='/mint/<?php echo $_smarty_tpl->tpl_vars['r']->value['fullname'];?>
'>Pending</a>
			  </td>
			  <?php } else { ?>
			  <td>
			  	
			  </td>
			  <td>
			  	<a class="button button-primary" href='/mint/<?php echo $_smarty_tpl->tpl_vars['r']->value['fullname'];?>
'>Mint Coin</a>
			  </td>

			  <?php }?>
			  </td>
			</tr>
<?php } ?>
		</table>
	</div>
  </div>



<?php echo $_smarty_tpl->getSubTemplate ("footer.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>

<?php }} ?>
