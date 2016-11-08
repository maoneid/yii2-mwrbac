<?php 

namespace mw\mwrbac\components;

use Yii;
use yii\base\Action;
use yii\base\ActionFilter;
use yii\di\Instance;
use yii\web\User;
use yii\web\ForbiddenHttpException;
use yii\rbac\DbManager;
use yii\web\UrlManager;


class MwAccessControl extends ActionFilter
{
    public $user = "user";
    public $userId = null;
    public $denyCallback;
    public $allowedActions = [];
    public $rules = [];

    private $_authManager;
    private $_urlManager;

    public function init()
    {
        parent::init();
        $this->user = Instance::ensure($this->user, User::className());
        $this->userId = $this->user->getId();

        //load roles from auth manager 
        $this->_authManager = Instance::ensure("authManager", DbManager::className());
        $this->_urlManager = Instance::ensure("urlManager", UrlManager::className());
        //$data = $this->_authManager->getPermissionsByUser($this->userId);

        // print_r($data);
        // exit;
    }


    public function beforeAction($action)
    {
        $user = $this->user;
        $request = Yii::$app->getRequest();
        $route = "";
        echo Yii::$app->controller->getRoute();
        echo "<br>";
        $data = $this->_urlManager->parseRequest($request);
        if (isset($data[0])){
            $route = "/".$data[0];
        }
        echo $route;
        if ($this->user->can($route)) {
            return true;
        }else{
            // return true;
            if ($action->id !== "error"){
                $this->denyAccess($user);   
            }else{
                return true;
            }
            
        }
        
        

        return false;

        // print_r($request);
        // /* @var $rule AccessRule */
        // foreach ($this->rules as $rule) {
        //     if ($allow = $rule->allows($action, $user, $request)) {
        //         return true;
        //     } elseif ($allow === false) {
        //         if (isset($rule->denyCallback)) {
        //             call_user_func($rule->denyCallback, $rule, $action);
        //         } elseif ($this->denyCallback !== null) {
        //             call_user_func($this->denyCallback, $rule, $action);
        //         } else {
        //             $this->denyAccess($user);
        //         }
        //         return false;
        //     }
        // }
        // if ($this->denyCallback !== null) {
        //     call_user_func($this->denyCallback, null, $action);
        // } else {
        //     $this->denyAccess($user);
        // }
        // return false;
    }


    protected function denyAccess($user)
    {

        if ($user->getIsGuest()) {
            $user->loginRequired();
        } else {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }
    }



}

?>