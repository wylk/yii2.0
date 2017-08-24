<?php

namespace app\web\modules\admin\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\web\modules\models\Menu;
use app\web\modules\models\LoginForm;
/**
 * Default controller for the `admin` module
 */
class DefaultController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['get'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $this->layout = "layout1";
        $user_id=Yii::$app->user->identity->getId();
        $user_info = Yii::$app->authManager->getRolesByUser($user_id);
        //var_dump($user_info);die;
        $menu = new Menu();
        $menu = $menu->getLeftMenuList();
        return $this->render('index',[
            'menu' => $menu,
            'user_info' => key($user_info)
        ]);
    }

     /**
     * 登录
     */
    public function actionLogin()
    {
        $this->layout = "layout1";
        if (!Yii::$app->user->isGuest) {
            echo 1;
            header('Location:?r=admin/default/index');die;
            //return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            $model->loginLog();
            echo 1;
            header('Location:?r=admin/default/index');die;
            //return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }
}
