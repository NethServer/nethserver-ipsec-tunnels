<?php
/* @var $view \Nethgui\Renderer\Xhtml */
$header = $view->getModule()->getIdentifier() === 'enable' ? $T('IpsecTunnels_enable_title') : $T('IpsecTunnels_disable_title');
echo $view->header('service')->setAttribute('template', $header);

$view->requireFlag($view::INSET_DIALOG);

$message = $view->getModule()->getIdentifier() === 'enable' ? $T('confirm_enable_label') : $T('confirm_disable_label');
echo $view->textLabel('service')->setAttribute('template', $message);

echo $view->buttonList()
    ->insert($view->button('Confirm', $view::BUTTON_SUBMIT))
    ->insert($view->button('Cancel', $view::BUTTON_CANCEL))
;

