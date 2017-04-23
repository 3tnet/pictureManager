<?php

namespace Ty666\PictureManager;


interface PictureUrlInterface
{
    public function getUrl($pictureId, $size);

    public function getOriginalPath($pictureId);
}