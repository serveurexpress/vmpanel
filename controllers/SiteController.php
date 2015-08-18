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
                    switch ($action) {
                        case 'start':
                            \Yii::$app->getSession()->setFlash('success', 'VM lancée');
                            break;
                        case 'pause':
                            \Yii::$app->getSession()->setFlash('success', 'VM en pause');
                            break;
                        case 'stop':
                            \Yii::$app->getSession()->setFlash('success', 'VM arrêtée');
                            break;
                        case 'restart':
                            \Yii::$app->getSession()->setFlash('success', 'VM relancée');
                            break;
                        case 'fsck':
                            \Yii::$app->getSession()->setFlash('success', 'FSCK en cours');
                            break;
                        default:
                            \Yii::$app->getSession()->setFlash('error', 'Action interdite !');
                    }
                } else {
                    \Yii::$app->getSession()->setFlash('error', 'Cette VM n\'est pas à vous !');
                }
            }
            return $this->render('index', [
                        'vmlist' => $vmlist,
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
