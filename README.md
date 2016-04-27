# yii2-widgets
Yii2通用组件库
-----------------
### 多级联动的下拉菜单
> 重点： 每一级都使用ajax获取数据，所以需要提供每一级的ajax url
> 必须填写每一级的模型属性(attribute)
```php
LinkageSelect::widget([
     'model'=>$model,
     'template'=>'{item-0} {item-1}', // 可选项
     'items'=>[
         [
             'attribute'=>'city_id',
             'url'=>['city/find'],
             'initValueText'=>'北京',
         ],
         [
             'attribute'=>'area_id',
             'url'=>['area/find'],
             'initValueText'=>'朝阳',
         ]
     ],
     'hideSearch'=>true,
]);
```