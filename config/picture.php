<?php

return [
    /**
     * 显示图片的路由名称
     */
    'imageRouteName' => 'image',
    /*
     * 图片上传路径
     */
    'uploadPath' => realpath('uploads/imgs/'),
    /**
     * 允许的尺寸列表
     * tip:0,0是原图大小
     */
    'sizeList' => [
        'is'=>'34,34',
        'xs'=>'50,50',
        'l'=>'100,100',
        'b' => '600,0',
        'r'=>'0,0'//原图大小
    ],
    /**
     * 允许的类型列表
     */
    'allowTypeList' => [
        'png',
        'jpg',

    ],
    /**
     * 默认添加水印
     */
    'needWaterMark'=>false,
    /**
     * 水印图片
     * tip:目录相对public目录
     */
    'watermark' => '',
    /**
     * 图片路由
     */
    'route' => [
        'path'=>'/pic',
        'name'=>'pic',
        'middleware'=>[]
    ]
];