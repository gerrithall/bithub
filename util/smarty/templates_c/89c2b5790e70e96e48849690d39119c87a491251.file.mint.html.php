<?php /* Smarty version Smarty-3.1.21-dev, created on 2015-08-05 18:53:22
         compiled from "/var/www/bithub/template/mint.html" */ ?>
<?php /*%%SmartyHeaderCode:166808489055aee9fdb2e683-56038381%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '89c2b5790e70e96e48849690d39119c87a491251' => 
    array (
      0 => '/var/www/bithub/template/mint.html',
      1 => 1438815194,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '166808489055aee9fdb2e683-56038381',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.21-dev',
  'unifunc' => 'content_55aee9fdb5d233_72271283',
  'variables' => 
  array (
    'project_name' => 0,
    'repo' => 0,
    'date_minted' => 0,
    'asset_hash' => 0,
    'equity' => 0,
    'k' => 0,
    'g' => 0,
    'contributors' => 0,
    'cal' => 0,
    'today' => 0,
    'k2' => 0,
    'hash' => 0,
    'status' => 0,
    'btc_address' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_55aee9fdb5d233_72271283')) {function content_55aee9fdb5d233_72271283($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>

<?php echo $_smarty_tpl->getSubTemplate ("page-header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>

<div class='container'>
  <div class='row'>
  	<div class='twelve columns centered'>
		<h1><?php echo $_smarty_tpl->tpl_vars['project_name']->value;?>
</h1>
   		<h2>Mint Your Coin</h2>
	</div>
  </div>
   <div class='row'>
   <?php if ($_smarty_tpl->tpl_vars['repo']->value['status']==4) {?>
 	<div class='twelve columns centered'>
		Coin minted on <?php echo $_smarty_tpl->tpl_vars['date_minted']->value;?>
 <br/>
		<a href='http://btc.blockr.io/tx/info/<?php echo $_smarty_tpl->tpl_vars['asset_hash']->value;?>
' target='_blank'><?php echo $_smarty_tpl->tpl_vars['asset_hash']->value;?>
</a>
		<br/><br/>

		<strong>Top Earners:</strong>
		<center>
		<table class='commits'>

		<?php if ($_smarty_tpl->tpl_vars['equity']->value) {?>
			<?php  $_smarty_tpl->tpl_vars["g"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["g"]->_loop = false;
 $_smarty_tpl->tpl_vars["k"] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['equity']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["g"]->key => $_smarty_tpl->tpl_vars["g"]->value) {
$_smarty_tpl->tpl_vars["g"]->_loop = true;
 $_smarty_tpl->tpl_vars["k"]->value = $_smarty_tpl->tpl_vars["g"]->key;
?>
			<tr><td><?php echo $_smarty_tpl->tpl_vars['k']->value;?>
</td><td><?php echo $_smarty_tpl->tpl_vars['g']->value;?>
%</td></tr>
			<?php } ?>
		<?php } else { ?>
			<tr><td><strong>Nobody 

			   	<span class='failure'><i class='fa fa-frown-o fa-2x'></i></span>
			</strong>
			<br/><br/>There have been no commits since the coin was minted
			
			</td></tr>
		<?php }?>
		</table>
		
		</center>
		<hr>
		<center><strong>History</strong>
		<table class='commits' width='100%'>
	 	  <tr>
		  	<th></th>
		<?php  $_smarty_tpl->tpl_vars["g"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["g"]->_loop = false;
 $_smarty_tpl->tpl_vars["k"] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['contributors']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["g"]->key => $_smarty_tpl->tpl_vars["g"]->value) {
$_smarty_tpl->tpl_vars["g"]->_loop = true;
 $_smarty_tpl->tpl_vars["k"]->value = $_smarty_tpl->tpl_vars["g"]->key;
?>
			<th><?php echo $_smarty_tpl->tpl_vars['k']->value;?>
</th>
		<?php } ?>
		  </tr>
		<?php  $_smarty_tpl->tpl_vars["g"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["g"]->_loop = false;
 $_smarty_tpl->tpl_vars["k"] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['cal']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["g"]->key => $_smarty_tpl->tpl_vars["g"]->value) {
$_smarty_tpl->tpl_vars["g"]->_loop = true;
 $_smarty_tpl->tpl_vars["k"]->value = $_smarty_tpl->tpl_vars["g"]->key;
?>
		  <tr <?php if ($_smarty_tpl->tpl_vars['k']->value==$_smarty_tpl->tpl_vars['today']->value) {?>class='today'<?php }?>>
			<td><?php echo $_smarty_tpl->tpl_vars['k']->value;?>
</td>
			<?php  $_smarty_tpl->tpl_vars["g2"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["g2"]->_loop = false;
 $_smarty_tpl->tpl_vars["k2"] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['contributors']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["g2"]->key => $_smarty_tpl->tpl_vars["g2"]->value) {
$_smarty_tpl->tpl_vars["g2"]->_loop = true;
 $_smarty_tpl->tpl_vars["k2"]->value = $_smarty_tpl->tpl_vars["g2"]->key;
?>
			<td>
			   <?php if ($_smarty_tpl->tpl_vars['g']->value[$_smarty_tpl->tpl_vars['k2']->value]>0) {?>
			  	<span class='success'><i class='fa fa-smile-o fa-2x'></i></span>
			   <?php } else { ?>
			   	<span class='failure'><i class='fa fa-frown-o fa-2x'></i></span>
			   <?php }?>
			</td>
			<?php } ?>
		  </tr>
		<?php } ?>
	</table></center>
	</div>
   <?php } elseif ($_smarty_tpl->tpl_vars['repo']->value['status']==3) {?>
   	<div class='twelve columns centered'>
	Transaction Confirmed!  Minting your coin.
	</div>
   <?php } elseif ($_smarty_tpl->tpl_vars['hash']->value) {?>
   	<div class='twelve columns centered'>
	Transaction detected... <?php echo $_smarty_tpl->tpl_vars['status']->value;?>
 confirmation<?php if ($_smarty_tpl->tpl_vars['status']->value!=1) {?>s<?php }?> detected (6 necessary).
	<br/><br/>
	<a href='http://btc.blockr.io/tx/info/<?php echo $_smarty_tpl->tpl_vars['hash']->value;?>
' target='_blank'>Blockchain : <?php echo $_smarty_tpl->tpl_vars['hash']->value;?>
</a>
	</div>

   <?php } else { ?>
  	<div class='four columns centered'>
		<h3>1.</h3>
		Send BTC to <?php echo $_smarty_tpl->tpl_vars['btc_address']->value;?>
 (minimum .01).
	</div>
   	<div class='four columns centered'>
		<h3>2.</h3>
		We'll create a colored coin especially for your project.
	</div>
	<div class='four columns centered'>
		<h3>3.</h3>
		This coin will automatically be to contributors next 30 days. 
	</div>
	<?php }?>

  </div>
  

<?php echo $_smarty_tpl->getSubTemplate ("footer.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>

<?php }} ?>
