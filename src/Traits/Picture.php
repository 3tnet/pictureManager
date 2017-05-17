<?php
namespace Ty666\PictureManager\Traits;


Trait Picture
{
    public function getPicure($pictureId, $allowSizeList = null, $defaultPic = '')
    {
        $sizeListKeys = array_keys(config('picture.sizeList'));
        if (is_null($allowSizeList)) {
            $allowSizeList = $sizeListKeys;
        }elseif(is_string($allowSizeList)){
            $allowSizeList = [$allowSizeList];
        }

        $allowSizeList = array_values(array_intersect($sizeListKeys, $allowSizeList));
        $urls = [];
        if (empty($pictureId)) {
            //获取默认图片 (如果需要)
            foreach ($allowSizeList as $size) {
                $urls[$size] = $defaultPic;
            }
        } else {
            $pictureUrl = app('pictureManager.PictureUrlManager');
            foreach ($allowSizeList as $size) {
                $urls[$size] = $pictureUrl->getUrl($pictureId, $size);
            }
        }
        if(count($urls) == 1){
            return array_pop($urls);
        }
        return $urls;
    }
}

