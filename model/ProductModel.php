<?php
namespace app\model;

use think\Model;

class Product extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'bl_kcsp';
    
    // 开启自动时间戳
    protected $autoWriteTimestamp = true;
    
    // 定义多级分类关系
    public function children()
    {
        return $this->hasMany('Product', 'class', 'id');
    }
    
    // 定义父级分类关系
    public function parent()
    {
        return $this->belongsTo('Product', 'class');
    }
}
