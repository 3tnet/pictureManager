<?php

namespace Ty666\PictureManager;

use Illuminate\Support\ServiceProvider;

class PictureManagerServiceProvider extends ServiceProvider
{
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


        $this->app->singleton('pictureManager', function ($app) {
            $config = $app['config']->get('picture');
            $pictureManager = new PictureManager();
            $pictureManager->setUploadDir($config['uploadPath'])
                ->setSizeList($config['sizeList'])
                ->setAllowTypeList($config['allowTypeList'])
                ->setWatermark($config['watermark']);
            if(isset($config['needWatermark'])){
                $pictureManager->needWaterMark = $config['needWatermark'];
            }
            return $pictureManager;
        });
        //合并配置文件
        $this->mergeConfigFrom(
            realpath(__DIR__.'/../config/picture.php'), 'picture'
        );
    }
}
