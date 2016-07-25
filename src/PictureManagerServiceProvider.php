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
        parent::boot();
        $this->publishes([
            realpath(__DIR__.'/../config/picture.php') => config_path('picture.php'),
        ]);

        $this->pictureRoute();
    }

    /**
     * 图片路由
     */
    protected function pictureRoute(){
        $routeConfig = $this->app['config']->get('picture.route');
        if($routeConfig['path'] != ''){

            $this->app['router']->get($routeConfig['path'].'/{img_id}_{size}_{suffix}',[
                'as' => $routeConfig['name'],
                'middleware' => $routeConfig['middleware'],
                function ($img_id, $size, $suffix){
                    return \Ty666\PictureManager\Facades\PictureManager::init($img_id, $size, $suffix)->show();
                }
            ])->where(['img_id'=>'[a-zA-Z0-9]{32}','size'=>'(xs|l|b)','suffix'=>'(jpg|png|gif)']);
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
