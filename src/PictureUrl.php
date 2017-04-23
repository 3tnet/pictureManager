<?php

namespace Ty666\PictureManager;


abstract class PictureUrl implements PictureUrlInterface
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }
}