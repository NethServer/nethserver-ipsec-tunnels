<?php

/* @var $view \Nethgui\Renderer\Xhtml */

$nameId = $view->getUniqueId('name');
$leftId = $view->getUniqueId('leftid');
$rightId = $view->getUniqueId('rightid');

$view->includeJavascript("
    jQuery(function ($) {
        var updateWatermarks = function() {
            $('#${leftId}').attr('placeholder', '@' + $('#${nameId}').prop('value') + '.local');
            $('#${rightId}').attr('placeholder', '@' + $('#${nameId}').prop('value') + '.remote');
        };

        $('#${nameId}').on('change keyup', updateWatermarks);
        $('#${rightId}').on('nethguiupdateview', updateWatermarks);
    });
    ");


if ($view->getModule()->getIdentifier() == 'update') {
    $headerText = 'update_header_label';
} else {
    $headerText = 'create_header_label';
}

echo $view->header()->setAttribute('template',$T($headerText));

if ($view->getModule()->getIdentifier() == 'update') {
    $name = $view->textInput('name', $view::STATE_READONLY);
} else {
    $name = $view->textInput('name');
}

$global = $view->fieldset()->setAttribute('template', $T('general_label'))
    ->insert($name)
    ->insert($view->checkbox('status', 'enabled')->setAttribute('uncheckedValue', 'disabled'))
;

$auth = $view->fieldset()->setAttribute('template', $T('auth_label'))
    ->insert($view->textInput('psk'))
;

$left = $view->panel()
    ->insert($view->selector('left', $view::SELECTOR_DROPDOWN))
    ->insert($view->textInput('leftsubnets'))
    ->insert($view->textInput('leftid'));

$right = $view->panel()
    ->insert($view->textInput('right'))
    ->insert($view->textInput('rightsubnets'))
    ->insert($view->textInput('rightid'));

$connection = $view->fieldset()->setAttribute('template', $T('connection_label'))
    ->insert($view->columns()
        ->insert($left)
        ->insert($right));


$p1 = $view->fieldset()->setAttribute('template', $T('phase1_label'))
    ->insert($view->fieldsetSwitch('ike', 'auto'))
    ->insert($view->fieldsetSwitch('ike', 'custom',$view::FIELDSETSWITCH_EXPANDABLE)
        ->insert($view->selector('ikecipher', $view::SELECTOR_DROPDOWN))
        ->insert($view->selector('ikehash', $view::SELECTOR_DROPDOWN))
        ->insert($view->selector('ikepfsgroup', $view::SELECTOR_DROPDOWN))
        ->insert($view->textInput('ikelifetime'))
    );

$p2 = $view->fieldset()->setAttribute('template', $T('phase2_label'))
    ->insert($view->fieldsetSwitch('esp', 'auto'))
    ->insert($view->fieldsetSwitch('esp', 'custom',$view::FIELDSETSWITCH_EXPANDABLE)
        ->insert($view->selector('espcipher', $view::SELECTOR_DROPDOWN))
        ->insert($view->selector('esphash', $view::SELECTOR_DROPDOWN))
        ->insert($view->selector('esppfsgroup', $view::SELECTOR_DROPDOWN))
        ->insert($view->textInput('salifetime'))
    );

$advanced = $view->fieldset('', $view::FIELDSET_EXPANDABLE)->setAttribute('template', $T('Advanced_label'))
    ->insert($view->checkbox('dpdaction', 'restart')->setAttribute('uncheckedValue', 'hold'))
    ->insert($view->checkbox('pfs', 'yes')->setAttribute('uncheckedValue', 'no'))
    ->insert($view->checkbox('compress', 'yes')->setAttribute('uncheckedValue', 'no'))
    ->insert($p1)
    ->insert($p2);



echo $global;
echo $auth;
echo $connection;
echo $advanced;


echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_CANCEL | $view::BUTTON_HELP);

