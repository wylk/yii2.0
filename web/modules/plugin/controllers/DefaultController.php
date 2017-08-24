<?php

namespace app\web\modules\plugin\controllers;

use yii\web\Controller;

/**
 * Default controller for the `plugin` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
    	$test = \YII::$app->getModule('stores');
        $test->runAction('default/index');
       // return $this->render('index');
    }
}
