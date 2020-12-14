<?php
namespace wap\controllers;

/**
 * Cart controller
 */
class CartController extends Controller
{

    public function actionIndex()
    {
        return $this->message('功能开发中！', ['site/index']);
    }
}
