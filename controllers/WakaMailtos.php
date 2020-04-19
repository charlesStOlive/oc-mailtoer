<?php namespace Waka\Mailtoer\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use System\Classes\SettingsManager;

/**
 * Waka Mailtos Back-end Controller
 */
class WakaMailtos extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
        'Waka.Informer.Behaviors.PopupInfo',
        'Waka.Mailtoer.Behaviors.MailtoBehavior',
        'Waka.Utils.Behaviors.DuplicateModel',
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';
    public $duplicateConfig = 'config_duplicate.yaml';

    public function __construct()
    {
        parent::__construct();

        \BackendMenu::setContext('October.System', 'system', 'settings');
        SettingsManager::setContext('Waka.Mailtoer', 'wakamailtos');
    }

}
