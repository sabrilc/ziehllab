<?php

namespace app\modules\lab\models;

use Yii;

/**
 * This is the model class for table "examen_germen".
 *
 * @property int $id
 * @property int $examen_id
 * @property int $germen_id
 * @property string $contaje_colonia
 *
 * @property Examen $examen
 * @property Germen $germen
 * @property ExamenGermenAntibiotico[] $examenGermenAntibioticos
 */
class ExamenGermen extends \yii\db\ActiveRecord
{
    
   public $_id;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'examen_germen';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['examen_id', 'germen_id','_id'], 'integer'],
            [['contaje_colonia'], 'string', 'max' => 50],
            [['examen_id'], 'exist', 'skipOnError' => true, 'targetClass' => Examen::class, 'targetAttribute' => ['examen_id' => 'id']],
            [['germen_id'], 'exist', 'skipOnError' => true, 'targetClass' => Germen::class, 'targetAttribute' => ['germen_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'examen_id' => 'Examen',
            'germen_id' => 'Germen',
            'contaje_colonia' => 'Contaje Bacteriano [UFC/ML]',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExamen()
    {
        return $this->hasOne(Examen::class, ['id' => 'examen_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGermen()
    {
        return $this->hasOne(Germen::class, ['id' => 'germen_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExamenGermenAntibioticos()
    {
        return $this->hasMany(ExamenGermenAntibiotico::class, ['examen_germen_id' => 'id']);
    }
    
    public function verPruebaSensibilidadExamenGermen(){
        
        $html = "<table class='table'>
                  <thead>
                    <tr>
                      <th scope='col'>#</th>
                      <th scope='col'>Antibiotico</th>
                      <th scope='col'>INHIBICION EN [MM]</th>
                      <th scope='col'>DIAMETRO DE ZONA</th>
                    </tr>
                  </thead>
                  <tbody>";
               
            foreach ( Antibiotico::find()->orderBy(['descripcion'=> SORT_ASC])->all() as $index => $antibiotico) {
                $examenGermenAntibiotico = ExamenGermenAntibiotico::find()->where(
                    [ 'examen_germen_id' => $this->id,
                        'antibiotico_id' => $antibiotico->id ])->One();
                    
                    
                    if(isset($examenGermenAntibiotico)){
                        $html .="<tr>
                   <th scope='row'>". ($index+1) ." </th>
                   <td>$antibiotico->descripcion</td>
                   <td> <label class='text-monospace'>".$examenGermenAntibiotico->valor."</label> </td>
                   <td>";
                        if($examenGermenAntibiotico->tipo == 'RESISTENTE'){                            
                            $html.="<label class='badge badge-info'>RESISTENTE</label>";
                        }
                        else{
                            $html.="<label class='badge badge-secondary'>SENSIBLE</label>";
                        }
                     $html .="</td>
                   </tr>";
                        
                        
                    }
                
                    
                    
            }
        
        $html.="  </tbody>
                </table>";
        
        return $html;
    }
    
}
