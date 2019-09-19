<?php

namespace ofixone\content\assets;

use dmstr\web\AdminLteAsset;
use uran1980\yii\assets\TextareaAutosizeAsset;
use yii\web\AssetBundle;

class ModuleAsset extends AssetBundle
{
    public function init()
    {
        parent::init();
        $this->sourcePath = __DIR__ . "/src";
    }

    public $css = [
        'app.css'
    ];

    public $depends = [
        AdminLteAsset::class,
        TextareaAutosizeAsset::class
    ];
}