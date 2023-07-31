<?php

/**
 * @author RYU Chua <me@ryu.my>
 */

namespace common\base\audit\models;

use common\base\db\ActiveRecord;
use common\models\User;
use common\models\UserQuery;

/**
 * Class AuditTrail
 * @package common\base\audit
 *
 * @property string $id
 * @property string $entryId
 * @property string $userId
 * @property string $action
 * @property string $ownerReference
 * @property string $tableName
 * @property string $modelClass
 * @property string $modelKey
 * @property string $field
 * @property string $oldValue
 * @property string $newValue
 * @property string $traceAt
 * @property string $createdAt
 * 
 * 
 * @property User $user
 */
class AuditTrail extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%audit_trail}}';
    }

    /**
     * @return mixed
     */
    public function getDiffHtml()
    {
        $old = explode("\n", $this->oldValue);
        $new = explode("\n", $this->newValue);
        foreach ($old as $i => $line) {
            $old[$i] = rtrim($line, "\r\n");
        }
        foreach ($new as $i => $line) {
            $new[$i] = rtrim($line, "\r\n");
        }
        $diff = new \Diff($old, $new);
        return $diff->render(new \Diff_Renderer_Html_Inline);
    }

    /**
     * @return \yii\db\ActiveQuery|UserQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'userId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEntry()
    {
        return $this->hasOne(AuditEntry::class, ['id' => 'entryId']);
    }

    /**
     * @return ActiveRecord|bool
     */
    public function getParent()
    {
        /** @var ActiveRecord $parentModel */
        $parentModel = new $this->modelClass;
        $parent = $parentModel::findOne($this->modelPk);
        return $parent ? $parent : $parentModel;
    }

    /**
     * @inheritdoc
     * @return AuditTrailQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new AuditTrailQuery(get_called_class());
    }
}
