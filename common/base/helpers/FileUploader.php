<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\helpers;

use Aws\Result;
use common\base\traits\RuntimeCache;
use yii\base\BaseObject;
use yii\base\Exception;
use yii\base\InvalidCallException;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\Url;
use yii\validators\UrlValidator;
use yii\web\UploadedFile;
use Yii;

/**
 * Class FileUploader
 * @property string $file
 * @package common\base\web
 */
class FileUploader extends BaseObject
{
    use RuntimeCache;

    const DRIVER_LOCAL = 'local';
    const DRIVER_GCLOUD = 'gcloud';
    const DRIVER_S3 = 's3';

    const PERMISSION_PUBLIC = 'public';
    const PERMISSION_PRIVATE = 'private';

    /**
     * @var string the id of the file
     */
    private $_id;
    /**
     * @var string the path prefix to be saved
     */
    private $_path;
    /**
     * @var string The working file path
     */
    private $_file;
    /**
     * @var string
     */
    private $_extension;
    /**
     * @var string
     */
    private $_mime;
    /**
     * @var string
     */
    private $_fileSource;
    /**
     * @var string
     */
    private $_nameExtra;
    /**
     * @var string
     */
    private $_permission;
    /**
     * @var array
     */
    private $_property = [];

    private $_filename;

