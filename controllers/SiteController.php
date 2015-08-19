<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller {

    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex($vm = null, $action = null) {
        if (!\Yii::$app->user->isGuest) {
            $vmlist = Yii::$app->user->identity->vmList;

            if ($vm != null && $action != null && $vm != "" && $action != "" && isset($vm) && isset($action)) {
                if (in_array($vm, $vmlist)) {
                    $output = '';
                    switch ($action) {
                        case 'start':
                            shell_exec('echo sudo ' . Yii::$app->params['scriptDir'] . '/' . Yii::$app->params['scriptStart'] . ' ' . $vm.' | sudo at now');
                            \Yii::$app->getSession()->setFlash('success', ucfirst($vm) . ' : Démarrage de la VM en cours');
                            break;
                        case 'stop':
                            shell_exec('echo sudo ' . Yii::$app->params['scriptDir'] . '/' . Yii::$app->params['scriptStop'] . ' ' . $vm.' | sudo at now');
                            \Yii::$app->getSession()->setFlash('success', ucfirst($vm) . ' : Arret de la VM en cours');
                            break;
                        case 'restart':
                            shell_exec('echo sudo ' . Yii::$app->params['scriptDir'] . '/' . Yii::$app->params['scriptStop'] . ' ' . $vm.' && echo sudo ' . Yii::$app->params['scriptDir'] . '/' . Yii::$app->params['scriptStart'] . ' ' . $vm.' | sudo at now');
                            \Yii::$app->getSession()->setFlash('success', ucfirst($vm) . ' : Redémarrage de la VM en cours');
                            break;
                        case 'fsck':
                            shell_exec('echo sudo ' . Yii::$app->params['scriptDir'] . '/' . Yii::$app->params['scriptFsck'] . ' ' . $vm.' | sudo at now');
                            \Yii::$app->getSession()->setFlash('success', ucfirst($vm) . ' : Fsck de la VM en cours');
                            break;
                        default:
                            \Yii::$app->getSession()->setFlash('error', 'Action interdite !');
                    }
                } else {
                    \Yii::$app->getSession()->setFlash('error', 'Cette VM n\'est pas à vous !');
                }
                return $this->render('index', [
                            'vmlist' => $vmlist,
                            'action' => $action,
                ]);
            }
            return $this->render('index', [
                        'vmlist' => $vmlist,
                        'action' => null,
            ]);
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
                    'model' => $model,
        ]);
    }

    public function actionLogin() {
        if (!\Yii::$app->user->isGuest) {
            return $this->render('index');
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
                    'model' => $model,
        ]);
    }

    public function actionLogout() {
        Yii::$app->user->logout();

        return $this->goHome();
    }

}
