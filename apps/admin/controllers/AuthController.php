<?php
namespace admin\controllers;

use admin\models\AdminAuth;
use Yii;
use yii\base\InlineAction;
use yii\data\ActiveDataProvider;
use yii\helpers\Inflector;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;

/**
 * 权限管理类
 *
 * @auth_key    auth
 * @auth_name   权限管理
 */
class AuthController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => $this->getRules('auth'),
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * 权限列表
     *
     * @auth_key    *
     * @auth_parent auth
     *
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => AdminAuth::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 更新权限缓存
     *
     * @auth_key    auth_view
     * @auth_name   查看权限
     * @auth_parent auth
     *
     * @param $id
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * 更新权限缓存
     *
     * @auth_key    auth_cache
     * @auth_name   缓存权限
     * @auth_parent auth
     *
     * @return \yii\web\Response
     * @throws \ReflectionException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCache()
    {
        $datas = [];
        $controllerPath = Yii::$app->getControllerPath();
        $controllers = $this->getControllers($controllerPath);
        foreach ($controllers as $controllerId) {
            if(!$controllerId) continue;

            $result = Yii::$app->createController($controllerId);

            if ($result !== false) {
                list($controller, $actionID) = $result;
                /** @var Controller $controller */
                $auth = $this->getAuth(new \ReflectionClass($controller));
                if($auth){
                    $datas[$controllerId] = $auth;
                }

                $actions = $this->getActions($controller);
                foreach($actions as $actionID){
                    $action = $controller->createAction($actionID);

                    if($action instanceof InlineAction){
                        $action = new \ReflectionMethod($controller, $action->actionMethod);
                    }else{
                        $action = new \ReflectionMethod($action, 'run');
                    }

                    $auth = $this->getAuth($action);
                    if($auth){
                        $actionID = str_replace("*", $actionID, $controllerId);
                        $datas[$actionID] = $auth;
                    }
                }
            }
        }

        AdminAuth::deleteAll(1);

        $forAll = [];
        $forGroupAll = [];
        foreach($datas as $route => $data){
            if($data['key'] == '*'){
                if(isset($data['group'])){
                    $forGroupAll[$data['group']][] = $route;
                }else{
                    $forAll[$data['parent']][] = $route;
                }
            }
        }

        foreach($datas as $route => $data){
            if($data['key'] == '*'){
                continue;
            }
            $model = AdminAuth::findOne($data['key']);
            if(!$model){
                $model = new AdminAuth();
                $model->setAttributes($data);
            }
            $model->addRoute($route);
            if(isset($forAll[$model->parent])){
                $model->addRoutes($forAll[$model->parent]);
            }

            if(isset($data['group']) && isset($forGroupAll[$data['group']])){
                $model->addRoutes($forGroupAll[$data['group']]);
            }

            $model->save();
        }

        return $this->redirect(['index']);
    }

    private function getControllers($folder, $prefix = '', $controllers = [])
    {
        if(is_dir($folder)){
            $files = scandir($folder);
            foreach($files as $file){
                if(empty($file) || $file == '.' || $file == '..'){
                    continue;
                }

                if(is_dir($folder . '/' . $file)){
                    $prefix .= $file . '/';
                    $controllers = $this->getControllers($folder . '/' . $file, $prefix, $controllers);
                }

                if(substr_compare($file, 'Controller.php', -14, 14) === 0){
                    $controllerClass = Yii::$app->controllerNamespace . '\\' . substr(basename($file), 0, -4);
                    if($prefix){
                        $controllerClass = Yii::$app->controllerNamespace . '\\' . str_replace('/', '\\', $prefix) . substr(basename($file), 0, -4);
                    }
                    if($this->validateControllerClass($controllerClass)){
                        $controllers[] = $prefix . Inflector::camel2id(substr(basename($file), 0, -14)) . '/*';
                    }
                }
            }
        }

        return $controllers;
    }

    private function getActions($controller)
    {
        $actions = array_keys($controller->actions());
        $class = new \ReflectionClass($controller);
        foreach ($class->getMethods() as $method) {
            $name = $method->getName();
            if ($name !== 'actions' && $method->isPublic() && !$method->isStatic() && strpos($name, 'action') === 0) {
                $actions[] = Inflector::camel2id(substr($name, 6), '-', true);
            }
        }
        sort($actions);

        return array_unique($actions);
    }

    private function getAuth($reflection)
    {
        $patten_key = '/@auth_key (.*)/';
        $patten_name = '/@auth_name (.*)/';
        $patten_parent = '/@auth_parent (.*)/';
        $patten_group = '/@auth_group (.*)/';

        $comment = $reflection->getDocComment();

        $auth = [];

        preg_match($patten_key, $comment, $match);
        if(isset($match[1])){
            $auth['key'] = trim($match[1]);
        }

        preg_match($patten_name, $comment, $match);
        if(isset($match[1])){
            $auth['name'] = trim($match[1]);
        }

        preg_match($patten_parent, $comment, $match);
        if(isset($match[1])){
            $auth['parent'] = trim($match[1]);
        }

        preg_match($patten_group, $comment, $match);
        if(isset($match[1])){
            $auth['group'] = trim($match[1]);
        }

        return $auth;
    }

    private function validateControllerClass($controllerClass)
    {
        if (class_exists($controllerClass)) {
            $class = new \ReflectionClass($controllerClass);
            return !$class->isAbstract();
        } else {
            return false;
        }
    }

    /**
     * 通用模型查询
     *
     * @param $id
     *
     * @return \admin\models\AdminAuth
     * @throws \yii\web\NotFoundHttpException
     */
    protected function findModel($id)
    {
        if(($model = AdminAuth::findOne($id)) !== NULL){
            return $model;
        }else{
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
