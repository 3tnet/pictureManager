<?php

namespace Ty666\PictureManager;

use Storage;

class QiniuPictureUrl extends PictureUrl
{

    public function getUrl($pictureId, $style)
    {
        if (!array_key_exists($style, $this->config['sizeList'])) {
            return null;
        }
        $baseUrl = Storage::disk('qiniu')->url($pictureId);
        if(isset($this->config['style_delimiter'])){
            return "$baseUrl{$this->config['style_delimiter']}$style";
        }
        $style = explode(',', $this->config['sizeList'][$style], 3);
        $width = $style[0] ?: '';
        $height = $style[1] ?: '';
        if (count($style) >= 3) {
            $quality = $style[2];
        } else {
            $quality = $this->config['quality'];
        }

        if ($width == '' && $height == '') {
            if ($quality == 100) {
                return $baseUrl;
            }
            return "{$baseUrl}?imageMogr2/auto-orient/blur/1x0/quality/{$quality}|imageslim";
        }
        return "{$baseUrl}?imageMogr2/auto-orient/thumbnail/{$width}x{$height}/blur/1x0/quality/{$quality}|imageslim";

    }

    public function getOriginalPath($pictureId)
    {
        return $pictureId;
    }
}