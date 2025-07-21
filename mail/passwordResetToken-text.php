<?php

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $user->password_reset_token]);
?>
Saludos <?= $user->username ?>,
Acceda al siguiente link para cambiar su clave:
<?= $resetLink ?>