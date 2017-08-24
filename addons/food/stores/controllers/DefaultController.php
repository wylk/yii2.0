<?php

namespace app\addons\food\stores\controllers;

use yii\web\Controller;
use app\addons\food\stores\models\User;
use Yii;
/**
 * Default controller for the `stores` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public $layout = false;
    public function actionIndex()
    {
        $this->layout = "layout2";
        $model = new User();
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            if ($model->login($post)) {
                $this->redirect(['default/test']);
                Yii::$app->end();
            }
            //var_dump($_POST);die;
             //var_dump($this->username);
        }
    //$a = User::findOne(1);
       //var_dump($a);
        return $this->render('index',[
                'model' => $model
            ]);
    }

    public function actionTest()
    {
        echo 'test';
    }
}
