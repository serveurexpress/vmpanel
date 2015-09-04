<?php

namespace app\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;

class se extends Component {

    public function checkRemoteFile($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        // don't download content
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if (curl_exec($ch) !== FALSE) {
            return true;
        } else {
            return false;
        }
    }

    public function getStatus($vm) {
        return trim(shell_exec('sudo ' . Yii::$app->params['scriptDir'] . Yii::$app->params['scriptStatus'] . ' ' . $vm));
    }

    public function getLog($vm) {
        if (file_exists(Yii::$app->params['logDir'] . '/' . $vm . '.last')) {
            return file_get_contents(Yii::$app->params['logDir'] . '/' . $vm . '.last');
        } else {
            return '';
        }
    }

    public function getErr($vm) {
        if (file_exists(Yii::$app->params['logDir'] . '/' . $vm . '.err')) {
            return file_get_contents(Yii::$app->params['logDir'] . '/' . $vm . '.err');
        } else {
            return '';
        }
    }
    
    public function getAction($vm) {
            return glob(Yii::$app->params['actionDir'] . $vm . '-' . Yii::$app->params['hosterName'] . '-*');
    }
    
    public function isLive($vm) {
            if (count(glob(Yii::$app->params['liveDir'] . $vm . '-*')) > 0) {
                return true;
            } else {
                return false;
            }
    }
    
    public function isNet($vm) {
            if (count(glob(Yii::$app->params['netDir'] . $vm . '-*')) > 0) {
                return true;
            } else {
                return false;
            }
    }
    
    public function isMount($vm) {
            if (count(glob(Yii::$app->params['mountDir'] . $vm . '-*')) > 0) {
                return true;
            } else {
                return false;
            }
    }

}
