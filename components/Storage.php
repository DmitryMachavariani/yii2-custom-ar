<?php

namespace app\components;

use function Clue\StreamFilter\fun;
use yii\base\Component;
use \Arhitector\Yandex\Disk;
use Arhitector\Yandex\Disk\Resource\Closed as Resource;

/**
 * Class Storage.
 *
 * @package app\components
 */
class Storage extends Component
{
    /**
     * @var string
     */
    public $token;

    /**
     * @var string string
     */
    public $folder = '';

    /**
     * @var string
     */
    public $localBaseFolder = '';

    /**
     * @var Resource
     */
    protected $resource = null;

    /**
     * @var string
     */
    protected $folderToReplace = '';

    /**
     * @var Disk
     */
    protected $disk = null;

    public function init()
    {
        $this->setDisk();
        if (strpos($this->localBaseFolder, '@') >= 0) {
            $this->folderToReplace = \Yii::getAlias($this->localBaseFolder);
        } else {
            $this->folderToReplace = $this->localBaseFolder;
        }

        parent::init();
    }

    /**
     * @return $this
     */
    protected function setDisk()
    {
        if (is_null($this->disk)) {
            $this->disk = new Disk($this->token);
        }

        return $this;
    }

    /**
     * @param string $resourcePath
     *
     * @return Resource
     */
    protected function setResource($resourcePath)
    {
        $this->setDisk();
        $this->resource = $this->disk->getResource("/{$this->folder}/{$resourcePath}");

        return $this->resource;
    }

    /**
     * @param $filePathSrc
     *
     * @return string
     */
    protected function getRelativePath($filePathSrc)
    {
        return trim(preg_replace('/'.preg_quote($this->folderToReplace, '/').'(.+)/isu', "$1", $filePathSrc), '/');
    }

    /**
     * @param $filePathSrc
     *
     * @return Disk\Operation|bool
     */
    public function save($filePathSrc)
    {
        $diskPath = '';
        $path = array_values(array_filter(
            preg_split('/\//', $this->getRelativePath($filePathSrc)),
            function ($item) {
                return !empty($item);
            }
        ));

        for ($i = 0; $i < count($path) - 1; $i++) {
            $diskPath .= $path[$i] . '/';
            $resource = $this->setResource($diskPath);
            if (!$resource->has()) {
                $resource->create();
            }
        }
        $diskPath .= $path[count($path) - 1];

        return $this->setResource($diskPath)->upload($filePathSrc, true);
    }

    /**
     * @param $filePathSrc
     *
     * @return bool
     */
    public function download($filePathSrc)
    {
        $filePathDest = $this->getRelativePath($filePathSrc);
        return $this->setResource($filePathDest)->download($filePathSrc, true);
    }

    /**
     * @param $filePathSrc
     *
     * @return Disk\Operation|Disk\Resource\Removed|bool
     */
    public function delete($filePathSrc)
    {
        $filePathDest = $this->getRelativePath($filePathSrc);
        return $this->setResource($filePathDest)->delete(true);
    }
}
