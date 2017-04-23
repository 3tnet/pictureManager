<?php

namespace Ty666\PictureManager;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Ty666\PictureManager\Exception\UploadException;


class PictureUploader
{

    protected $disk;
    /**
     * @var PictureUrlManager
     */
    protected $pictureUrlManager;
    /**
     * @var PictureIdGenerator
     */
    protected $pictureIdGenerator;

    public function __construct($disk, PictureUrlManager $pictureUrlManager, PictureIdGenerator $pictureIdGenerator)
    {
        $this->disk = $disk;
        $this->pictureUrlManager = $pictureUrlManager;
        $this->pictureIdGenerator = $pictureIdGenerator;
    }

    /**
     * 多图上传
     */
    public function uploadMultiple($imageFiles)
    {
        $return = [];
        foreach ($imageFiles as $imageFile) {
            $return[] = $this->upload($imageFile);
        }
        return $return;
    }

    /**
     * 上传图片
     * @param UploadedFile $imageFile
     * @throws UploadException         If upload is fail
     * @return string
     */
    public function upload($imageFile)
    {
        if ($imageFile instanceof UploadedFile) {
            if ($imageFile->isValid()) {
                $pictureId = $this->pictureIdGenerator->generate($imageFile);
                $this->disk->putFileAs('', $imageFile, $this->pictureUrlManager->getOriginalPath($pictureId));
                return $pictureId;
            } else {
                throw new UploadException($imageFile->getErrorMessage());
            }
        } else {
            throw new UploadException();
        }
    }
}