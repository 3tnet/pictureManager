<?php
namespace Ty666\PictureManager\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class UploadException extends HttpException
{
    public function __construct($message = '图片上传出错', $statusCode = 500, \Exception $previous = null, array $headers = array(), $code = 0)
    {
        parent::__construct($statusCode, $message, $previous, $headers, $code);
    }

}