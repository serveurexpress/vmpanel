<?php
/* @var $this yii\web\View */

$this->title = 'VMpanel';
?>
<div class="site-index">
    <div class="body-content">
        <?php
        $result = "";
        foreach ($vmlist as &$vm) {
            $result = '
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">'.$vm.'</h3>
                    </div>
                    <div class="panel-body">
                        Panel content
                    </div>
                </div>';
            echo $result;
        }
        ?>
    </div>
</div>
