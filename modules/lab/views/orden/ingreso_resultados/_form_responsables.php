<?php
?>
<div class='row mt-3 animated fadeIn'>
    <div class='col-xs-12 col-sm-12'>
        <div class='panel panel-primary'>
            <div class='panel-heading'>Responsables</div>
            <div class='panel-body'
            <div class='row'>
                <?php use app\modules\lab\models\Laboratorista;
                use app\modules\site\models\User;
                use kartik\select2\Select2;
                use yii\helpers\ArrayHelper;
                use yii\helpers\Html;
                use yii\widgets\ActiveForm;

                $form = ActiveForm::begin(['options' => [
                    'class' => 'animated fadeIn',
                    'id' => 'formResponsablesOrden',
                    'onsubmit' => 'return guardarResponsablesOrden()',
                ]]) ?>


                <?= Html::activeHiddenInput($model,'_id') ?>
                <div class="col-md-6">
                    <?=$form->field($model, 'doctor_id')->dropDownList( ArrayHelper::map(
                        User::find()->alias('u')
                            ->innerJoin('auth_assignment','u.id=auth_assignment.user_id')
                            ->where(['item_name'=>'medico'])
                            ->all(), 'id', 'nombreCompleto'),
                            [
                            'prompt' => 'Seleccionar ...',
                            'multiple' => false,
                            'onChange'=>'guardarResponsablesOrden()',
                            'class'=>'form-control select2me',
                        ]
                    )
                    ?>
                </div>
                <div class="col-md-6">
                    <?=$form->field($model, 'laboratorista_id')->dropDownList( ArrayHelper::map(
                        Laboratorista::find()
                            ->where(['dbremove'=>false])
                            ->all(), 'id', 'nombres'),
                        [
                            'prompt' => 'Seleccionar ...',
                            'multiple' => false,
                            'onChange'=>'guardarResponsablesOrden()',
                            'class'=>'form-control select2me',
                        ]
                    )
                    ?>
                </div>

                <div class="col-md-6">
                    <?=$form->field($model, 'responsable_tecnico_id')->dropDownList( ArrayHelper::map(
                        Laboratorista::find()
                            ->where(['dbremove'=>false])
                            ->andWhere(['responsable_tecnico'=>1])
                            ->all(), 'id', 'nombres'),
                        [
                            'prompt' => 'Seleccionar ...',
                            'multiple' => false,
                            'onChange'=>'guardarResponsablesOrden()',
                            'class'=>'form-control select2me',
                        ]
                    )
                    ?>
                </div>

                <?php ActiveForm::end() ?>
            </div>

        </div>
    </div>
</div>