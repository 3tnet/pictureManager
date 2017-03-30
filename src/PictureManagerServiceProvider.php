<?php

namespace Ty666\PictureManager;

use Illuminate\Support\ServiceProvider;

class PictureManagerServiceProvider extends ServiceProvider
{
    /**
     * 服务提供者加是否延迟加载.
     *
     * @var bool
     */
    protected $defer = true;
    /**
     * 获取由提供者提供的服务.
     *
     * @return array
     */
    public function provides()
    {
        return ['pictureManager'];
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

        $this->publishes([
            realpath(__DIR__.'/../config/picture.php') => config_path('picture.php'),
        ]);

        //$this->pictureRoute();
    }

    /**
     * 图片路由
     */
    protected function pictureRoute(){
        $config = $this->app['config']->get('picture');
        if($config['route']['path'] != ''){
            //图片尺寸正则
            $sizePattern = '('.implode('|', array_keys($config['sizeList'])).')';
            //图片后缀正则
            $suffixPattern = '('.implode('|',$config['allowTypeList']).')';
            $this->app['router']->get($config['route']['path'].'/{img_id}_{size}_{suffix}',[
                'as' => $config['route']['name'],
                'middleware' => $config['route']['middleware'],
                function ($img_id, $size, $suffix){
                    return \Ty666\PictureManager\Facades\PictureManager::init($img_id, $size, $suffix)->show();
                }
            ])->where(['img_id'=>'[a-zA-Z0-9]{32}','size' => $sizePattern, 'suffix' => $suffixPattern]);
        }
    }
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //合并配置文件
        $this->mergeConfigFrom(
            realpath(__DIR__.'/../config/picture.php'), 'picture'
        );

        $this->app->singleton('pictureManager', function ($app) {
            $config = $app['config']->get('picture');
            return new PictureManager($config);
        });

    }
}
