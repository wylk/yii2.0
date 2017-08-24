<?php

namespace app\web\modules\admin\modules\test\controllers;

use yii\web\Controller;

/**
 * Default controller for the `test` module
 */
class DefaultController extends Controller
{
    public $layout = false;
    public function actionIndex()
    {
    	echo 'test24rtr';
        return $this->render('index');
    }

    public function actionShop()
    {
    	echo 'shop';
    }

}
