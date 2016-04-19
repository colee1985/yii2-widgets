<?php
/**
 * ==============================================
 * Copy right 2015-2016
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * 多级联动的下拉菜单
 * 重点： 每一级都使用ajax获取数据，所以需要提供每一级的ajax url
 *      必须填写每一级的模型属性(attribute)
 * eg:
 * LinkageSelect::widget([
 *      'model'=>$model,
 *      'template'=>'{item-0} {item-1}', // 可选项
 *      'items'=>[
 *          [
 *              'attribute'=>'city_id',
 *              'url'=>['city/find'],
 *              'initValueText'=>'北京',
 *          ],
 *          [
 *              'attribute'=>'area_id',
 *              'url'=>['area/find'],
 *              'initValueText'=>'朝阳',
 *          ]
 *      ],
 *      'hideSearch'=>true,
 * ]);
 * @param unknowtype
 * @return return_type
 * @author: CoLee
 */
namespace colee\widgets;

use kartik\base\InputWidget;
use kartik\select2\Select2;
use yii\web\JsExpression;
use yii\helpers\Html;

class LinkageSelect extends InputWidget
{
    public $name = 'LinkageSelect';
    public $items=[];
    public $hideSearch = false;
    public $template;
    
    /**
     * 拆分配置选项到数组
     * @return multitype:string
     */
    public function splitItems()
    {
        $ids = [];
        $attributes=[];
        foreach ($this->items as $item){
            $ids[] = Html::getInputId($this->model, $item['attribute']);
            $attributes[] = $item['attribute'];
            $initValueTexts[] = empty($item['initValueText'])?null:$item['initValueText'];
        }
        return [
            'htmlIds'=>$ids,
            'attributes'=>$attributes,
            'initValueTexts'=>$initValueTexts,
        ];
    }
    
    /**
     * 生成select2的HTML数组
     * @return multitype:string
     */
    public function getHtmlItemsArray()
    {
        $options = $this->splitItems();
        $htmlIds = $options['htmlIds'];
        $attributes = $options['attributes'];
        $initValueTexts = $options['initValueTexts'];
        $model = $this->model;
        
        $items = [];
        foreach ($this->items as $i=>$option){
            $val = empty($htmlIds[$i-1])?0:new JsExpression("$('#{$htmlIds[$i-1]}').val()");
            $items[] = Select2::widget([
                'model'=>$model,
                'attribute'=>$option['attribute'],
                'options'=>[
                    'placeholder' => '请选择'.$model->getAttributeLabel($option['attribute']),
                    'id'=>$htmlIds[$i],
                ],
                'initValueText'=>$initValueTexts[$i],
                'hideSearch' => $this->hideSearch,
                'pluginOptions' => [
                    'allowClear' => true,
                    'minimumInputLength' => 0,
                    'ajax' => [
                        'url' => \Yii::$app->urlManager->createUrl($option['url']),
                        'dataType' => 'json',
                        'data' => new JsExpression('function(params) {
                            return {
                                ajax:1,
                                term: params.term,
                                parent_id: '.$val.'
                            };
                        }'),
                    ],
                ],
                'pluginEvents'=>empty($htmlIds[$i+1])?[]:[
                    'change' => 'function() {
    	               $("#'.$htmlIds[$i+1].'").val(0).trigger("change");
                    }',
                ],
            ]);
        }
        
        return $items;
    }
    
    /**
     * 模版
     */
    public function getTemplate()
    {
        $template = $this->template;
        $col = ceil(12/count($this->items));
        if(empty($template)){
            $template .= '<div class="row">';
            foreach ($this->items as $i=>$item){
                $template .= '<div class="col-md-'.$col.'">';
                $template .= '{item-'.$i.'}';
                $template .= '</div>';
            }
            $template .= '</div>';
        }
        
        return $template;
    }
    
    public function run()
    {
        $template = $this->getTemplate();
        $items = $this->getHtmlItemsArray();
        foreach ($items as $i=>$item){
            $template = str_replace('{item-'.$i.'}', $item, $template);
        }
        
        return $template;
    }
}
