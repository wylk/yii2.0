<?php

namespace app\web\modules\plugin;

/**
 * plugin module definition class
 */
class Plugin extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\web\modules\plugin\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->modules = [
            'stores' => [
                'class' => 'app\addons\food\stores\Stores',
            ],
        ];

        // custom initialization code goes here
    }
}
