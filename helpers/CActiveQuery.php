<?php

namespace app\helpers;

use yii\db\ActiveQuery;
use yii\db\ActiveQueryInterface;

class CActiveQuery extends ActiveQuery implements ActiveQueryInterface
{
    public function populate($rows): array
    {
        $models = parent::populate($rows);

        if (!$this->asArray) {
            return $models;
        }

        $class = $this->modelClass;
        $additional_fields = method_exists($class, 'virFields') ? $class::virFields() : [];
        foreach ($models as &$model) {
            if (!empty($additional_fields)) {
                foreach ($additional_fields as $attr => $val) {
                    if (is_string($val)) {
                        $model = array_merge($model, [$attr => $val]);
                    } elseif (is_callable($val)) {
                        $model = array_merge($model, [$attr => call_user_func($val, $model)]);
                    }
                }
            }
        }
        return $models;
    }
}