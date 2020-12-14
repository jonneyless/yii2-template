<?php
namespace wap\widgets;

use yii\base\Widget;

/**
 * 顶部组件
 *
 * @author Jony <jonneyless@163.com>
 */

class TopBar extends Widget
{

    public $buttons = [
        'back' => '',
        'option' => '',
        'home' => '',
    ];

    /**
     * 初始化变量
     */
    public function init()
    {
        if(isset($this->view->context->topBar['backButton'])){
            $this->buttons['back'] = $this->view->context->topBar['backButton'];
        }

        if(isset($this->view->context->topBar['optionButton'])){
            $this->buttons['option'] = $this->view->context->topBar['optionButton'];
        }

        if(isset($this->view->context->topBar['homeButton'])){
            $this->buttons['home'] = $this->view->context->topBar['homeButton'];
        }

        if($this->view->context->topBar === false){
            $this->buttons = false;
        }
    }

    public function run()
    {
        return $this->render('/widget/top-bar', [
            'bottons' => $this->buttons,
        ]);
    }
}