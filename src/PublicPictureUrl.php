<?php

namespace Ty666\PictureManager;


class PublicPictureUrl extends PictureUrl
{
    public function getUrl($pictureId, $size)
    {
        if (!array_key_exists($size, $this->config['sizeList'])) {
            return null;
        }
        return route($this->config['route']['name'], [$pictureId, $size]);
    }

    public function getOriginalPath($pictureId)
    {
        return $this->config['uploadPath'] . substr($pictureId, 0, 2) . DIRECTORY_SEPARATOR . $pictureId;
    }
}