<?php

namespace Ty666\PictureManager;

use Intervention\Image\ImageManager;
use Ty666\PictureManager\Exception\PictureNotFountException;

class PictureManager
{

    protected $config = [];
    protected $isInit = false;
    protected $pictureId;
    protected $style;
    protected $quality;
    /**
     * @var ImageManager
     */
    protected $imageManager;
    protected $originalPath;
    protected $thumbnailPath;
    protected $size = [];
    protected $pictureUrlManager;
    protected $pictureIdGenerator;

    public function __construct($config, $imageManager,
                                PictureUrlManager $pictureUrlManager,
                                PictureIdGenerator $pictureIdGenerator)
    {

        $this->config = $config;
        $this->imageManager = $imageManager;
        $this->pictureUrlManager = $pictureUrlManager;
        $this->pictureIdGenerator = $pictureIdGenerator;
    }

    public function init($pictureId, $style)
    {
        $this->pictureId = $pictureId;
        $this->originalPath = config('filesystems.disks.public.root') . DIRECTORY_SEPARATOR . $this->pictureUrlManager->getOriginalPath($pictureId);
        if (!is_null($style) && !array_key_exists($style, $this->config['sizeList'])) {
            throw new PictureNotFountException("图片样式出错,\"$style\"不存在！");
        }

        if (!is_null($style)) {
            //获取具体尺寸
            $style = explode(',', $this->config['sizeList'][$style], 3);
            $this->size['width'] = $style[0] ?: null;
            $this->size['height'] = $style[1] ?: null;
            if (count($style) >= 3) {
                $this->quality = $style[2];
            } else {
                $this->quality = $this->config['quality'];
            }
            $this->thumbnailPath = $this->originalPath . '_' . $style[0] . '_' . $style[1] . '_' . $this->quality;
        } else {
            $this->thumbnailPath = $this->originalPath;
        }
        $this->isInit = true;
        return $this;
    }


    /**
     * 显示图片
     * @param string $pictureId
     * @param string $size
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function show()
    {
        $fileName = "{$this->pictureId}_{$this->style}";
        //判断客户端是否有缓存
        if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] === $fileName) {
            //有缓存
            $response = response('', 304);
        } else {
            //无缓存
            if (!file_exists($this->thumbnailPath)) {
                $this->createPicture();
            }
            $response = $this->imageManager->make($this->thumbnailPath)->response();
        }
        $response->header('Content-Disposition', 'inline; filename=' . $fileName);
        $response->header('Etag', $fileName);
        $response->header('Cache-Control', 'public, max-age=31536000');
        return $response;
    }

    /**
     * 生成图片
     */
    protected function createPicture()
    {
        if (!file_exists($this->originalPath)) {
            throw new PictureNotFountException();
        }
        if (!empty($this->size)) {
            $imageObj = $this->imageManager->make($this->originalPath);

            if (!is_null($this->size['width']) && !is_null($this->size['height'])) {
                $imageObj->resize($this->size['width'], $this->size['height'], function ($constraint) {
                    $constraint->aspectRatio();
                });
            }

            $imageObj->save($this->thumbnailPath, $this->quality);
        }
    }

    public function convert($imageUrl)
    {
        $imageObj = $this->imageManager->make($imageUrl);
        $pictureId = $this->pictureIdGenerator->generate($imageObj->encoded);
        $this->init($pictureId, null);
        $dir = pathinfo($this->originalPath, PATHINFO_DIRNAME);
        if(!file_exists($this->originalPath)){
            if(!file_exists($dir)){
                mkdir($dir, 0777, true);
            }
            $imageObj->save($this->originalPath, $this->quality);
        }
        return $this;
    }
}
