<?php 

namespace mw\mwrbac\components;

use Yii;
use yii\base\Action;
use yii\base\ActionFilter;
use yii\di\Instance;
use yii\web\User;
use yii\web\ForbiddenHttpException;



class MwAccessControl extends ActionFilter
{
	public $user;
	public $denyCallback;

	public function init()
    {
        parent::init();
        $this->user = Instance::ensure($this->user, User::className());
        print_r($this->user);
        // foreach ($this->rules as $i => $rule) {
        //     if (is_array($rule)) {
        //         $this->rules[$i] = Yii::createObject(array_merge($this->ruleConfig, $rule));
        //     }
        // }
    }
}

?>