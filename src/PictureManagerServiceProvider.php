<?php

namespace Ty666\PictureManager;

use Illuminate\Support\ServiceProvider;
use Intervention\Image\ImageManager;
use Storage;

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
        return ['pictureManager', 'pictureManager.PictureUrlManager', 'pictureManager.pictureIdGenerator', 'pictureManager.uploader'];
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            realpath(__DIR__ . '/../config/picture.php') => config_path('picture.php'),
        ]);

        //$this->pictureRoute();
    }

    /**
     * 图片路由
     */
    protected function pictureRoute()
    {
        /*$config = $this->app['config']->get('picture');
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
        }*/
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
            realpath(__DIR__ . '/../config/picture.php'), 'picture'
        );
        $this->app->bind('pictureManager', function ($app) {
            $config = $app['config']['picture'];
            return new PictureManager($config,
                $this->app->make(ImageManager::class),
                $this->app['pictureManager.PictureUrlManager'],
                $this->app['pictureManager.pictureIdGenerator']);
        });

        $this->app->singleton('pictureManager.PictureUrlManager', function ($app) {
            return new PictureUrlManager($app);
        });
        $this->app->singleton('pictureManager.pictureIdGenerator', function ($app) {
            return new PictureIdGenerator();
        });
        $this->app->singleton('pictureManager.uploader', function ($app) {
            return new PictureUploader(
                Storage::disk($app['config']->get('picture.default_disk')),
                $this->app['pictureManager.PictureUrlManager'],
                $this->app['pictureManager.pictureIdGenerator']);
        });
    }
}
