<?php

use kartik\icons\Icon;
use kartik\widgets\SwitchInput;

$this->title = 'VMpanel';
?>
<div class="site-index">
    <div class="body-content">
        <?php
        $result = "";
        foreach ($vmlist as &$vm) {
            $startStatus = '';
            $stopStatus = '';
            $status = trim(shell_exec('sudo ' . Yii::$app->params['scriptDir'] . Yii::$app->params['scriptStatus'] . ' ' . $vm));
            if ($status == '1') {
                $status = true;
                $startStatus = 'disabled';
            } else {
                $status = false;
                $stopStatus = 'disabled';
            }
            $actionMenu = '<a href="/index.php?vm=' . $vm . '&action=start" class="btn btn-default" title="Démarrer" ' . $startStatus . '>' . Icon::show('play') . '</a>'
                    . '<a href="/index.php?vm=' . $vm . '&action=stop" class="btn btn-default" title="Arrêter" ' . $stopStatus . '>' . Icon::show('stop') . '</a>'
                    . '<a href="/index.php?vm=' . $vm . '&action=restart" class="btn btn-default" title="Relancer" ' . $stopStatus . '>' . Icon::show('refresh') . '</a>'
                    . '<a href="/index.php?vm=' . $vm . '&action=fsck" class="btn btn-default" title="Fsck" ' . $startStatus . '>' . Icon::show('search') . '</a>';
            if ($action != null) {
                if (file_exists(Yii::$app->params['actionDir'] . $vm . '-' . Yii::$app->params['hosterName'] . '-' . $action)) {
                    $actionMenu = '';
                }
            }
            $result = '
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><label class="control-label">' . ucfirst($vm) . '</label> ' . SwitchInput::widget([
                        'name' => 'status_1',
                        'value' => $status,
                        'disabled' => true,
                        'pluginOptions' => [
                            'size' => 'small',
                            'onColor' => 'success',
                            'offColor' => 'danger',
                        ]
                    ]) . '</h3>
                    </div>
                    <div class="panel-body">
                        Les graphs ici
                    </div>
                    <div class="panel-footer">
                       ' . $actionMenu . '
                    </div>
                </div>';
            echo $result;
        }
        ?>
    </div>
</div>
