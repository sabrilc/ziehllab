<?php

namespace app\modules\lab\grids;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\lab\models\Antibiotico;

/**
 * AntibioticoGrid represents the model behind the Grid form of `app\models\Antibiotico`.
 */
class AntibioticoGrid extends Antibiotico
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
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
    public function Grid($params)
    {
        $query = Antibiotico::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
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
        ]);

        $query->andFilterWhere(['like', 'descripcion', $this->descripcion]);

        return $dataProvider;
    }
}
