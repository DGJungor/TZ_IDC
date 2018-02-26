<?php
    namespace app\portal\model;
    
    use app\admin\model\RouteModel;
    use think\Model;
    use tree\Tree;
    use think\Db;
    class InfoPostModel extends Model
    {
        protected $type = [
            'more' => 'array',
        ];
        // 开启自动写入时间戳字段
        protected $autoWriteTimestamp = true;
        public function getProductData($categorie = -1) {
            if($categorie==-1) {
              $result = $this->where("post_status",1)->select();
              return $result;
            }else {
                $result = $this->where(["post_status"=>1,"id"=>$categorie])->find();
                return $result;
            }
        }
        public function adminAddProduct($data, $categories) {
            $data['user_id'] = cmf_get_current_admin_id();
            if (!empty($data['more']['thumbnail'])) {
                $data['more']['thumbnail'] = cmf_asset_relative_url($data['more']['thumbnail']);
            }
            
            $data['more']['server'] = str_replace('，', ',', $data['more']['server']);
            if(isset($data["post_excerpt"])) {
                $data["post_introduction"] = $data["post_excerpt"];
            }
            if(isset($data["post_company_info"])|isset($data["post_advantage"])|isset($data["post_performance"])|isset($data["post_recommend"])) {
                $data["post_company_info"] = $this->setPostContentAttr($data["post_company_info"]);
                $data["post_advantage"] = $this->setPostContentAttr($data["post_advantage"]);
                $data["post_performance"] = $this->setPostContentAttr($data["post_performance"]);
                $data["post_recommend"] = $this->setPostContentAttr($data["post_recommend"]);
            }
            $data["user_type"] = 1;
        
            
            
            $this->allowField(true)->data($data, true)->isUpdate(false)->save();
            if (is_string($categories)) {
                $categories = explode(',', $categories);
            }
            $this->categories($categories,$this->id);
            $keywords = explode(',', $data['more']['server']);
    
            $this->addTags($keywords, $this->id);
    
            return $this;
        }
        /**
     * post_content 自动转化
     * @param $value
     * @return string
     */
    public function getPostContentAttr($value)
    {
        return cmf_replace_content_file_url(htmlspecialchars_decode($value));
    }
        /**
     * post_content 自动转化
     * @param $value
     * @return string
     */
    public function setPostContentAttr($value)
    {
        return htmlspecialchars(cmf_replace_content_file_url(htmlspecialchars_decode($value), true));
    }

    /**
     * published_time 自动完成
     * @param $value
     * @return false|int
     */
    public function setPublishedTimeAttr($value)
    {
        return strtotime($value);
    }
        /**
     * 后台管理编辑文章
     * @param array $data 文章数据
     * @param array|string $categories 文章分类 id
     * @return $this
     */
    public function adminEditArticle($data, $categories)
    {

        unset($data['user_id']);

        if (!empty($data['more']['thumbnail'])) {
            $data['more']['thumbnail'] = cmf_asset_relative_url($data['more']['thumbnail']);
        }
        $data['more']['server'] = str_replace('，', ',', $data['more']['server']);
        $data['post_status'] = empty($data['post_status']) ? 0 : 1;
        $data['is_top']      = empty($data['is_top']) ? 0 : 1;
        // $data['recommended'] = empty($data['recommended']) ? 0 : 1;
        
        $this->allowField(true)->isUpdate(true)->data($data, true)->save();
        if (is_string($categories)) {
            $categories = explode(',', $categories);
        }

        $this->categories($categories,$this->id,TRUE);
        // $sameCategoryIds       = array_intersect($categories, $oldCategoryIds);
        // $needDeleteCategoryIds = array_diff($oldCategoryIds, $sameCategoryIds);
        // $newCategoryIds        = array_diff($categories, $sameCategoryIds);

        // if (!empty($needDeleteCategoryIds)) {
        //     $this->categories()->detach($needDeleteCategoryIds);
        // }

        // if (!empty($newCategoryIds)) {
        //     $this->categories()->attach(array_values($newCategoryIds));
        // }


        // $data['post_keywords'] = str_replace('，', ',', $data['post_keywords']);

        // $keywords = explode(',', $data['post_keywords']);

        // $this->addTags($keywords, $data['id']);

        return $this;

    }
        public function addTags($keywords, $articleId)
        {
            
        }
        public function getTypeList($id) {
            $result = [];
           $data = Db::name("info_category_post")->alias('a')->where('post_id', $id)->join("idckx_info_category c","c.id = a.category_id")->select();
           foreach($data as $k=>$v) {
            $result[$v["id"].""] = $v["name"];
           }
           
           return $result;
        }
        /**
         * 关联分类表
         */
        public function categories($categories = [], $articleId = -1,$flag = FALSE)
        {
            if($flag) {
                Db::name("info_category_post")->where('post_id',$articleId)->delete();
            }
            foreach($categories as $k => $v) {
                Db::name("info_category_post")->insert([
                    "post_id"=>$articleId,
                    "category_id"=>$v
                ]);
            }
        }
        
    }