<?php
namespace app\modules\lab\grids;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Parametro;

/**
 * ParametroGrid represents the model behind the Grid form of `app\models\Parametro`.
 */
class ParametroGrid extends Parametro
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id','orden_impresion', 'metodo_id', 'medida_id', 'analisis_id', 'created_by', 'updated_by'], 'integer'],
            [['descripcion', 'valores_posibles', 'created_at', 'updated_at'], 'safe'],
            [['hombre_valo_de_referencia_min', 'hombre_valo_de_referencia_max', 'mujer_valo_de_referencia_max', 'mujer_valo_de_referencia_min', 'ninio_valo_de_referencia_max', 'ninio_valo_de_referencia_min'], 'number'],
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
    public function Grid($params,$analisis_id)
    {
        $query = Parametro::find()->where(['analisis_id'=>$analisis_id]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'hombre_valo_de_referencia_min' => $this->hombre_valo_de_referencia_min,
            'hombre_valo_de_referencia_max' => $this->hombre_valo_de_referencia_max,
            'mujer_valo_de_referencia_max' => $this->mujer_valo_de_referencia_max,
            'mujer_valo_de_referencia_min' => $this->mujer_valo_de_referencia_min,
            'ninio_valo_de_referencia_max' => $this->ninio_valo_de_referencia_max,
            'ninio_valo_de_referencia_min' => $this->ninio_valo_de_referencia_min,
            'metodo_id' => $this->metodo_id,
            'medida_id' => $this->medida_id,
            'analisis_id' => $this->analisis_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'orden_impresion' => $this->orden_impresion,
        ]);

        $query->andFilterWhere(['like', 'descripcion', $this->descripcion])
            ->andFilterWhere(['like', 'valores_posibles', $this->valores_posibles]);

        return $dataProvider;
    }
}
