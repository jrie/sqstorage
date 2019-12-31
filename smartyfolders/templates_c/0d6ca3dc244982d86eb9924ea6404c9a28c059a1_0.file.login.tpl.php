<?php
/* Smarty version 3.1.34-dev-7, created on 2019-12-31 15:11:08
  from 'D:\Development\sqstorage\templates\login.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.34-dev-7',
  'unifunc' => 'content_5e0b56fc415a62_97064575',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '0d6ca3dc244982d86eb9924ea6404c9a28c059a1' => 
    array (
      0 => 'D:\\Development\\sqstorage\\templates\\login.tpl',
      1 => 1577801399,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:head.tpl' => 1,
    'file:footer.tpl' => 1,
    'file:bodyend.tpl' => 1,
  ),
),false)) {
function content_5e0b56fc415a62_97064575 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:head.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('title'=>'foo'), 0, false);
?>

        <div class="content">
            <div class="login-box">
                <div class="card">
                    <div class="card-body login-card-body">

                    <?php if (isset($_smarty_tpl->tpl_vars['error']->value)) {?>
                    <div class="alert alert-danger"><?php echo $_smarty_tpl->tpl_vars['error']->value;?>
></div>
                    <?php }?>

                    <p class="login-box-msg">
                        <?php if ($_smarty_tpl->tpl_vars['createFirstAdmin']->value) {?>
                            <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Neue Admin-Zugangsdaten eingeben<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
                        <?php } else { ?>
                            <?php if ($_smarty_tpl->tpl_vars['showActivation']->value) {?>
                                <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Neue Zugangsdaten eingeben<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
                            <?php } else { ?>
                                <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Zugangsdaten eingeben<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></p>
                            <?php }?>
                        <?php }?>
                    <form action="login.php<?php if ($_smarty_tpl->tpl_vars['showActivation']->value) {?>?activate=<?php echo $_smarty_tpl->tpl_vars['activate']->value;
}
if ($_smarty_tpl->tpl_vars['showRecover']->value) {?>?recover<?php }?>" method="post">
                        <div class="input-group mb-3">
                            <input type="text" id="username" name="username" class="form-control" placeholder="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Benutzername<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>" value="<?php if (($_smarty_tpl->tpl_vars['showActivation']->value || $_smarty_tpl->tpl_vars['showRecover']->value)) {
if (isset($_smarty_tpl->tpl_vars['POST']->value['username'])) {
echo $_smarty_tpl->tpl_vars['POST']->value['username'];
}
} else {
if (isset($_smarty_tpl->tpl_vars['user']->value['username'])) {
echo $_smarty_tpl->tpl_vars['user']->value['username'];
}
}?>">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                <span class="fas fa-user"></span>
                                </div>
                            </div>
                        </div>
                        <?php if (($_smarty_tpl->tpl_vars['showRecover']->value || $_smarty_tpl->tpl_vars['createFirstAdmin']->value)) {?>
                        <div class="input-group mb-3">
                            <input type="email" id="mailaddress" name="mailaddress" class="form-control" placeholder="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>E-Mail<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>" value="<?php if (isset($_smarty_tpl->tpl_vars['POST']->value['mailaddress'])) {
echo $_smarty_tpl->tpl_vars['POST']->value['mailaddress'];
}?>">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                                </div>
                            </div>
                        </div>
                        <?php }?>
                        <?php if ((!$_smarty_tpl->tpl_vars['showRecover']->value)) {?>


                            <div class="input-group mb-3">
                                <input type="password" id="password" name="password" class="form-control" placeholder="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Passwort<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>" value="<?php if ($_smarty_tpl->tpl_vars['showActivation']->value) {
if (isset($_smarty_tpl->tpl_vars['POST']->value['password'])) {
echo $_smarty_tpl->tpl_vars['POST']->value['password'];
}
}?>">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                    <span class="fas fa-lock"></span>
                                    </div>
                                </div>
                            </div>
                            <?php if ($_smarty_tpl->tpl_vars['showActivation']->value) {?>
                            <div class="input-group mb-3">
                                <input type="password" id="password_repeat" name="password_repeat" class="form-control" placeholder="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Passwort wiederholen<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>" value="<?php if ($_smarty_tpl->tpl_vars['showActivation']->value) {
if (isset($_smarty_tpl->tpl_vars['POST']->value['password_repeat'])) {
echo $_smarty_tpl->tpl_vars['POST']->value['password_repeat'];
}
}?>">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                    <span class="fas fa-lock"></span>
                                    </div>
                                </div>
                            </div>
                            <?php }?>
                            <div class="row">
                                <div class="col-7">
                                    <div class="form-group form-check">
                                    <input type="checkbox" id="remember" class="form-check-input">
                                    <label for="remember" class="form-check-label">
                                        <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Angemeldet bleiben?<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
                                    </label>
                                    </div>
                                </div>
                                <div class="col-5">
                                    <button type="submit" class="btn btn-primary btn-block btn-flat"><?php if ($_smarty_tpl->tpl_vars['showActivation']->value) {
$_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Speichern<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);
} else {
$_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Anmelden<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);
}?></button>
                                </div>
                            </div>


                        <?php } else { ?>
                            <div class="row">
                                <div class="col-7">
                                </div>
                                <div class="col-5">
                                    <button type="submit" class="btn btn-primary btn-block btn-flat"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Anfordern<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></button>
                                </div>
                            </div>
                        <?php }?>


                    </form>
                    <?php if (!$_smarty_tpl->tpl_vars['showActivation']->value) {?>
                    <?php if (!$_smarty_tpl->tpl_vars['showRecover']->value) {?>
                    <p class="mb-1">
                        <a href="login.php?recover"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Zugangsdaten vergessen<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></a>
                    </p>
                    <?php }?>
                    <?php }?>
                    </div>
                </div>
            </div>
        </div>

<?php $_smarty_tpl->_subTemplateRender("file:footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

<?php $_smarty_tpl->_subTemplateRender("file:bodyend.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
