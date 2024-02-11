<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Orden;

/**
 * OrdenSearch represents the model behind the search form of `app\models\Orden`.
 */
class OrdenSearch extends Orden
{
    public $paciente;
    public $doctor;
    public $_examenes;
    
   /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'pagado', 'cerrada', 'paciente_id', 'doctor_id', 'cotizacion_id', 'created_by', 'updated_by'], 'integer'],
            [['codigo', 'fecha', 'created_at', 'updated_at'], 'safe'],
            [['precio', 'abono'], 'number'],
            [['paciente', 'doctor'], 'safe'],
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
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Orden::find()->orderBy(['id'=>SORT_DESC]);

        $query->joinWith(['paciente']);
        
        $query->join('left outer join', 'user as doctor','doctor.id=orden.doctor_id');

        
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 10,],
        ]);
        
    
        $dataProvider->sort->attributes['paciente'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['user.nombres' => SORT_ASC],
            'desc' => ['user.nombres' => SORT_DESC],
        ];
        
        $dataProvider->sort->attributes['doctor'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['doctor.nombres' => SORT_ASC],
            'desc' => ['doctor.nombres' => SORT_DESC],
        ];
        
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'fecha' => $this->fecha,
            'precio' => $this->precio,
            'abono' => $this->abono,
            'pagado' => $this->pagado,
            'cerrada' => $this->cerrada,
            'paciente_id' => $this->paciente_id,
            'doctor_id' => $this->doctor_id,
            'cotizacion_id' => $this->cotizacion_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'orden.codigo', $this->codigo]);
        $query->andFilterWhere(['like', 'user.nombres', $this->paciente]);
        $query->andFilterWhere(['like', 'doctor.nombres', $this->doctor]);

        return $dataProvider;
    }
}
