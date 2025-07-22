<?php

namespace app\modules\lab\grids;

use app\modules\lab\bussines\OrdenBussines;
use app\modules\lab\models\Orden;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * UserGrid represents the model behind the Grid form of `app\models\User`.
 */
class ClienteResultadoGrid extends OrdenBussines
{
    
    public $paciente;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fecha'], 'safe'],
            [['valor_total'], 'number'],
            [['codigo'], 'string', 'max' => 10],
            [['paciente',], 'safe'],
          
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with Grid query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function Grid($params)
    {
        $query = OrdenBussines::find();
        
        $query->joinWith(['paciente']);
        
        $query->where(['paciente_id'=> Yii::$app->user->identity->id ]);
        $query->andWhere([ 'pagado'=> true]);
        $query->andWhere(['cerrada'=>true]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder'=>['fecha'=> SORT_DESC ]],
            'pagination' => [
                'pageSize' => 5,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        
        $dataProvider->sort->attributes['paciente'] = [          
            'asc' => ['user.nombres' => SORT_ASC],
            'desc' => ['user.nombres' => SORT_DESC],
        ];
        
        $dataProvider->sort->attributes['codigo'] = [
            'asc' => ['orden.codigo' => SORT_ASC],
            'desc' => ['orden.codigo' => SORT_DESC],
        ];

        // grid filtering conditions
        $query->andFilterWhere(['ilike', 'user.nombres', $this->paciente]);
   
        $query->andFilterWhere(['ilike', 'orden.codigo', $this->codigo])
            ->andFilterWhere(['ilike', 'valor_total', $this->valor_total])
            ->andFilterWhere(['ilike', 'fecha', $this->fecha]);

        return $dataProvider;
    }
}
