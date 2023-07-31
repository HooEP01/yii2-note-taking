<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace backend\controllers;

use backend\base\web\Controller;
use common\base\helpers\ImageUploader;
use common\base\helpers\Json;
use common\base\helpers\NanoIdHelper;
use yii\web\ServerErrorHttpException;
use yii\web\UploadedFile;

/**
 * Class ImageController
 * @package backend\controllers
 */
class ImageController extends Controller
{
    /**
     * @param $action
     * @return bool
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action)
    {
        if (in_array($action->id, ['upload'])) {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    /**
     * Upload image for TinyMCE
     * @return string
     * @throws ServerErrorHttpException
     * @throws \yii\base\Exception
     * @throws \Exception
     */
    public function actionUpload()
    {
        $file = UploadedFile::getInstanceByName('file');

        $path = sprintf('html/%d/%d', date('Y'), date('m'));

        $uploader = new ImageUploader();
        $uploader->path($path)->id(NanoIdHelper::generate());
        $uploader->file($file);
        if ($uploader->save()) {
            $src = $uploader->getFileSource();
            $src = ImageUploader::resolveFileSource($src);
            return Json::encode([
                'success' => true,
                'location' => $src,
            ]);
        }

        throw new ServerErrorHttpException('Server failed to process the uploaded image.');
    }
}