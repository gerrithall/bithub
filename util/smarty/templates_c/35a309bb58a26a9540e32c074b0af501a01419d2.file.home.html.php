<?php /* Smarty version Smarty-3.1.21-dev, created on 2015-08-05 19:04:29
         compiled from "/var/www/bithub/template/home.html" */ ?>
<?php /*%%SmartyHeaderCode:78978198255abe40e7ecb98-37973895%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '35a309bb58a26a9540e32c074b0af501a01419d2' => 
    array (
      0 => '/var/www/bithub/template/home.html',
      1 => 1438815866,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '78978198255abe40e7ecb98-37973895',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.21-dev',
  'unifunc' => 'content_55abe40e80dc25_10640679',
  'variables' => 
  array (
    'github_redirect' => 0,
    'featured' => 0,
    'g' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_55abe40e80dc25_10640679')) {function content_55abe40e80dc25_10640679($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>

<div class='container'>
	<br/><br/>
  <div class='row'>
  	<div class='twelve columns centered'>
		<h1>BitHub</h1>
		<h3>Colored Coins for Coders</h3>
	</div>
  </div>
  <div class='row homeinput'>
  	<div class="four columns centered">
		<i class="fa fa-bitcoin fa-5x"></i><br/>
		Mint a coin for your repo.
	</div>

	<div class="four columns centered">
		<i class="fa fa-flash fa-5x"></i><br/>
		Automatically reward contributors.
	</div>
  	<div class="four columns centered">
		<i class="fa fa-smile-o fa-5x"></i><br/>
		Bring color to coding projects
	</div>
	<div class='twelve columns centered'>

	<br/><br/>
		<a class="button button-primary" href="https://github.com/login/oauth/authorize?client_id=<?php echo @constant('GITHUB_CLIENTID');?>
&redirect_uri=<?php echo rawurlencode($_smarty_tpl->tpl_vars['github_redirect']->value);?>
&scope=user,repo,read:org">
			<i style='color: #FFF;' class="fa fa-github fa-3x"></i> <span>Login with GitHub</span>
		</a>
	</div>

	<br/><br/>
    </form>
  </div>
</div>
<hr class='main'>
<div class='container featured'>
  <div class='row'>
  	<div class='twelve columns centered'>
  		<h2>Featured Projects</h2>
	</div>
  </div>
  <div class='row'>
  	<?php  $_smarty_tpl->tpl_vars["g"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["g"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['featured']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["g"]->key => $_smarty_tpl->tpl_vars["g"]->value) {
$_smarty_tpl->tpl_vars["g"]->_loop = true;
?>
  	<div class='three columns centered'>
		<a href='/mint/<?php echo $_smarty_tpl->tpl_vars['g']->value['name'];?>
' target="_blank">
		<img src='<?php echo $_smarty_tpl->tpl_vars['g']->value['icon_url'];?>
' class='u-max-full-width'><br/>
		<?php echo $_smarty_tpl->tpl_vars['g']->value['name'];?>


		</a>
	</div>
	<?php } ?>
  </div>
 </div>
 <hr class='main'/>
 <div class='container faq'>

   <div class='row'>
  	<div class='twelve columns centered'>
  		<h2>About Bithub</h2>
	</div>
  </div>
 
  <hr class='sub'/>
  <div class='row'>
  	<div class='four columns'><strong>What is Bithub?</strong></div>
	<div class='eight columns'>Bithub is a fun way to reward contributions to a repository.</div>
  </div>
  <hr class='sub'/>
  <div class='row'>
  	<div class='four columns'><strong>What is a Colored Coin?</strong></div>
		<div class='eight columns'>We use a cool protocol called <a target = '_blank' href='http://blog.coloredcoins.org/blog/2015/5/6/the-great-use-cases-index-of-coloredcoins'>Colored Coins</a>.  This protocol earmarks BTC with special attributes in the header, so your project coin is written to the blockchain.	
	</div>		
  </div>
  <hr class='sub'/>
  <div class='row'>
  	<div class='four columns'><strong>How does it work?</strong></div>
	<div class='eight columns'>Login using your Github account and fund your account with any amount of BTC you like.  We convert this into a colored coin representing your project.
	<br/><br/>
	For 30 days, we review your project's commit history.  Every contributor earns a proportional share of your project coin, which they can claim at Bithub.</div>
  </div>
<hr class='sub'/>
  <div class='row'>
  	<div class='four columns'><strong>How do you calculate project contributions?</strong></div>
	<div class='eight columns'>We're open to suggestions.  For the time being, we're keeping it simple.  Every day, we divide 1/30 of your project coin evenly among anybody who commits.  There's many more interesting ways to calculate this, enough that it's been <a href='#'>the subject of academic research</a>.
	</div>
  </div>
<hr class='sub'/>
  <div class='row'>
  	<div class='four columns'><strong>Is this for real?</strong></div>
	<div class='eight columns'>We eat our own dog food.  Bithub is the <a href='#'>first project</a> listed on our site.  Join us, improve our source code, and you'll automatically receive our Bithub project coin!
	</div>
  </div>
</div>
<hr class='main'/>
<div class='container apis'>
   <div class='row centered'>
	  <h2>Hacked together from these great APIs</h2>
   </div>
   <div class='row centered'>
	<div class='four columns centered'>
		<a target='_blank' href='http://coinprism.com/'><img src='https://pbs.twimg.com/profile_images/437231293533655040/ul_G4smn_400x400.png' class='u-max-full-width'></a>
	</div>
	<div class='four columns centered'>
		<a target='_blank' href='http://btc.blockr.io/'><img src='https://pbs.twimg.com/profile_images/454758137107468288/ll8TSd0Y.png' class='u-max-full-width'></a>
	</div>
	<div class='four columns centered'>
		<a target='_blank' href='http://blockcypher.com/'><img src='https://pbs.twimg.com/profile_images/548271308794900480/ogHR7ALk_400x400.png' class='u-max-full-width'></a>
	</div>

</div>

</div>

<?php echo $_smarty_tpl->getSubTemplate ("footer.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>

<?php }} ?>
