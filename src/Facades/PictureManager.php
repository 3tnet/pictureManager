<?php

namespace Ty666\PictureManager\Facades;

use Illuminate\Support\Facades\Facade;


class PictureManager extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'pictureManager';
    }
}