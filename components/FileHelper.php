<?php

namespace app\components;

use yii\base\InvalidConfigException;

/**
 * Class FileHelper.
 *
 * @package app\components
 */
class FileHelper extends \yii\helpers\FileHelper
{
    /**
     * @var string the original name of the file being uploaded
     */
    public $name;
    /**
     * @var string the path of the uploaded file on the server.
     * Note, this is a temporary file which will be automatically deleted by PHP
     * after the current request is processed.
     */
    public $tempName;

    /**
     * @var string|null
     */
    public $folderName;

    /**
     * @var string
     */
    public $baseFolder = '/';

    /**
     * FileHelper constructor.
     *
     * @param      $filename
     * @param null $folder
     */
    public function __construct($filename = null, $folder = null)
    {
        if ($folder) {
            if (!file_exists($folder)) {
                $oldMask = umask(0);
                mkdir($folder, 0755, true);
                umask($oldMask);
            }

            $this->folderName = $folder;
            $shortFileName = $this->getJustFilename($filename);
            $this->tempName = $filename;
            $this->name = $this->folderName . '/' . $shortFileName;
        } else {
            $this->tempName = $filename;
            $this->name = $filename;
        }
    }

    /**
     * @param $folder
     *
     * @return $this
     */
    public function setBaseFolder($folder)
    {
        $this->baseFolder = $folder;

        return $this;
    }

    /**
     * @param null $filename
     *
     * @return bool
     */
    public function existFile($filename = null)
    {
        if (empty($filename)) {
            $filename = $this->name;
        }

        return file_exists($filename);
    }

    /**
     * @param      $fileData
     * @param null $filename
     *
     * @return null|string
     */
    public function saveData($fileData, $filename = null)
    {
        if (empty($filename)) {
            $filename = $this->name;
        }
        if ($fileData) {
            file_put_contents($filename, $fileData);
        }

        return $filename;
    }

    /**
     * @param null|string $filename
     *
     * @return string original file base name
     */
    public function getBaseName($filename = null)
    {
        if (empty($filename)) {
            $filename = $this->name;
        }
        $pathInfo = pathinfo($filename, PATHINFO_FILENAME);
        return $pathInfo;
    }

    /**
     * @param null $filename
     *
     * @return string file extension
     */
    public function getExtension($filename = null)
    {
        if (empty($filename)) {
            $filename = $this->name;
        }
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }

    /**
     * @param null $filename
     *
     * @return string
     */
    public function getFilePath($filename = null)
    {
        if (empty($filename)) {
            $filename = $this->name;
        }

        return strtolower(pathinfo($filename, PATHINFO_DIRNAME));
    }

    /**
     * @param null $filename
     *
     * @return string
     */
    public function getJustFilename($filename = null)
    {
        return $this->getBaseName($filename) . ($this->getExtension($filename) ? '.' . $this->getExtension($filename) : '');
    }

    /**
     * @param null $filename
     *
     * @return null|string
     */
    public function getSubFolder($filename = null)
    {
        $path = $this->getFilePath($filename);

        return preg_replace('/.+\/(.+)$/', "$1", $path);
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        $method = 'get' . ucfirst($name);
        if (method_exists($this, $method)) {
            return $this->{$method}();
        }

        return parent::__get($name);
    }

    /**
     * Saves the uploaded file.
     * Note that this method uses php's move_uploaded_file() method. If the target file `$file`
     * already exists, it will be overwritten.
     * @param string $file the file path used to save the uploaded file
     * @param bool $deleteTempFile whether to delete the temporary file after saving.
     * If true, you will not be able to save the uploaded file again in the current request.
     * @return bool true whether the file is saved successfully
     * @see error
     */
    public function saveAs($file = null, $deleteTempFile = true)
    {
        if (empty($file)) {
            $file = $this->getFilePath() . $this->getJustFilename();
        }
        if (!file_exists($file)) {
            copy($this->tempName, $file);
            $this->name = $file;
        }
        $result = \Yii::$app->storage->save($file);

        if (!$result) {
            return false;
        }
        $this->name = $file;
        if ($deleteTempFile) {
            return @unlink($file);
        }

        return $result;
    }

    /**
     * @param null $file
     *
     * @return bool|string|null
     */
    public function downloadFile($file = null)
    {
        if (is_null($file)) {
            $file = $this->getFilePath() . '/' . $this->getJustFilename();
        }
        $result = \Yii::$app->storage->download($file);

        return ($result ? $file : false);
    }

    /**
     * @return string
     */
    public function getRelativePath()
    {
        return trim(preg_replace('/'.preg_quote($this->baseFolder, '/').'(.+)/isu', "$1", $this->getFilePath() . '/' . $this->getJustFilename()), '/');
    }

    /**
     * @param array $extensions
     * @param bool  $recursive
     *
     * @return FileHelper[]
     */
    public function listFiles($extensions = [], $recursive = false)
    {
        if (!is_dir($this->name)) {
            return [];
        }
        $options = [];
        if ($recursive) {
            $options['recursive'] = true;
        }
        $files = self::findFiles($this->name, $options);

        if ($extensions) {
            $files = array_filter($files, function ($filename) use ($extensions) {
                $extension = self::getExtension($filename);
                return in_array($extension, $extensions);
            });
        }

        return array_map(
            function($filename) {
                return new self($filename);
            },
            $files
        );
    }

    /**
     * @return bool
     */
    public function delete()
    {
        if (file_exists($this->name)) {
            self::unlink($this->name);
        }

        return true;
    }

    /**
     * @return string|null
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * @param null   $magicFile
     * @param bool   $checkExtension
     *
     * @return mixed|string|null
     * @throws InvalidConfigException
     */
    public function getMime($magicFile = null, $checkExtension = false)
    {
        $file = $this->getFilePath() . '/' . $this->getJustFilename();

        return parent::getMimeType($file, $magicFile, $checkExtension);
    }
}
