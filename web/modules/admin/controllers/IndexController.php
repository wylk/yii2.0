<?php

namespace app\web\modules\admin\controllers;
use Yii;
use yii\web\Controller;
use app\web\modules\models\Log;
//use backend\models\PasswordForm;
use yii\data\Pagination;
use app\web\modules\models\Menu;


/**
 * Default controller for the `admin` module
 */
class IndexController extends Controller
{
    public $layout = false;
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionWelcome()
    {
        $this->layout = "layout1";
        $query = Log::find();
        $count = $query->count();
        $pages = new Pagination(['totalCount' => $count,'pageSize' => '10']);
        $log = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->orderBy('id desc')
            ->all();

        $sql = "SELECT *,FROM_UNIXTIME(create_time,'%Y-%m') as period,COUNT(*) as times FROM log GROUP BY period LIMIT 12";
        $History = Yii::$app->db->createCommand($sql)->queryAll();
        $HistoryMonthStr = '';
        $HistoryMonthNum = '';
        foreach($History as $val){
            $HistoryMonthStr .= "'".$val['period']."',";
            $HistoryMonthNum .= $val['times'].",";
        }
        $HistoryMonthStr = substr($HistoryMonthStr,0,-1);
        $HistoryMonthNum = substr($HistoryMonthNum,0,-1);
        return $this->render('welcome',[
            'log' => $log,
            'pages' => $pages,
            'HistoryMonthStr' => $HistoryMonthStr,
            'HistoryMonthNum' => $HistoryMonthNum,
        ]);
    }
    //菜单列表
    public function actionMenu()
    {
        $this->layout = "layout1";
        $menu = new Menu();
        $menu = $menu->getMenuList();
        return $this->render('menu', [
            'menu' => $menu
        ]);
    }

    public function actionDelete()
    {
        $id = Yii::$app->request->get();
        $menu = new Menu();
        $menu->findOne($id)->delete();
        return $this->redirect(['index/menu']);
    }

    public function actionCreate()
    {
        echo 1;

    }
}