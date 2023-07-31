<?php
/**
 * @author RYU Chua <ryu@riipay.my>
 *  @link https://riipay.my
 * @copyright Copyright (c) Riipay
 */

namespace common\base\services;


use common\base\helpers\ArrayHelper;
use Da\QrCode\Component\QrCodeComponent;
use Da\QrCode\QrCode;
use Yii;

/**
 * Class Qr
 * @method QrCode setLogo(string $logo)
 * @package common\base\services
 */
class Qr extends QrCodeComponent
{
    /**
     * @return \Da\QrCode\QrCode
     * @throws \Exception
     */
    public function useLogo()
    {
        $path = ArrayHelper::getValue(Yii::$app->params, 'qr.image.path');
        $logo = Yii::getAlias($path);

        return $this->setLogo($logo);
    }
}