<?php

namespace Ty666\PictureManager;
use Intervention\Image\Facades\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Ty666\PictureManager\Exception\PictureNotFountException;
use Ty666\PictureManager\Exception\UploadException;
use Log;
class PictureManager
{
    //图片实际尺寸
    public $path;
    //图片类型
    public $type;
    //需要打水印
    public $needWaterMark = false;
    //水印
    protected $watermark='';
    //图片质量
    protected $quality = null;
    //图片上传路径
    protected $uploadDir;
    protected $image_id = null;
    protected $size='';//尺寸 字符串表示 [b,xs,lg..]
    protected $realSize=[];//实际尺寸 数组表示 ['width'=>50,'height'=>50]
    //原图路径
    protected $original;
    //允许的尺寸列表
    protected $sizeList = [];
    //允许的类型列表
    protected $allowTypeList = [];

    /**
     * 设置需要水印
     * @return $this
     */
    public function withWaterMark(){
        $this->needWaterMark = true;
        return $this;
    }

    /**
     * 取消水印
     * @return $this
     */
    public function cancleWaterMark(){
        $this->needWaterMark = false;
        return $this;
    }
    public function setAllowTypeList($allowTypeList){
        $this->allowTypeList = $allowTypeList;
        return $this;
    }
    public function setSizeList($sizeList){
        $this->sizeList = $sizeList;
        return $this;
    }
    public function setUploadDir($uploadDir){
        $this->uploadDir = $uploadDir;
        return $this;
    }
    public function setWatermark($watermark){
        $this->watermark = $watermark;
        return $this;
    }

    /**
     * 初始化
     * @param string $image_id
     * @param string $size
     * @param string $type
     * @thows PictureNotFountException
     * @return $this
     */
    public function init($image_id , $size ,$type){

        $this->image_id = $image_id;
        if(!array_key_exists($size,$this->sizeList)){
            throw new PictureNotFountException("图片尺寸出错,\"$size\"不存在！");
        }else{
            $this->size = $size;
        }
        if(!in_array($type,$this->allowTypeList)){
            throw new PictureNotFountException("图片类型出错,\"$type\"不存在！");
        }else{
            $this->type = $type;
        }

        $this->setPath();
        return $this;
    }

    /**
     * 获取图片路径
     * @param $image_id
     * @return array
     */
    private function getPath($image_id){
        //获取路径
        return [
            $this->uploadDir.substr($image_id, 0, 2).'/',
            substr($image_id, 2)
        ];
    }

    /**
     * 设置带尺寸的图片路径
     * @return $this
     */
    private function setPath(){

        $path = $this->getPath($this->image_id);
        $this->original = $path[0].$path[1];

        //获取具体尺寸
        list($this->realSize['width'],$this->realSize['height']) = explode(',',$this->sizeList[$this->size],2);

        //尺寸是原图大小
        if( 0 == $this->realSize['width'] ){
            $this->quality = 90;
            $this->path = $this->original.'_optimize';
        }else{
            //带尺寸的图片路径
            $this->quality = null;
            $imgPath = $this->original.'_'.$this->realSize['width'].'_'.$this->realSize['height'];
            $this->path = $imgPath;
        }
        return $this;
    }

    /**
     * 显示图片
     * @param string $image_id
     * @param string $size
     * @param string $type
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function show($image_id = '', $size ='',$type = ''){
        if($this->original == ''){
            $this->init($image_id, $size, $type);
        }

        //判断图片是否存在
        if(!file_exists($this->original)){
            //404
            throw new PictureNotFountException;
        }elseif(!file_exists($this->path)){//缩略图是否没有生成
            $this->createPicture();
        }
        $fileName = "{$this->image_id}_{$this->size}.{$this->type}";

        //判断客户端是否有缓存
        if(isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH']===$fileName){
            //有缓存
            $response = response('',304);
        }else{
            //无缓存
            $response = Image::make($this->path)->response($this->type);
        }
        $response->header('Content-Disposition' , 'inline; filename='.$fileName);
        $response->header('Etag',$fileName);
        $response->header('Cache-Control','public, max-age=31536000');
        return $response;
    }

    /**
     * 生成图片
     */
    protected function createPicture(){
        $image = app('image')->make($this->original);
        if($this->quality){
            //添加水印
//            $this->watermark != '' && $image->insert($this->watermark,'bottom-right', 10, 10);
            $image->save($this->path , $this->quality);
        }else{
            $image->resize($this->realSize['width'], $this->realSize['height']);
            //添加水印
//            $this->watermark != '' && $image->insert($this->watermark,'bottom-right', 10, 10);
            $image->save($this->path);
        }

    }

    /**
     * 多图上传
     */
    public function uploadMultiple($imageFiles){
        $return = [];
        foreach ($imageFiles as $imageFile){
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
    public function upload(UploadedFile $imageFile){

        if($imageFile instanceof UploadedFile){
            if($imageFile->isValid()){
                //获取图片扩展名
                $minmeType = $imageFile->getMimeType();
                $suffix = substr(strstr($minmeType,'/',false),1);
                $suffix = $suffix=='jpeg'?'jpg':$suffix;
                //md5
                $image_id = md5_file($imageFile->getRealPath());
                $path = $this->getPath($image_id);
                if($this->needWaterMark && $this->watermark!=''){
                    //需要添加水印
                    Image::make($imageFile)
                        ->insert($this->watermark,'bottom-right', 10, 10)
                        ->save($path[0].$path[1]);
                }else{
                    $imageFile->move($path[0],$path[1]);
                }

                return $image_id.'.'.$suffix;

            }else{
                throw new UploadException($imageFile->getErrorMessage());
            }
        }else{
            throw new UploadException();
        }
    }
}