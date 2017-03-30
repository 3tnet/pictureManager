<?php
namespace Ty666\PictureManager\Traits;


use PictureManager;

Trait Picture
{
    public function getPicure($picture, $allowSizeList = null, $defaulePic = '')
    {

        $sizeList = array_keys(PictureManager::getSizeList());
        if (!is_null($allowSizeList)) {
            if (is_string($allowSizeList)) $allowSizeList = [$allowSizeList];

            $sizeList = array_intersect($allowSizeList, $sizeList);
        }
        if(empty($picture)) {
            //获取默认图片 (如果需要)
            return array_combine($sizeList, array_fill(0, count($sizeList), $defaulePic));
        }
        list($pictureId, $suffix) = explode('.', $picture, 2);
        $data = [];
        foreach ($sizeList as $size) {
            $data[$size] = route(PictureManager::getImageRouteName(), [
                'img_id' => $pictureId,
                'size' => $size,
                'suffix' => $suffix
            ]);
        }
        return $data;
    }
}