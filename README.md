# pictureManager for Laravel 5.*
a picture manage tool

[完整中文文档](https://github.com/3tnet/pictureManager/wiki)

## Installation

- Run `composer require ty666/picture-manager`

- Add `Ty666\PictureManager\PictureManagerServiceProvider::class,` to  **providers** in *config/app.php*
- Add `'PictureManager' => Ty666\PictureManager\Facades\PictureManager::class,` to **aliases** in *config/app.php*
- Run `php artisan vendor:publish --provider="Ty666\PictureManager\PictureManagerServiceProvider"`


## Config

Set the pictureConfig options in **config/picture.php** 


## Usage

Show picture

``` 
Route::get('pic/{pictureId}_{style?}', function ($pictureId, $style = null) {
    return PictureManager::init($pictureId, $style)->show();
});
```


Visit  `http://localhost:8080/pic/99cceb7fd0bdf4e17903dc2655c84af4` ok!

