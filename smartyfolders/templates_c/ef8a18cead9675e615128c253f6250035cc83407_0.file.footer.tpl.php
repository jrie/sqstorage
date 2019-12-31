<?php
/* Smarty version 3.1.34-dev-7, created on 2019-12-31 15:11:08
  from 'D:\Development\sqstorage\templates\footer.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.34-dev-7',
  'unifunc' => 'content_5e0b56fc82f763_90690197',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'ef8a18cead9675e615128c253f6250035cc83407' => 
    array (
      0 => 'D:\\Development\\sqstorage\\templates\\footer.tpl',
      1 => 1577797576,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5e0b56fc82f763_90690197 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Development\\sqstorage\\vendor\\smarty\\smarty\\libs\\plugins\\function.mailto.php','function'=>'smarty_function_mailto',),));
?>
<footer class="footer">
    <?php ob_start();
$_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
echo "Kontakt";
$_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);
$_prefixVariable1=ob_get_clean();
echo smarty_function_mailto(array('address'=>'jan@dwrox.net','encode'=>'javascript','subject'=>'sqstorage','extra'=>'class="btn btn-info" tabindex="-1"','text'=>$_prefixVariable1),$_smarty_tpl);?>

    <a class="btn btn-info" tabIndex="-1" target="_blank" href="https://github.com/jrie/sqstorage"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Github<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></a>
</footer>
<?php }
}
