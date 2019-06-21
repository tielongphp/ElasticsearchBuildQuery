# elasticsearch-build-query
对Elasticsearch-PHP进行查询语句封装 可实现链式调用 方便 es查询

注：elasticsearch-php git地址：https://github.com/elastic/elasticsearch-php

# Installation via Composer
The recommended method to install ielongphp/es-build-query is through Composer.

Add tielongphp/es-build-query as a dependency in your project's composer.json file (change version to suit your version of tielongphp/es-build-query, for instance for ^1.0):

    {
        "require": {
           "tielongphp/es-build-query": "^1.0",
        }
    }
Download and install Composer:

    curl -s http://getcomposer.org/installer | php
Install your dependencies:

    php composer.phar install
Require Composer's autoloader

Composer also prepares an autoload file that's capable of autoloading all the classes in any of the libraries that it downloads. To use it, just add the following line to your code's bootstrap process:

    <?php

    use EsBuildQuery\EsBuildQuery;
    
    require 'vendor/autoload.php';
    
    $config = '127.0.0.1:9200'
    
    $elasticSearch  = new EsBuildQuery($config);

You can find out more on how to install Composer, configure autoloading, and other best-practices for defining dependencies at getcomposer.org.


# Documentation && Quickstart


example:

1、 

          $elasticSearch = new \EsBuildQuery\EsBuildQuery("127.0.1.1:9200");
          $config = [
              'indexName' => '_indexName',
              'indexType' => '_typeName'
          ];
          
          $attrWhere['must'][]['range']['@timestamp'] = [
              'gt' => strtotime('2019-01-01').'000',
              'lte' => strtotime('2019-01-02').'000',
              'format' => "epoch_millis"
          ];
          //精确匹配：requestUri = '/a/b/c'
          $attrWhere['must'][]['match']['requestUri'] = '/a/b/c';
          
          $attList = $elasticSearch->_set($config)
              ->where($attrWhere)
              ->limit(20, 0)
              ->fields(['requestUri','apiStart'])
              ->select();


          相当于SQl：
          selsect requestUri,apiStart from tableName where @timestamp > ? and @timestamp<= ?
                and requestUri = '/a/b/c' LIMIT 20
                
2、        
                  
                   // 正则查询：排除request 中带后缀名的 如.jpg、.js等
                   $commonWhere['must'][]['regexp']['request.keyword'] = '/*([^.]*)';// /*([^.]*?)
                   // 模糊匹配查询：只查询域名字段domain中包含XXX的数据
                   $commonWhere['must'][]['query_string'] = [
                       'query' => 'domain:*XXX*',
                       "analyze_wildcard" => true,
                       "default_field" => "*"
                   ];
           

                   // group 分组查询
                   $arrList = $elasticSearch->_set($config)
                       ->where($commonWhere)
                       ->group("domain.keyword")
                       ->select();
                       
3、Some examples of where conditions 
                   
                      //可以实现模糊匹配查询：类似于 myql的like  %/json%
                      $where['must'][]['match_phrase']['request']['query'] = '/json';
                      // apiGwResSign === 'amendments'
                      $where['must'][]['match']['apiGwResSign'] = 'amendments';
                      // request 不包含 '/nbig'
                      $where['must_not'][]['match_phrase']['request']['query'] = '/nbig';
                      ....
                       
