<?php
    /**
 * 过滤查询出来的数据字段的
 * 方法名：filter
 * 参数：$arr,$filter=[],$myKey=false
 * $arr 是要过滤的数据
 * $filter 要筛选出来的字段
 * $myKey 是否要自定义输出的字段名称
 * 例如：
 *  filter(["a"=>10,"b"=>20,"c"=>30],["a","c"]) 它就会返回 ["a"=>10,"c"=>30]
*/
    function filter($arr,$filter=[],$myKey=false) {
        $result = [];
        foreach($arr as $k=>$v) {
            foreach($filter as $key=>$value) {
                if($value==$k) {
                    // array_push($result,$v);
                    if($myKey) {
                        $result[$key] = $v;
                    }else {
                        $result[$k] = $v;
                    }
                    
                }
            }
        }
        return $result;
    }