<?php
namespace wap\widgets;

use Yii;
use yii\bootstrap\Html;
use yii\web\View;

/**
 * 底部菜单
 *
 * @author Jony <jonneyless@163.com>
 */
class BottomBar extends \yii\base\Widget
{
    /**
     * @var 信息类型
     */
    public $buttons = [
        ['label' => '首页', 'url' => ['site/index'], 'icon' => '&#xe61d;', 'class' => '', 'options' => [], 'active' => 'site/index'],
    ];

    /**
     * 初始化变量
     */
    public function init()
    {
        if ($this->view->context->bottomBar) {
            foreach ($this->view->context->bottomBar as $link) {
                if (!isset($link['icon'])) {
                    $link['icon'] = '';
                }

                if(!isset($link['class'])){
                    $link['class'] = '';
                }

                if(!isset($link['options'])){
                    $link['options'] = [
                        'rel' => 'holder-link',
                    ];
                }else if(!isset($link['options']['rel'])){
                    $link['options']['rel'] = 'holder-link';
                }

                if(!isset($link['active'])){
                    if(is_array($link['url'])){
                        $link['active'] = $link['url'][0];
                    }else{
                        $link['active'] = '';
                    }
                }

                $this->buttons[] = $link;
            }
        }

        if(!$this->view->context->bottomBarActive){
            $this->view->context->bottomBarActive = $this->view->context->id . '/' . $this->view->context->action->id;
        }
    }

    /**
     * 输出
     */
    public function run()
    {
        echo $this->render('/widget/bottom-bar', [
            'buttons' => $this->renderButton(),
        ]);
    }

    public function renderButton()
    {
        $buttons = [];
        foreach($this->buttons as $button){
            $icon = '';
            if($button['icon']){
                $icon = Html::tag('span', $button['icon'], ['class' => 'icon iconfont']) . '<br />';
            }

            $link = Html::a($icon . $button['label'], $button['url'], $button['options']);

            $active = false;
            if($button['active'] == $this->view->context->bottomBarActive){
                $active = true;
            }

            $options = [
                'class' => '',
            ];

            if(isset($button['class'])){
                $options['class'] = $button['class'];
            }

            if($active){
                $options['class'] = $options['class'] ? $options['class'] . ' active': 'active';
            }

            $buttons[] = Html::tag('li', $link, $options);
        }
        return $buttons;
    }
}