4、The methods that the EsBuildQuery class can use are as follows

                        /**
                          * 设置参数
                          * @param array $config
                          *            配置
                          *            array(
                          *            'indexName'=>'索引名称',
                          *            'indexType'=>'索引类型',
                          *            'numberOfShards'=>'主分片数量',
                          *            'numberOfReplicas'=>'从分片数量',
                          *            'mapping'=>'过滤器'
                          *            )
                          */
                        public function _set($config)
                       
                        /**
                         * 创建索引
                         * 调用_set()方法完成赋值
                         * @author wen
                         * @param string $this ->indexName
                         *            索引名称
                         * @param int $this ->numberOfShards
                         *            主分片数量
                         * @param int $this ->numberOfReplicas
                         *            从分片数量
                         * @return bool
                         */
                        public function createIndex()
                       
                        /**
                         * 检测索引是否存在
                         * @author wen
                         * @param string $this ->indexName
                         *            索引名称
                         * @return bool
                         */
                        public function checkIndex()
                       
                    
                        /**
                         * 插入索引数据
                         * 调用_set()方法完成赋值
                         * @author wen
                         * @param string $this ->indexName
                         *            索引名称
                         * @param string $this ->indexType
                         *            索引类型
                         * @param array $data
                         *            数据 array('字段1'=>'值1', '字段2'=>'值2' ...)
                         * @return bool|array
                         */
                        public function add($data)
                       
                    
                        /**
                         * 判断索引数据是否存在
                         * 调用_set()方法完成赋值
                         * @author wen
                         * @param string $this ->indexName
                         *            索引名称
                         * @param string $this ->indexType
                         *            索引类型
                         * @param string $this ->documentId
                         *            文档id
                         * @return bool|array
                         */
                        public function exists()
                       
                    
                        /**
                         * 获取索引数据
                         * 调用_set()方法完成赋值
                         * @author wen
                         * @param string $this ->indexName
                         *            索引名称
                         * @param string $this ->indexType
                         *            索引类型
                         * @param string $this ->documentId
                         *            文档id
                         * @return bool|array
                         */
                        public function find()
                                         
                        /**
                         * 查询条件方法
                         * @author wen
                         * @param array $where
                         *            查询条件
                         *            // must :: 多个查询条件的完全匹配,相当于 and。
                                      // must_not :: 多个查询条件的相反匹配，相当于 not。
                                      // should :: 至少有一个查询条件匹配, 相当于 or。
                                      // term主要用于精确匹配哪些值
                                      // terms 跟 term 有点类似，但 terms 允许指定多个匹配条件。 如果某个字段指定了多个值，那么文档需要一起去做匹配
                                      // multi_match 同意内容搜索多个字段
                                      // range 比较 gt gte lt lte
                         */
                        public function where($where)
                        
                        /**
                         * 数据分组
                         * @author wen
                         * @param string|array $group
                         *            字段名|聚合条件
                         */
                        public function group($group)
                        
                    
                        /**
                         * 指定字段
                         * @author wen
                         * @param string|array $fields
                         *            字段
                         *            例：id,name
                         *            例：['id','name')
                         */
                        public function fields($fields)
                       
                    
                        /**
                         * 数据排序
                         * @author wen
                         * @param string|array $sort
                         *            排序 最好是用数字类的进行排序
                         *            例：price:asc,time:desc
                         */
                        public function order($sort)
                        
                    
                        /**
                         * 数据分页
                         * @author wen
                         * @param int $size
                         *            分页条数
                         * @param int $from
                         *            分页开始位置
                         */
                        public function limit($size = 10, $from = 0)
                        
                    
                        /**
                         * 数据分页-深度
                         * @author wen
                         * @param string $time
                         *            查询时间
                         * @param int $size
                         *            分页条数
                         */
                        public function scroll($size = 10, $time = "30s")
                       
                    
                        /**
                         * scroll_id 数据分页-深度第二页及以后需要的参数
                         * @author wen
                         * @param string $scroll_id
                         *            第一次请求或之后请求时得到的scroll_id
                         */
                        public function scrollId($scroll_id)
                       
                    
                        /**
                         * 索引数据 统计
                         * @author wen
                         */
                        public function count()
                        
                    
                        /**
                         * 高亮显示
                         * @author wen
                         * @param array $filedArr
                         *            需要高亮的字段 例 array('name', 'summary')
                         * @param string $pre_tags
                         *            开始标签
                         * @param string $post_tags
                         *            结束标签
                         */
                        public function highlight($filedArr, $pre_tags = '<em>', $post_tags = '</em>')
                       
                    
                        /**
                         * 获取索引数据
                         * 调用_set()方法完成赋值
                         * @author wen
                         * @param string $this ->indexName
                         *            索引名称
                         * @param string $this ->indexType
                         *            索引类型
                         * @return bool|array
                         */
                        public function select()
                        
                    
                        /**
                         * 修改索引数据
                         * 调用_set()方法完成赋值
                         * @author wen
                         * @param string $this ->indexName
                         *            索引名称
                         * @param string $this ->indexType
                         *            索引类型
                         * @param string $this ->documentId
                         *            文档id
                         * @param array $data
                         *            要修改的数据 必须保证数据字段和添加时全部对应 ['字段1'=>'值1', '字段2'=>'值2' ...]
                         * @return bool|array
                         */
                        public function save($data)
                    
                        /**
                         * 删除索引数据
                         * 调用_set()方法完成赋值
                         * @author wen
                         * @param string $this ->indexName
                         *            索引名称
                         * @param string $this ->indexType
                         *            索引类型
                         * @param string $this ->documentId
                         *            文档id
                         * @return bool
                         */
                        public function delete()
                        
                        /**
                         * 删除索引
                         * 调用_set()方法完成赋值
                         * @author wen
                         * @param string $this ->indexName
                         *            索引名称
                         * @return bool
                         */
                        public function deleteIndex()
                      
                    
                       
5、More instance methods can be found in the EsBuildQuery class and
   https://github.com/elastic/elasticsearch-php
   https://www.elastic.co/guide/index.html

           
                
         

