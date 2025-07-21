<?php

use yii\helpers\Html;
use luya\bootstrap4\grid\GridView;

/* @var $this yii\web\View */
/* @var $GridModel app\models\AntibioticoGrid */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Antibióticos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="antibiotico-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_Grid', ['model' => $GridModel]); ?>

    <p>
        <?= Html::a('Nuevo Antibiótico', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $GridModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'descripcion',

            ['class' => 'yii\grid\ActionColumn','template' => '{Editar}',
                'buttons' => [
                    'Editar' => function ($url, $model) {
                    
                    return '<div class="btn-group" role="group">'.
                        Html::a('<span> <b class="mdi mdi-circle-edit-outline"></b></span> ',
                            \yii\helpers\Url::to(['update','id' => $model->id]),
                            ['class'=>'btn btn-primary','title' => Yii::t('yii', 'Editar'),'aria-label' => Yii::t('yii', 'Editar')]).
                            
                            Html::a('<span> <b class="mdi mdi-eye-outline"></b></span> ',
                                \yii\helpers\Url::to(['view','id' => $model->id]),
                                ['class'=>'btn btn-primary','title' => Yii::t('yii', 'Visualizar'),'aria-label' => Yii::t('yii', 'Visualizar')]).
                                
                                Html::a('<span> <b class="mdi mdi-trash-can-outline"></b></span> ',
                                    \yii\helpers\Url::to(['delete','id' => $model->id]),
                                    ['class'=>'btn btn-primary','title' => Yii::t('yii', 'Borrar'),'aria-label' => Yii::t('yii', 'Borrar'),
                                        'data-confirm' => Yii::t('yii', "Esta seguro de eliminar el antibiotico $model->descripcion?"),
                                        'data-method'  => 'post',
                                    ]).
                                    '</div>';
                    }
                    ]
                    
                    ]
        ],
    ]); ?>
</div>
