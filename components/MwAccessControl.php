<?php

namespace mwsys\mwrbac\components;

use Yii;
use yii\base\Action;
use yii\base\ActionFilter;
use yii\di\Instance;
use yii\web\User;
use yii\web\ForbiddenHttpException;
use yii\rbac\DbManager;
use yii\web\UrlManager;
use yii\rbac\Item;

class MwAccessControl extends ActionFilter {

    public $user = "user";
    public $userId = null;
    public $denyCallback;
    public $allowedActions = [];
    public $rules = [];
    private $_authManager;
    private $_urlManager;

    public function init() {
        parent::init();
        $this->user = Instance::ensure($this->user, User::className());
        $this->userId = $this->user->getId();

        //load roles from auth manager 
        //$this->_authManager = Instance::ensure("authManager", DbManager::className());
        //$this->_urlManager = Instance::ensure("urlManager", UrlManager::className());
        $this->_authManager = Yii::$app->authManager;
        $this->_urlManager = Yii::$app->urlManager;
    }

    public function beforeAction($action) {
        
        
        $user = Yii::$app->user;
        $authitem = $action->uniqueId;
        $add_item = Yii::$app->params["add_auth_item"];
        
        if ($add_item) {
            try {
                $newPermission = $this->_authManager->createPermission($authitem);
                $this->_authManager->add($newPermission);
            } catch (\Exception $ex) {
                Yii::error($ex->getMessage());
            }
        }
        
        if (is_array($this->allowedActions)){
            if (in_array($authitem, $this->allowedActions)){
                return true;
            }
        }

        if ($this->user->can($authitem)) {
            return true;
        } else {
            if ($action->id !== "error") {
                $this->denyAccess($user);
            } else {
                return true;
            }
        }
        return false;
    }

    protected function denyAccess($user) {

        if ($user->getIsGuest()) {
            $user->loginRequired();
        } else {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }
    }
    
    
    protected function isActive($action){
        
        $actionUniqueId = $action->uniqueId;
        $allAction = preg_replace(array("/\/\w+[\s\S]\w+$/"), array("/*"), $actionUniqueId);
        //$allModule = preg_replace(array("/\/\w+[\s\S]\w+\/\*$/"), array("/*"), $allAction);
        //update regex for all modules \/[\s\S][a-z]+\w-.+\/\*$
        $allModule = preg_replace(array('/\/[\s\S][a-z]+\w-.+\/\*$/'), array("/*"), $allAction);
        
        //for all module 
        if (in_array($allModule, $this->allowedActions)){
            return false;
        }
        
        //for all action
        if (in_array($allAction, $this->allowedActions)){
            return false;
        }
        
        
        return true;
        
    }

}

?>
