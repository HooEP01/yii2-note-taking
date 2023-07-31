<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\gii\generators\model;

use yii\gii\CodeFile;
use Yii;

/**
 * Class Generator
 * @package common\base\gii\generators\model
 */
class Generator extends \yii\gii\generators\model\Generator
{
    public $useTablePrefix = true;
    public $enableI18N = true;
    public $messageCategory = 'model';
    public $ns = 'common\models';
    public $baseClass = 'common\base\db\ActiveRecord';
    public $queryNs = 'common\models';
    public $queryBaseClass = 'common\base\db\ActiveQuery';

    protected $_commonFields = [
        'id', 'position', 'description', 'code', 'name', 'title', 'status', 'remark', 'slug', 'brief',
        'createdBy', 'createdAt', 'updatedBy', 'updatedAt', 'deletedBy', 'deletedAt',
        'cacheData', 'configuration', 'cacheTranslation', 'isActive', 'isVerified', 'provider',
        'imageId', 'parentId', 'userId', 'deeplinkId', 'agencyId', 'developerId', 'publisherId', 'projectId', 'ownerType', 'ownerKey',
        'startDatetime', 'endDatetime', 'expiryDatetime', 'precision', 'magnifier', 'amount',
        'name', 'shortName', 'displayName', 'stateCode', 'countryCode', 'currencyCode', 'cityId',
        'latitude', 'longitude', 'brief', 'htmlContent', 'purifiedContent', 'content',
        'token', 'tags', 'categoryIds', 'locationCodes',
        'src', 'size', 'width', 'height', 'extension', 'format',
        'requestDatetime', 'requestData', 'responseData',
    ];

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Hustle Hero Model Generator';
    }

    /**
     * @return array|\yii\gii\CodeFile[]
     */
    public function generate()
    {
        $files = parent::generate();


        foreach ($this->getTableNames() as $tableName) {
            $modelClassName = $this->generateClassName($tableName);
            $queryClassName = ($this->generateQuery) ? $this->generateQueryClassName($modelClassName) : false;
            $params = ['tableName' => $tableName, 'modelClassName' => $modelClassName, 'queryClassName' => $queryClassName];
        }


        return $files;
    }

    /**
     * @param \yii\db\TableSchema $table
     * @return array
     */
    public function generateRules($table)
    {
        $newTable = clone $table;
        foreach ($newTable->columns as $i => $column) {
            if ($column->name != 'id' && in_array($column->name, $this->_commonFields)) {
                unset($newTable->columns[$i]);
            }
        }

        return parent::generateRules($newTable);
    }

    /**
     * Generates a string depending on enableI18N property
     *
     * @param string $string the text be generated
     * @param array $placeholders the placeholders to use by `Yii::t()`
     * @return string
     */
    public function generateString($string = '', $placeholders = [])
    {
        if ($this->enableI18N) {
            $tableName = $this->tableName;
            if (in_array($string, $this->_commonFields)) {
                $tableName = 'common';
            }
            $string = strtolower($tableName) . '.' . $string;
            return "Yii::t('" . $this->messageCategory . "', '" . $string . "')";
        }

        return parent::generateString($string, $placeholders);
    }
}
