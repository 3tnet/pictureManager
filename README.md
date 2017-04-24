# pictureManager for Laravel 5.*
a picture manage tool


## Installation

- Run `composer require ty666/picture-manager`

- Add `Ty666\PictureManager\PictureManagerServiceProvider::class,` to  **providers** in *config/app.php*
- Add `'PictureManager' => Ty666\PictureManager\Facades\PictureManager::class,` to **aliases** in *config/app.php*
- Run `php artisan vendor:publish --provider="Ty666\PictureManager\PictureManagerServiceProvider"`


## Config

set the pictureConfig options in **config/picture.php** 


## Usage

显示图片

``` 
Route::get('pic/{pictureId}_{style?}', function ($pictureId, $style = null) {
    return PictureManager::init($pictureId, $style)->show();
});
```


访问 `http://localhost:8080/pic/99cceb7fd0bdf4e17903dc2655c84af4` 就ok!

