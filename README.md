# pictureManager for Laravel 5.*
a picture manage tool


## Installation

- Run `composer require ty666/picture-manager`

- Add `Intervention\Image\ImageServiceProvider::class,` to  **providers** in *config/app.php*
- Add `Ty666\PictureManager\PictureManagerServiceProvider::class,` to  **providers** in *config/app.php*
- Add `'PictureManager' => Ty666\PictureManager\Facades\PictureManager::class,` to **aliases** in *config/app.php*
- Run `php artisan vendor:publish`



## Usage

显示图片
``` 
return PictureManager::init($image_id, $size, $type)->show();
```
pictureManager会自动创建如下路由（可在配置文件中配置）
``` 
Route::get('/pic/{img_id}_{size}_{suffix}', 
    function ($img_id, $size, $suffix){
        return \Ty666\PictureManager\Facades\PictureManager::init($img_id, $size, $suffix)->show();
                }
            )
```
访问 `http://localhost:8080/pic/99cceb7fd0bdf4e17903dc2655c84af4_xs_jpg` 就ok!

## Config

set the pictureConfig options in **config/picture.php** 