# pictureManager for Laravel 5.*
a picture manage tool


## Installation

- Run `composer require ty666/picture-manager`


- Add `Ty666\PictureManager\PictureManagerServiceProvider::class,` to  **providers** in *config/app.php*
- Add `'PictureManager' => Ty666\PictureManager\Facades\PictureManager::class,` to **aliases** in *config/app.php*
- Run `php artisan vendor:publish`



## Usage

Just add this code to your blade template file:

``` 
{!! Toastr::render() !!}
```

Use these methods in controllers:

- `PictureManager::` 
- `Toastr::error($message, $title = null, $options = [])` 
- `Toastr::info($message, $title = null, $options = [])`
- `Toastr::success($message, $title = null, $options = [])`
- `Toastr::clear() `



## Config

set the toaster options in **config/toastr.php** , available options => [toastr.js demo](http://codeseven.github.io/toastr/demo.html)
