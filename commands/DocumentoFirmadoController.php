<?php

namespace app\commands;

use app\models\Orden;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\db\Expression;


class DocumentoFirmadoController extends Controller
{

    public function actionBorrar($days=90)
    {
        $ordenes = Orden::find()
            ->where(['firmado_digitalmente'=>true])
            ->andWhere( new Expression("DATEDIFF ( now(),orden.fecha_firmado_digital) >= $days"))
            ->orderBy(['id'=>SORT_DESC])
            ->limit(500)
            ->all();

        foreach ( $ordenes  as $orden){
           $orden->borrarDocumentoFirmado();
        }

        return ExitCode::OK;
    }
}
