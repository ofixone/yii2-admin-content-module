<?php

namespace ofixone\content\models;

use kartik\form\ActiveForm;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;

abstract class FilterModel extends Model
{
    abstract public function query(): ActiveQuery;

    public function getDefaultSearchAttributes()
    {
        return [
            'like' => function (ActiveQuery $query, string $attribute) {
                return !empty($this->{$attribute}) ? $query->andWhere(['like', $attribute, $this->{$attribute}]) : $query;
            },
            '=' => function (ActiveQuery $query, string $attribute) {
                return !empty($this->{$attribute}) ? $query->andWhere([$attribute => $this->{$attribute}]) : $query;
            },
        ];
    }

    public function getSearchAttributes()
    {
        return [

        ];
    }

    public function getGridColumns()
    {
        return [

        ];
    }

    public function getDataProvider(): ActiveDataProvider
    {
        $query = $this->query();
        foreach ($this->getSearchAttributes() as $attribute => $search) {
            if (is_string($search)) {
                if (!empty($this->getDefaultSearchAttributes()[$search])) {
                    $query = call_user_func($this->getDefaultSearchAttributes()[$search], $query, $attribute);
                } else {
                    throw new InvalidConfigException('Нет предустановленного метода фильтрации ' . $search .
                        ". Имеющиеся предустановленые методы " . implode(array_keys(
                            $this->getDefaultSearchAttributes()
                        ))
                    );
                }
            } else if (is_array($search)) {
                $query = !empty($this->{$attribute}) ? $query->andWhere($search) : $query;
            } else if ($search instanceof \Closure) {
                $query = call_user_func($search, $query);
            } else {
                throw new NotFoundHttpException('Неправильный атрибут фильтрации ' . $search);
            }
        }
        return new ActiveDataProvider([
            'query' => $query
        ]);
    }

    public function getForm(ActiveForm $form, ActiveRecord $model): string
    {
        return '';
    }
}