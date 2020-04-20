<?php namespace Waka\Mailtoer;

use Backend;
use System\Classes\PluginBase;

/**
 * Mailtoer Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name' => 'Mailtoer',
            'description' => 'No description provided yet...',
            'author' => 'Waka',
            'icon' => 'icon-leaf',
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {
        \Event::listen('backend.down.update', function ($controller) {
            if (get_class($controller) == 'Waka\Mailtoer\Controllers\WakaMailto') {
                return;
            }

            if (in_array('Waka.Mailtoer.Behaviors.MailtoBehavior', $controller->implement)) {
                $data = [
                    'model' => $modelClass = str_replace('\\', '\\\\', get_class($controller->formGetModel())),
                    'modelId' => $controller->formGetModel()->id,
                ];
                return \View::make('waka.mailtoer::publishMailto')->withData($data);;
            }
        });
        \Event::listen('popup.actions.line1', function ($controller, $model, $id) {
            if (get_class($controller) == 'Waka\Mailtoer\Controllers\WakaMailto') {
                return;
            }

            if (in_array('Waka.Mailtoer.Behaviors.MailtoBehavior', $controller->implement)) {
                //trace_log("Laligne 1 est ici");
                $data = [
                    'model' => str_replace('\\', '\\\\', $model),
                    'modelId' => $id,
                ];
                return \View::make('waka.mailtoer::publishMailtoContent')->withData($data);;
            }
        });

    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return []; // Remove this line to activate

        return [
            'Waka\Mailtoer\Components\MyComponent' => 'myComponent',
        ];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return [
            'waka.mailtoer.admin.super' => [
                'tab' => 'Waka',
                'label' => 'Administrateur de Mailtoer',
            ],
            'waka.mailtoer.admin' => [
                'tab' => 'Waka',
                'label' => 'Administrateur de Mailtoer',
            ],
            'waka.mailtoer.user' => [
                'tab' => 'Waka',
                'label' => 'Utilisateur de Mailtoer',
            ],
        ];
    }

    /**
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerSettings()
    {
        return [
            'wakamailtos' => [
                'label' => \Lang::get('waka.mailtoer::lang.menu.wakamailtos'),
                'description' => \Lang::get('waka.mailtoer::lang.menu.wakamailtos_description'),
                'category' => \Lang::get('waka.mailtoer::lang.menu.settings_category'),
                'icon' => 'icon-file-pdf-o',
                'url' => \Backend::url('waka/mailtoer/wakamailtos'),
                'permissions' => ['waka.mailtoer.admin'],
                'order' => 1,
            ],
        ];
    }
}
