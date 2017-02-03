Yii2 RBAC Extension
===================
Yii2 RBAC Extension

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist mwsys/yii2-mwrbac "*"
```

or add

```
"mwsys/yii2-mwrbac": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Up to this moment, this extension  only provide actionfilter to handle regex-based access rights

Add parameter to the parameters, this config indicate that filter will add route otomatically as auth item. 

'add_auth_item' => true,

Add behavior in configuration. 


....
'as checkuser' => [
    'class' => 'mwsys\mwrbac\components\MwAccessControl',
    'allowActions' => [
            'site/*',
    ],
    
    
...
    
    
