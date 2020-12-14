<?php
namespace wap\controllers;

/**
 * Category controller
 */
class CategoryController extends Controller
{

    public function actionIndex()
    {
        return $this->message('功能开发中！', ['site/index']);
    }
}
