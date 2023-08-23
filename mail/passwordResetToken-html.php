<?php
use yii\helpers\Html;

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $user->password_reset_token]);
?>
 
<div class="password-reset">
    <p>Saludos <?= Html::encode($user->username) ?>,</p>
    <p>Acceda al siguinte link para cambiar su clave</p>
    <p><?= Html::a(Html::encode($resetLink), $resetLink) ?></p>
</div>