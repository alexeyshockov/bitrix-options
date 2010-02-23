<?php
/**
 * Module settinds.
 */

/*
 * Include some standard language constants.
 */
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php");

/*
 * Unfortunately, Bitrix doesn't have GetList method for options...
 */
$options = $GLOBALS['DB']->Query('SELECT * FROM `b_option` WHERE `MODULE_ID` = "options"');
$optionNames = array();
while ($option = $options->Fetch()) {
    $optionNames[] = $option['NAME'];
}

$tabs = array(
    array(
        "DIV"   => "edit",
        "TAB"   => 'Свойства',
        "ICON"  => "",
        "TITLE" => 'Пользовательские свойства'
    ),
);
$tabControl = new CAdminTabControl("tabControl", $tabs);

if (
    (strlen($_POST['Update'].$_POST['Apply']) > 0)
    &&
    check_bitrix_sessid()
) {
    foreach($optionNames as $key => $optionName) {
        /*
         * TODO Better flag checking.
         */
        if (isset($_POST['options'][$optionName]['delete'])) {
            COption::RemoveOption('options', $optionName);
            
            unset($optionNames[$key]);
        } else {
            COption::SetOptionString("options", $optionName, $_POST['options'][$optionName]['value']);
        }
    }
    $_POST['new_options'] = array_filter($_POST['new_options']);
    if (count($_POST['new_options'])) {
        for ($i = 0; $i < count($_POST['new_options']); $i++) {
            COption::SetOptionString(
                "options", 
                $_POST['new_options'][$i], 
                $_POST['new_option_values'][$i]
            );
            
            $optionNames[] = $_POST['new_options'][$i];
        }
    }
    
    if (strlen($_REQUEST['Update']) && strlen($_REQUEST['back_url_settings'])) {
        LocalRedirect($_REQUEST['back_url_settings']);
    } else {
        LocalRedirect(
            $GLOBALS['APPLICATION']->GetCurPage().
            "?mid=".urlencode($mid).
            "&lang=".urlencode(LANGUAGE_ID).
            "&back_url_settings=".urlencode($_REQUEST["back_url_settings"]).
            "&".$tabControl->ActiveTabParam()
        );
    }
}

$tabControl->Begin();

?>
<form
    method="post"
    action="<?=$APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?=LANGUAGE_ID?>">
<?php

$tabControl->BeginNextTab();

foreach($optionNames as $optionName) {
    $optionValue = COption::GetOptionString("options", $optionName);

?>
        <tr>
            <td width="50%"><?=$optionName?></td>
            <td width="50%">
                <input
                    type="text"
                    size="30"
                    value="<?=$optionValue?>"
                    name="options[<?=$optionName?>][value]" />
                <input type="checkbox" name="options[<?=$optionName?>][delete]" />&nbsp;&mdash; удалить?
            </td>
        </tr>
<?php } ?>
        <tr>
            <td width="50%">
                <input
                    type="text"
                    size="30"
                    value=""
                    name="new_options[]" />
            </td>
            <td width="50%">
                <input
                    type="text"
                    size="30"
                    value=""
                    name="new_option_values[]" />
            </td>
        </tr>
<?php $tabControl->Buttons() ?>
    <input
        type="submit"
        name="Update"
        value="<?=GetMessage("MAIN_SAVE")?>"
        title="<?=GetMessage("MAIN_OPT_SAVE_TITLE")?>" />
    <input
        type="submit"
        name="Apply"
        value="<?=GetMessage("MAIN_OPT_APPLY")?>"
        title="<?=GetMessage("MAIN_OPT_APPLY_TITLE")?>" />
    <?php if (strlen($_REQUEST["back_url_settings"])) { ?>
        <input
            type="button"
            name="Cancel"
            value="<?=GetMessage("MAIN_OPT_CANCEL")?>"
            title="<?=GetMessage("MAIN_OPT_CANCEL_TITLE")?>"
            onclick="window.location='<?=htmlspecialchars(CUtil::addslashes($_REQUEST["back_url_settings"]))?>'" />
        <input
            type="hidden"
            name="back_url_settings"
            value="<?=htmlspecialchars($_REQUEST["back_url_settings"])?>" />
    <?php } ?>
    <?=bitrix_sessid_post();?>
<?php $tabControl->End() ?>
</form>

<?=BeginNote()?>
Свойства, заданные на этой странице вы можете использовать в коде следующим образом (имя модуля&nbsp;&mdash; options&nbsp;&mdash; остаётся неизменным):
<br />
<br />
<?=str_replace('&lt;?php&nbsp;', '', highlight_string('<?php $optionValue = COption::GetOptionString(\'options\', \'your.option.name\');', true))?>
<?=EndNote()?>
