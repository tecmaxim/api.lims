
<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\models\Crop;
?>

<div class="navbar">
    <div class="top-navbar">
        <span>Menu</span>
        <?= Html::a('Logout', ['site/logout']) ?>
        <?php /* // Html::beginForm(['site/logout', 'id' => 'form-logut'], 'post', ['enctype' => 'multipart/form-data']) ?>
          <?= Html::submitButton('Logout', ['class' => 'btn btn-primary', 'name' => 'logout-button']) ?>
          <?= Html::endForm() */ ?>
    </div>
   
    <?php if (Yii::$app->user->getIdentity()->ItemName !== 'breeder'): ?>
        <ul class="links-navbar">
            <?php if(Yii::$app->user->getIdentity()->itemName != 'breeder'): ?>
                <?php $class_news = Yii::$app->user->getIdentity()->ViewNews == 0 ? "news":"" ?>    
                <li class="<?= $class_news?>"><?= Html::a('What´s New', ['plate-history-by-project/whats-new']) ?> </li>
            <?php endif; ?>
            <li><?= Html::a('Jobs', ['project/index']) ?> </li>
            <li> <?= Html::a('Dashboard', ['site/index']) ?></li>
                        
            <li> <?= Html::a('Cancellation Causes', ['cancelcauses/index']) ?></li>
            <li> <?= Html::a('City', ['city/index']) ?></li>
            <li> <?= Html::a('Country', ['country/index']) ?></li>
            <li> <?= Html::a('Generation', ['generation/index']) ?></li>
            <li> <?= Html::a('Plates', ['plate/index']) ?></li>
            <li> <?= Html::a('Plate fail reason', ['causebydiscartedplates/index']) ?></li>
            <li> <?= Html::a('Research Station', ['researchstation/index']) ?></li>
            <li> <?= Html::a('Result Protocols', ['protocolresult/index']) ?></li>
            <li> <?= Html::a('Traits', ['traits/index']) ?></li>
            <li> <?= Html::a('Plates Reception', ['plate/receive-plates']) ?></li>
        </ul>

        <?php if (Yii::$app->user->getIdentity()->ItemName == 'admin'): ?>
            <?= Html::a('User settings', ['user/index'], ["class" => "config-user"]) ?>
        <?php endif; ?>
    <?php else: ?> 
        <ul class="links-navbar">
            <li> <?= Html::a('Dashboard', ['site/index']) ?></li>
            <li> <?= Html::a('Jobs', ['project/index']) ?></li> 
        </ul>
    <?php endif; ?>


</div>
<header class="navbar-fixed-top">
    <div class="container full-width">
        <div class="row">
            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                <a href="<?= Yii::$app->urlManager->baseUrl ?>"><img src="<?= Yii::$app->urlManager->baseUrl ?>/images/logo.png" alt="" class="logo"></a>
                <a href="<?= Yii::$app->urlManager->baseUrl ?>"><img src="<?= Yii::$app->urlManager->baseUrl ?>/images/logo-mobile.png" alt="" class="logo-mobile"></a>
            </div>
            <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9 header-right">
                <div class="notifications without hide">9
                    <img src="<?= Yii::$app->urlManager->baseUrl ?>/images/ico-alarm-not.png" alt="">
                </div>

                <?php if (isset(Yii::$app->session['cropId'])): ?>
                    <div id="title_crop" class="notifications notifications-crop">
                        <?php //if(($name = Crop::findOne(Yii::$app->session['cropId'])) != null )
                        echo "Crop: <b><i>" . Crop::findOne(Yii::$app->session['cropId'])->Name . "</b></i>";
                        ?>
                    </div>
<?php endif; ?>


                <?php
                if (!Yii::$app->user->isGuest)
                    echo Html::button(Yii::$app->user->identity->Username, ['class' => 'user',
                        'data-toggle' => 'modal',
                        'data-target' => '#modal',
                        'data-url' => Url::to(['user/update', 'id' => Yii::$app->user->id]),
                    ]);
                ?>

                <a href="javascript:;" class="menu">
                    <img src="<?= Yii::$app->urlManager->baseUrl ?>/images/ico-menu.png" alt="">
                </a>
            </div>
        </div>
    </div>
    <!--<div class="notifications-panel">
            <p>Fusce consectetur ante non est pellentesque feugiat erat nisi.</p>
            <p>Vestibulum erat nisornare in suscipit eu</p>
            <p>Proin quis aliquet purusdapi bus consequat quam, id blandit purus blandit at.</p>
            <p>Fusce consectetur ante non est pellentesque feugiat erat nisi.</p>
    </div> -->

</header>
