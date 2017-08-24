<?php

namespace app\web\modules\admin;

/**
 * admin module definition class
 */
class Admin extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\web\modules\admin\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->modules = [
            'test' => [
                'class' => 'app\web\modules\admin\modules\test\Test',
            ],
            'weixin' => [
                'class' => 'app\addons\weixin\Weixin',
            ],
        ];

        // custom initialization code goes here
    }
}