    /**
     * @var array
     */
    private $_knownContentTypes = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'image/svg+xml',
        'image/x-icon',
        'text/html'
    ];

    /**
     * Save and remove the file
     * @return bool
     * @throws Exception
     */
    public function save()
    {
        $this->generateProperty();

        $driver = $this->getDriver();
        if ($driver === self::DRIVER_LOCAL) {
            return $this->saveToLocal();
        } elseif ($driver === self::DRIVER_S3) {
            return $this->saveToS3();
        } elseif ($driver === self::DRIVER_GCLOUD) {
            return $this->saveToGoogleStorage();
        }

        throw new Exception('Driver: ' . $driver . ' is not supported yet !');
    }

    /**
     * @return string
     */
    public function getDriver()
    {
        return $this->getOrSetRuntimeData(__METHOD__, function () {
            $driver = ArrayHelper::getValue(Yii::$app->params, 'upload.driver', 'not-set');
            if (!in_array($driver, [self::DRIVER_LOCAL, self::DRIVER_S3, self::DRIVER_GCLOUD])) {
                throw new Exception('Driver: ' . $driver . ' is not support !');
            }
            return $driver;
        });
    }

    /**
     * @param string $value
     * @return $this
     */
    public function id($value)
    {
        $this->_id = trim($value);
        $this->_id = strtolower($this->_id);
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function permission($value)
    {
        if (in_array($value, [static::PERMISSION_PUBLIC, static::PERMISSION_PRIVATE])) {
            $this->_permission = $value;
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function makePrivate()
    {
        return $this->permission(static::PERMISSION_PRIVATE);
    }

    /**
     * @return $this
     */
    public function makePublic()
    {
        return $this->permission(static::PERMISSION_PUBLIC);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function nameExtra($value)
    {
        $this->_nameExtra = trim($value);
        $this->_nameExtra = strtolower($this->_nameExtra);
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function extension($value)
    {
        $this->_extension = trim($value);
        $this->_extension = strtolower($this->_extension);
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function mime($value)
    {
        $this->_mime = trim($value);
        $this->_mime = strtolower($this->_mime);
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function path($value)
    {
        $this->_path = trim($value, '/');
        return $this;
    }

    /**
     * Load the file with file path or UploadedFile Object
     * @param string|UploadedFile $value
     * @return $this
     * @throws Exception
     */
    public function file($value)
    {
        if (isset($this->_file)) {
            throw new InvalidCallException('$file already loaded !');
        }

        if (is_string($value)) {
            if (!is_file($value)) {
                return $this->base64($value);
            }

            $this->_file = $value;
            return $this;
        }

        if ($value instanceof UploadedFile) {
            $tempFile = $this->generateTemporaryFile();
            if ($value->saveAs($tempFile) && $this->isFileExist($tempFile)) {
                $this->_file = $tempFile;
                $this->getMime();
                return $this->extension($value->extension);
            }

            throw new Exception('Failed to save UploadedFile to ' . $tempFile);
        }

        throw new Exception('$value passed in is not a supported format !');
    }

    /**
     * Load the file with base64 string
     * @param string $value
     * @return $this
     * @throws Exception
     */
    public function base64($value)
    {
        if (isset($this->_file)) {
            throw new InvalidCallException('$file already loaded !');
        }

        //TODO: validate the value to ensure is base64 ?
        if (!is_string($value)) {
            throw new InvalidCallException('$value is not a valid base64 string !');
        }

        $binary = base64_decode($value, true);

        $tempFile = $this->generateTemporaryFile();
        $handle = fopen($tempFile, 'w');
        fwrite($handle, $binary);
        fclose($handle);

        if ($this->isFileExist($tempFile)) {
            $this->_file = $tempFile;
            return $this;
        }

        throw new Exception('Failed to save Base64 encoded file to ' . $tempFile);
    }

    /**
     * Load the file with https url
     * @param string $value
     * @return $this
     * @throws Exception
     */
    public function url($value)
    {
        if (isset($this->_file)) {
            throw new InvalidCallException('$file already loaded !');
        }

        $urlValidator = new UrlValidator();
        if (!$urlValidator->validate($value)) {
            throw new InvalidCallException($value . ' is not a valid URL !');
        }

        $handle = @fopen($value, 'rb');
        $contents = @stream_get_contents($handle);
        @fclose($handle);

        $tempFile = $this->generateTemporaryFile();
        $handle = @fopen($tempFile, 'w');
        @fwrite($handle, $contents);
        @fclose($handle);

        if ($this->isFileExist($tempFile)) {
            $this->_file = $tempFile;
            return $this;
        }

        throw new Exception('Failed to save url ' . $value . ' file to ' . $tempFile);
    }

    /**
     * @return bool
     */
    public function getHasFile()
    {
        return isset($this->_file);
    }

    /**
     * @return string
     */
    protected function generateNewFileName()
    {
        if (isset($this->_filename)) {
            return sprintf('%s.%s', $this->_filename, $this->getExtension());
        }

        if (isset($this->_nameExtra) && !empty($this->_nameExtra)) {
            return sprintf('%s_%s_%s.%s', $this->getId(), $this->_nameExtra, time(), $this->getExtension());
        }

        return sprintf('%s_%s.%s', $this->getId(), time(), $this->getExtension());
    }

    /**
     * @param string $value
     * @return $this
     */
    public function filename($value)
    {
        $this->_filename = $value;
        return $this;
    }

    /**
     * @return bool
     * @throws Exception
     */
    protected function saveToLocal()
    {
        try {
            $folder = static::getLocalUploadPath();
            $key = trim($this->getPath(), '/') . DIRECTORY_SEPARATOR . $this->generateNewFileName();
            $fullFilePath = rtrim($folder) . DIRECTORY_SEPARATOR . $key;

            $basePath = dirname($fullFilePath);
            FileHelper::createDirectory($basePath);

            copy($this->getFile(), $fullFilePath);
            if ($this->isFileExist($fullFilePath) && $this->deleteFile()) {
                $this->setFileSource($key);
                return true;
            }
        } catch (\Exception $e) {
            Yii::error($e);
            $this->deleteFile();
        }

        return false;
    }

    /**
     * @return bool
     * @throws Exception
     */
    protected function saveToS3()
    {
        try {
            $key = trim($this->getPath(), '/') . '/' . $this->generateNewFileName();

            Yii::beginProfile('FileUploader::saveToS3');

            $client = Yii::$app->aws->getS3Client();

            $data = [
                'ACL' => $this->getIsPublic() ? 'public-read' : 'bucket-owner-full-control',
                'Body' => fopen($this->getFile(), 'r'),
                'Bucket' => ArrayHelper::getValue(Yii::$app->params, 'upload.s3.bucket', 'property-genie_fake_bucket'),
                'Key' => $key,
            ];

            if (isset($this->_mime) && in_array($this->_mime, $this->_knownContentTypes)) {
                $data['ContentType'] = $this->_mime;
            }

            $result = $client->putObject($data);

            Yii::debug($result);
            Yii::endProfile('FileUploader::saveToS3');

            if ($result instanceof Result && ($url = $result->get('ObjectURL')) !== null && $this->deleteFile()) {
                $this->setFileSource($key);
                return true;
            }
        } catch (\Exception $e) {
            Yii::error($e);
            $this->deleteFile();
        }

        return false;
    }


    /**
     * @return bool
     * @throws Exception
     */
    protected function saveToGoogleStorage()
    {
        try {
            $key = trim($this->getPath(), '/') . '/' . $this->generateNewFileName();

            Yii::beginProfile('FileUploader::saveToGoogleStorage');

            $bucketName = ArrayHelper::getValue(Yii::$app->params, 'upload.gcp.bucket', 'property-genie_fake_bucket');

            $storage = Yii::$app->gcp->getStorageClient();
            $bucket = $storage->bucket($bucketName);

            if (!$bucket->exists()) {
                return false;
            }

            $acl = $this->getIsPublic() ? 'publicRead' : 'bucketOwnerFullControl';
            $object = $bucket->upload(fopen($this->getFile(), 'r'), [
                'name' => $key,
                'predefinedAcl' => $acl,
                'acl' => [
                    'entity' => 'allUsers',
                    'role' => 'READER',
                ],
            ]);

            Yii::endProfile('FileUploader::saveToGoogleStorage');

            if ($object->exists() && $this->deleteFile()) {
                $this->setFileSource($key);
                return true;
            }
        } catch (\Exception $e) {
            Yii::error($e);
            $this->deleteFile();
        }

        return false;
    }

    /**
     * @param string $file
     * @return $this
     */
    protected function replaceFile($file)
    {
        if ($this->isFileExist($file)) {
            copy($file, $this->_file);
            unlink($file);
        }
        return $this;
    }

    /**
     * @return bool
     * @throws Exception
     */
    protected function deleteFile()
    {
        return unlink($this->getFile());
    }

    /**
     * generate property like file Size
     */
    protected function generateProperty()
    {
        $this->addProperty('file_size', filesize($this->getFile()));
    }

    /**
     * @param string $key
     * @param mixed $value
     * @throws Exception
     */
    protected function addProperty($key, $value)
    {
        $this->_property[$key] = $value;
    }

    /**
     * @param string $key
     * @param null $default
     * @return mixed
     * @throws \Exception
     */
    protected function getProperty($key, $default = null)
    {
        return ArrayHelper::getValue($this->_property, $key, $default);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getFileSize()
    {
        return $this->getProperty('file_size', -1);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getFileSource()
    {
        if (!isset($this->_fileSource)) {
            throw new Exception('$fileSource is not available !!');
        }

        return $this->_fileSource;
    }

    /**
     * @return string
     */
    public function getPermission()
    {
        if (!isset($this->_permission)) {
            return static::PERMISSION_PUBLIC;
        }

        if (!in_array($this->_permission, [static::PERMISSION_PUBLIC, static::PERMISSION_PRIVATE])) {
            return static::PERMISSION_PUBLIC;
        }

        return $this->_permission;
    }

    /**
     * @return bool
     */
    public function getIsPublic()
    {
        return $this->getPermission() === static::PERMISSION_PUBLIC;
    }

    /**
     * @param string $value
     * @return $this
     */
    protected function setFileSource($value)
    {
        $this->_fileSource = ltrim($value, '/');

        $driver = $this->getDriver();
        if ($driver === self::DRIVER_S3) {
            $this->_fileSource = 's3:' . $this->_fileSource;
        } elseif ($driver === self::DRIVER_GCLOUD) {
            $this->_fileSource = 'gcloud:' . $this->_fileSource;
        }
        return $this;
    }

    /**
     * @return bool|resource
     * @throws Exception
     */
    public function getFileContent()
    {
        if (!isset($this->_file)) {
            throw new Exception('$file must be defined !');
        }

        return @file_get_contents($this->_file);
    }

    /**
     * @return bool|resource
     * @throws Exception
     */
    public function getFileResource()
    {
        if (!isset($this->_file)) {
            throw new Exception('$file must be defined !');
        }

        return @fopen($this->_file, 'r');
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getFile()
    {
        if (!isset($this->_file)) {
            throw new Exception('$file must be defined !');
        }

        return $this->_file;
    }

    /**
     * @return string
     * @throws Exception
     */
    protected function getPath()
    {
        if (!isset($this->_path)) {
            throw new Exception('$path must be defined !');
        }

        return $this->_path;
    }

    /**
     * @return string
     */
    protected function getId()
    {
        if (isset($this->_id)) {
            return $this->_id;
        }

        return $this->_id = mt_rand(100, 999);
    }

    /**
     * @return string
     */
    public function getMime()
    {
        if (!isset($this->_mime)) {
            try {
                $this->_mime = mime_content_type($this->getFile());
            } catch (\Exception $e) {
                Yii::error($e);
            }
        }

        return $this->_mime;
    }

    /**
     * @return string
     */
    public function getExtension()
    {
        if (isset($this->_extension)) {
            return $this->_extension;
        }

        return $this->getExtensionFromMime();
    }

    /**
     * @return string
     */
    public function getExtensionFromMime()
    {
        return 'property-genie-unknown';
    }

    /**
     * @param string $file
     * @return bool
     */
    protected function isFileExist($file)
    {
        return file_exists($file) && is_file($file) && filesize($file) > 0;
    }

    /**
     * @return string
     * @throws Exception
     */
    protected function generateTemporaryFile()
    {
        $folder = sys_get_temp_dir() . DIRECTORY_SEPARATOR . '_alps';
        FileHelper::createDirectory($folder);
        return tempnam($folder, '_uploader');
    }

    /**
     * @return string
     * @throws \Exception
     */
    public static function getLocalUploadPath()
    {
        return Yii::getAlias(ArrayHelper::getValue(Yii::$app->params, 'upload.local.path', '@app/web/uploads'));
    }

    /**
     * @param string $fileSource
     * @return string
     */
    public static function resolveDriverFromFileSource($fileSource)
    {
        if (substr($fileSource, 0, 3) === 's3:') {
            return self::DRIVER_S3;
        } elseif (substr($fileSource, 0, 7) === 'gcloud:') {
            return self::DRIVER_GCLOUD;
        }

        return self::DRIVER_LOCAL;
    }

    /**
     * @param string $fileSource
     * @return string | false
     */
    public static function resolvePathFromFileSource($fileSource)
    {
        $driver = static::resolveDriverFromFileSource($fileSource);

        if ($driver === self::DRIVER_S3) {
            return str_replace('s3:', '', $fileSource);
        }

        if ($driver === self::DRIVER_GCLOUD) {
            return str_replace('gcloud:', '', $fileSource);
        }

        return false;
    }

    /**
     * @param string $fileSource
     * @param array $options
     * @return string
     * @throws \Exception
     */
    public static function resolveFileSource($fileSource, $options = [])
    {
        $sign = ArrayHelper::getValue($options, 'sign', false);
        $hash = ArrayHelper::getValue($options, 'hash');

        $driver = static::resolveDriverFromFileSource($fileSource);
        if ($driver === self::DRIVER_S3) {
            $path = str_replace('s3:', '', $fileSource);

            //-- if signed request !
            if ($sign && ($signedUrl = static::generateS3SignedUrl($path)) && $signedUrl !== $path) {
                return $signedUrl;
            }

            $host = ArrayHelper::getValue(Yii::$app->params, 'upload.s3.cdn');
            $url = sprintf('%s/%s', $host, $path);
            return empty($hash) ? $url : $url . '?hash=' . $hash;
        } elseif ($driver === self::DRIVER_GCLOUD) {
            $path = str_replace('gcloud:', '', $fileSource);

            //-- if signed request !
            if ($sign && ($signedUrl = static::generateGoogleStorageSignedUrl($path)) && $signedUrl !== $path) {
                return $signedUrl;
            }

            $host = ArrayHelper::getValue(Yii::$app->params, 'upload.gcp.cdn');
            $url = sprintf('%s/%s', $host, $path);
            return empty($hash) ? $url : $url . '?hash=' . $hash;
        }

        return static::resolveLocalFileSource($fileSource, $options);
    }

    /**
     * @param string $path
     * @return string
     */
    public static function generateS3SignedUrl($path)
    {
        try {
            $data = [
                'Bucket' => ArrayHelper::getValue(Yii::$app->params, 'upload.s3.bucket', 'property-genie_fake_bucket'),
                'Key' => $path,
            ];

            if ($data['Bucket'] === 'property-genie_fake_bucket') {
                return $path;
            }

            $client = Yii::$app->aws->getS3Client();
            $cmd = $client->getCommand('GetObject', $data);
            $request = $client->createPresignedRequest($cmd, '+1 minutes');
            $signedUrl = (string) $request->getUri();

            return $signedUrl;
        } catch (\Exception $e) {
            Yii::error($e);
        }

        return $path;
    }

    /**
     * @param string $path
     * @return string
     */
    public static function generateGoogleStorageSignedUrl($path)
    {
        try {
            $bucketName = ArrayHelper::getValue(Yii::$app->params, 'upload.gcp.bucket', 'property-genie_fake_bucket');
            if ($bucketName === 'property-genie_fake_bucket') {
                return $path;
            }

            $storage = Yii::$app->gcp->getStorageClient();
            $bucket = $storage->bucket($bucketName);

            if (!$bucket->exists()) {
                return $path;
            }

            $object = $bucket->object($path);
            if (!$object->exists()) {
                return $path;
            }

            $expiry = new \DateTime('1 min');
            $signedUrl = $object->signedUrl($expiry, ['version' => 'v4']);

            return $signedUrl;
        } catch (\Exception $e) {
            Yii::error($e);
        }

        return $path;
    }

    /**
     * @param string $fileSource
     * @param array $options
     * @return string
     */
    protected static function resolveLocalFileSource($fileSource, $options = [])
    {
        $hash = ArrayHelper::getValue($options, 'hash');

        //FIXME: fix when in console, this line will get error, as Url Helper will not able to find host !

        if (!empty($fileSource)) {
            if (substr($fileSource, 0, 1) === '/') {
                return Url::base(true) . $fileSource;
            }

            $folder = static::getLocalUploadPath();
            if (($pos = strpos($folder, '/web/')) !== false) {
                $folder = substr($folder, $pos + 5);
            }

            $url = Url::base(true)  . '/' . trim($folder) . '/' . $fileSource;
            return empty($hash) ? $url : $url . '?hash=' . $hash;
        }

        $dimension = ArrayHelper::getValue($options, 'dimension', '250x250');
        return sprintf('%s/%s', 'https://via.placeholder.com', $dimension);
    }

    /**
     * @param string $fileSource
     * @return string
     */
    public static function resolveFilePath($fileSource)
    {
        $folder = static::getLocalUploadPath();
        return $folder . '/' . $fileSource;
    }
}
