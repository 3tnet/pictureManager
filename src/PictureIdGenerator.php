<?php

namespace Ty666\PictureManager;

use SplFileInfo;

class PictureIdGenerator
{
    public function generate($picture = null)
    {
        if ($picture instanceof SplFileInfo) {
            return md5_file($picture->getRealPath());
        } elseif (is_string($picture)) {
            return md5($picture);
        } else {
            return md5(uniqid('t', true));
        }
    }
}