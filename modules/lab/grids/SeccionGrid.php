<?php

namespace app\modules\lab\grids;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\lab\models\Seccion;

/**
 * SeccionGrid represents the model behind the Grid form of `app\models\Seccion`.
 */
class SeccionGrid extends Seccion
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'analisis_id'], 'integer'],
            [['descripcion'], 'safe'],
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
        $query = Seccion::find()->where(['analisis_id'=>$analisis_id]);

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
            'analisis_id' => $this->analisis_id,
        ]);

        $query->andFilterWhere(['like', 'descripcion', $this->descripcion]);

        return $dataProvider;
    }
}
