<?php
/**
 * Module descriptor.
 */

if (class_exists("options")) {
    return;
}

/**
 * Module descriptor for Bitrix.
 *
 * @author Alexey Shockov <alexey@shockov.com>
 */
class options extends CModule
{
    public $MODULE_ID           = 'options';
    public $MODULE_VERSION      = '${bitrix.moduleVersion}';
    public $MODULE_VERSION_DATE = '${bitrix.moduleVersionDate}';
    public $MODULE_NAME         = 'Произвольные свойства';
    public $MODULE_DESCRIPTION  = 'Произвольные свойства для собстынных нужд: создание, редактирование, удаление.';
    /**
     * Registration.
     */
    public function DoInstall()
    {
        RegisterModule($this->MODULE_ID);
    }
    /**
     * Unregistration.
     */
    public function DoUninstall()
    {
        UnRegisterModule($this->MODULE_ID);
    }
}
