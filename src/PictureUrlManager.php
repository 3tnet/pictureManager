<?php


namespace Ty666\PictureManager;

use Closure;
use InvalidArgumentException;

class PictureUrlManager
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * The array of resolved filesystem drivers.
     *
     * @var array
     */
    protected $disks = [];

    /**
     * The registered custom driver creators.
     *
     * @var array
     */
    protected $customCreators = [];

    /**
     * Create a new filesystem manager instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application $app
     * @return void
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    public function disk($name = null)
    {
        $name = $name ?: $this->getDefaultDisk();

        return $this->disks[$name] = $this->get($name);
    }

    /**
     * Attempt to get the disk from the local cache.
     *
     * @param  string $name
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected function get($name)
    {
        return isset($this->disks[$name]) ? $this->disks[$name] : $this->resolve($name);
    }

    protected function resolve($name)
    {
        $config = $this->getConfig($name);
        if (isset($this->customCreators[$name])) {
            return $this->customCreators[$name]($this->app, $config);
        }

        $driverMethod = 'create' . ucfirst($name) . 'Driver';

        if (method_exists($this, $driverMethod)) {
            return $this->{$driverMethod}($config);
        } else {
            throw new InvalidArgumentException("Driver [{$name}] is not supported.");
        }
    }

    /**
     * Get configuration.
     *
     * @param  string $name
     * @return array
     */
    protected function getConfig($name)
    {
        $config = $this->app['config']['picture'];
        $driverConfig = isset($config['disks'][$name]) ? $config['disks'][$name] : [];
        if (!isset($driverConfig['sizeList'])) {
            $driverConfig['sizeList'] = $config['sizeList'];
        }
        if (!isset($driverConfig['quality'])) {
            $driverConfig['quality'] = $config['quality'];
        }
        return $driverConfig;
    }

    protected function createPublicDriver($config)
    {
        return new PublicPictureUrl($config);
    }

    protected function createQiniuDriver($config)
    {
        return new QiniuPictureUrl($config);
    }

    /**
     * Get the default cloud driver name.
     *
     * @return string
     */
    public function getDefaultDisk()
    {
        return $this->app['config']['picture.default_disk'];
    }

    /**
     * Register a custom driver creator Closure.
     *
     * @param  string $driver
     * @param  \Closure $callback
     * @return $this
     */
    public function extend($driver, Closure $callback)
    {
        $this->customCreators[$driver] = $callback;
        return $this;
    }

    /**
     * Dynamically call the default driver instance.
     *
     * @param  string $method
     * @param  array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->disk()->$method(...$parameters);
    }
}