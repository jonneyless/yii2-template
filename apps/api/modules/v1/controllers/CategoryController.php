<?php

namespace api\modules\v1\controllers;

use api\controllers\ApiController;
use api\models\Category;
use api\models\Goods;
use api\models\StoreCategory;
use Yii;

class CategoryController extends ApiController
{

    public $modelClass = 'api\models\Category';

    public function actionIndex()
    {
        $parent_id = Yii::$app->request->getQueryParam('parent_id');
        $store_id = Yii::$app->request->getQueryParam('store_id');

        if(!$parent_id){
            $parent_id = 0;
        }

        if(!$store_id){
            $store_id = 0;
        }

        Yii::$app->cache->delete('api_categories_' . $store_id);
        $categories = Yii::$app->cache->get('api_categories_' . $store_id);

        if(!$categories){
            $categories = [];
            $items = $this->getCategories($store_id);
            foreach($items as $item){
                if($store_id){
                    $categories[$item['parent_id']][] = [
                        'store_category_id' => (int) $item['category_id'],
                        'name' => $item['name'],
                        'child' => (int) $item['child'],
                        'items' => [],
                    ];
                }else{
                    $categories[$item['parent_id']][] = [
                        'category_id' => (int) $item['category_id'],
                        'name' => $item['name'],
                        'child' => (int) $item['child'],
                        'items' => [],
                    ];
                }
            }
            Yii::$app->cache->set('api_categories_' . $store_id, $categories);
        }

        return $this->getChilds($parent_id, $categories);
    }

    private function getCategories($store_id)
    {
        if($store_id){
            $category_ids = Goods::find()->select('store_category_id')->where(['store_id' => $store_id])->column();
            $query = StoreCategory::find()->where(['store_id' => $store_id, 'category_id' => $category_ids, 'status' => StoreCategory::STATUS_ACTIVE]);
        }else{
            $query = Category::find()->where(['status' => Category::STATUS_ACTIVE]);
        }

        return $query->select('category_id, parent_id, name, child')->orderBy(['sort' => SORT_ASC, 'category_id' => SORT_ASC])->indexBy('category_id')->asArray()->all();
    }

    private function getChilds($parent_id = 0, $categories)
    {
        $return = [];

        if(isset($categories[$parent_id])){
            $return = array_map(function($category) use ($categories){
                if($category['child']){
                    $category['items'] = $this->getChilds($category['category_id'], $categories);
                }

                return $category;
            }, $categories[$parent_id]);
        }

        return $return;
    }
}