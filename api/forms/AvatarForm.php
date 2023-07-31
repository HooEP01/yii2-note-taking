<?php
/**
 * @author RYU Chua <me@ryu.my>
 */

namespace api\forms;

use Yii;
use yii\validators\UrlValidator;

/**
 * Class AvatarForm
 * @package api\forms
 */
class AvatarForm extends BaseUserForm
{
    /**
     * @var string Image MIME
     */
    public $mime;

    /**
     * @var string Base64 string
     */
    public $content;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['mime'], 'filter', 'filter' => 'strtolower'],
            [['mime', 'content'], 'required'],
            [['mime'], 'in', 'range' => ['image/jpg', 'image/jpeg', 'image/png', 'text/url']],
            [['content'], 'required'],
            [['content'], 'string'],
            [['content'], 'common\base\validators\Base64ImageValidator', 'formats' => 'jpg, png'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'content' => Yii::t('form', 'avatar.content'),
        ];
    }

    /**
     * @return bool
     */
    public function process()
    {
        if ($this->validate()) {
            if ($this->mime === 'text/url') {
                $validator = new UrlValidator();
                if (!$validator->validate($this->content)) {
                    $this->addError('content', 'Invalid Url');
                    return false;
                }
            }

            $model = $this->user->getImageModel();
            return $model->upload([
                'content' => $this->content,
                'mime' => $this->mime,
                'width' => 512,
                'height' => 512
            ]);
        }

        return false;
    }
}