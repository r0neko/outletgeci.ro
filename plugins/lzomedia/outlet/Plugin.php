<?php namespace LzoMedia\Outlet;

use Backend;
use LzoMedia\Agencies\Models\Agency;
use RainLab\User\Models\User;
use System\Classes\PluginBase;

/**
 * Agencies Plugin Information File
 */
class Plugin extends PluginBase
{

    /**
     * @var array
     */
    public $require = ['RainLab.User'];

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name' => 'Outlet',
            'description' => 'Provides an way to manage the  recruitment agencies',
            'author' => 'LzoMedia',
            'icon' => 'icon-block'
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
     * @return mixed
     */
    public function boot()
    {
        User::extend(function ($model){

            $model->hasOne["agency"] = Agency::class;

        });
    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return [
        ];

    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return []; // Remove this line to activate

    }

    /**
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation()
    {

        return [];
    }
}
