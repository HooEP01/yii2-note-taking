<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

/**
 * This is the template for generating the ActiveQuery class.
 */

/* @var $this yii\web\View */
/* @var $generator common\base\gii\generators\model\Generator */
/* @var $tableName string full table name */
/* @var $className string class name */
/* @var $tableSchema yii\db\TableSchema */
/* @var $labels string[] list of attribute labels (name => label) */
/* @var $rules string[] list of validation rules */
/* @var $relations array list of relations (name => relation declaration) */
/* @var $className string class name */
/* @var $modelClassName string related model class name */

$modelFullClassName = $modelClassName;
if ($generator->ns !== $generator->queryNs) {
    $modelFullClassName = '\\' . $generator->ns . '\\' . $modelFullClassName;
}

echo "<?php\n";
?>
/**
* @copyright Copyright (c) Hustle Hero
*/

namespace <?= $generator->queryNs ?>;

use <?= ltrim($generator->queryBaseClass, '\\') . ";\n" ?>

/**
 * This is the ActiveQuery class for [[<?= $modelFullClassName ?>]].
 *
 * @see <?= $modelFullClassName . "\n" ?>
 */
class <?= $className ?> extends ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return <?= $modelFullClassName ?>[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return <?= $modelFullClassName ?>|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
