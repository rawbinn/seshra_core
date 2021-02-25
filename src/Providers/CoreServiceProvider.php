<?php

namespace Seshra\Core\Providers;

use Seshra\Core\Core;
use Illuminate\Routing\Router;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Seshra\Core\Http\Middleware\Locale;
use Seshra\Core\Facades\Core as CoreFacade;
use Seshra\Core\Http\Middleware\Theme;
use Illuminate\View\Compilers\BladeCompiler;

/**
 * Class CoreServiceProvider
 * @package Seshra\Core\Providers
 * @version 1.0.0
 * @author Rawbinn Shrestha <contact@rawbinn.com>
 * @organization RAWBINN.COM.
 */
class CoreServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(Router $router)
    {
        include __DIR__ . '/../Http/helpers.php';
        $router->aliasMiddleware('locale', Locale::class);
        $router->aliasMiddleware('theme', Theme::class);
        // $router->aliasMiddleware('currency', Currency::class);
        $this->registerPlugins();
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerFacades();
        $this->registerConfig();
        $this->registerBladeExtensions();
    }

    /**
     * Register AdminMiddleware as a singleton.
     *
     * @return void
     */
    protected function registerFacades()
    {
        $loader = AliasLoader::getInstance();
        $loader->alias('core', CoreFacade::class);

        $this->app->singleton('core', function () {
            return app()->make(Core::class);
        });
    }

    /**
     * Register package config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $plugin_path = base_path() .'/app/Plugins';
        if (is_dir($plugin_path)) {
            $plugins = array_diff(scandir($plugin_path), array_merge(['.', '..'], []));
            natsort($plugins);
            if (!empty($plugins)) {
                foreach ($plugins as $plugin) {
                    $menu_path = $plugin_path.'/'.$plugin.'/Config/menu.php';
                    if(file_exists($menu_path)) {
                        $this->mergeConfigFrom(
                            $menu_path, 'menu.admin'
                        );
                    }
                }
            }
        }
        
    }

    protected function registerPlugins()
    {
        $plugin_path = base_path() .'/app/Plugins';
        $plugins = scan_folder($plugin_path);
        if (!empty($plugins)) {
            foreach ($plugins as $plugin) {
                $route_path = $plugin_path.'/'.$plugin.'/Http/routes.php';
                $view_dir = $plugin_path.'/'.$plugin.'/Resources/views';
                if(file_exists($route_path)) {
                    $this->loadRoutesFrom($route_path);
                }
                if(file_exists($view_dir)) {
                    $this->loadViewsFrom($view_dir, strtolower($plugin));
                }
                // $content = get_file_data($plugin_path . DIRECTORY_SEPARATOR . $plugin . '/plugin.json');
                // $this->app->register($content['provider']);
            }
        }
    }

    protected function registerBladeExtensions()
    {
        $this->app->afterResolving('blade.compiler', function (BladeCompiler $bladeCompiler) {

            $bladeCompiler->directive('filter', function ($eventName, $params = null) {
                return "<?php echo filter($eventName, $params) ?>";
            });
        });
    }
}
