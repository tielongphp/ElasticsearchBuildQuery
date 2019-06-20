<?php

namespace App\library;

use Elasticsearch\ClientBuilder;

class EsBuild
{
    // 实例化对象变量
    private $client = null;
    // 索引名称
    private $indexName;
    // 类型名称
    private $indexType;
    // 映射
    private $mapping;
    // 主分片数量
    private $numberOfShards = 5;
    // 从分片数量
    private $numberOfReplicas = 0;
    // 文档id
    private $documentId;
    // 查询表达式参数
    protected $options = [];
    // 连接配置
    private $config = [];

    // 构造方法

    /**
     * ElasticSearch constructor.
     * @param $hostsAndPorts 139.217.5.85:9200  如有多个用逗号分割；
     */
    public function __construct($hostsAndPorts)
    {
        if ($this->client === null) {
            $this->config = explode( ",", $hostsAndPorts );
            if (empty( $this->config )) {
                throw new \Exception( 'ElasticSearch config format error...' );
            }
            $this->client = ClientBuilder::create()->setHosts( $this->config )->build();
        }
    }

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
    {
        $this->indexName = $config['indexName'];
        !empty( $config['indexType'] ) && $this->indexType = $config['indexType'];
        !empty( $config['numberOfShards'] ) && $this->numberOfShards = $config['numberOfShards'];
        !empty( $config['numberOfReplicas'] ) && $this->numberOfReplicas = $config['numberOfReplicas'];
        !empty( $config['documentId'] ) && $this->documentId = $config['documentId'];
        !empty( $config['mapping'] ) && $this->mapping = $config['mapping'];
        return $this;
    }

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
    {
        $params = array(
            'index' => $this->indexName,
            'body' => [
                'settings' => [
                    'number_of_shards' => $this->numberOfShards,
                    'number_of_replicas' => $this->numberOfReplicas
                ]
            ]
        );
        // "analysis" => array(
        // "analyzer" => array(
        // "pinyin" => array(
        // "tokenizer" => "my_pinyin",
        // "filter" => "word_delimiter"
        // )
        // ),
        // "tokenizer" => array(
        // "my_pinyin" => array(
        // "type" => "pinyin",
        // "first_letter" => "none",
        // "padding_char" => " "
        // )
        // )
        // )

        // 判断是否需要映射
        if (!empty( $this->mapping )) {
            $params['body']['mappings'] = $this->mapping;
        }

        /*
         * $params = array(
         * 'index' => 'my_index',
         * 'body' => array(
         * 'settings' => array(
         * 'number_of_shards' => 3,
         * 'number_of_replicas' => 2
         * ),
         * 'mappings' => array(
         * 'my_type' => array(
         * '_source' => array(
         * 'enabled' => true
         * ),
         * 'properties' => array(
         * 'first_name' => array(
         * 'type' => 'string',
         * 'analyzer' => 'standard'
         * ),
         * 'age' => array(
         * 'type' => 'integer'
         * )
         * )
         * )
         * )
         * )
         * );
         */
        $response = $this->client->indices()->create( $params );
        if (!empty( $response ) && $response['acknowledged'] == 1) {
            return true;
        }
        return false;
    }

    /**
     * 检测索引是否存在
     * @author wen
     * @param string $this ->indexName
     *            索引名称
     * @return bool
     */
    public function checkIndex()
    {
        $params = array(
            'index' => $this->indexName
        );
        return $this->client->indices()->exists( $params );
    }

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
    {
        $params = array(
            'index' => $this->indexName,
            'type' => $this->indexType,
            'id' => $this->documentId,
            'body' => $data
        );
        $response = $this->client->index( $params );
        if (!empty( $response )) {
            return $response;
        }
        return false;
    }

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
    {
        $params = array(
            'index' => $this->indexName,
            'type' => $this->indexType,
            'id' => $this->documentId
        );
        return $this->client->exists( $params );
    }

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
    {
        // 判断索引数据是否存在
        if (!$this->exists()) {
            return false;
        }
        $params = array(
            'index' => $this->indexName,
            'type' => $this->indexType,
            'id' => $this->documentId
        );
        $response = $this->client->get( $params );
        if (!empty( $response )) {
            return $response['_source'];
        }
        return false;
    }

    /**
     * 查询条件方法
     * @author wen
     * @param array $where
     *            查询条件
     */
    public function where($where)
    {
        // must :: 多个查询条件的完全匹配,相当于 and。
        // must_not :: 多个查询条件的相反匹配，相当于 not。
        // should :: 至少有一个查询条件匹配, 相当于 or。
        // term主要用于精确匹配哪些值
        // terms 跟 term 有点类似，但 terms 允许指定多个匹配条件。 如果某个字段指定了多个值，那么文档需要一起去做匹配
        // multi_match 同意内容搜索多个字段
        // 'multi_match' => array(
        // 'query' => '销售属性',
        // 'fields' => array(
        // 'name',
        // 'summary'
        // )
        // )
        // range 比较 gt gte lt lte
        //
        // JSON type Field type
        // Boolean: true or false "boolean"
        // Whole number: 123 "long"
        // Floating point: 123.45 "double"
        // String, valid date: "2014-09-15" "date"
        // String: "foo bar" "string"
        if (!empty( $where )) {
            $this->options['body'] = [
                'body' => [
                    'query' => [
                        'bool' => $where
                    ]
                ]
            ];
        } else {
            $this->options['body'] = [
                'body' => [
                    'query' => [
                        'match_all' => []
                    ]
                ]
            ];
        }
        return $this;
    }

    /**
     * 数据分组
     * @author wen
     * @param string|array $group
     *            字段名|聚合条件
     */
    public function group($group)
    {
        if (is_array( $group )) {
            $this->options['group'] = $group;
        } else {
            $this->options['group'] = [
                'group_data' => [
                    'terms' => [
                        'field' => $group,
                        'size' => 10000
                    ]
                ]
            ];
        }
        return $this;
    }

    /**
     * 指定字段
     * @author wen
     * @param string|array $fields
     *            字段
     *            例：id,name
     *            例：['id','name')
     */
    public function fields($fields)
    {
        $this->options['fields'] = ['_source' => $fields];
        return $this;
    }

    /**
     * 数据排序
     * @author wen
     * @param string|array $sort
     *            排序 最好是用数字类的进行排序
     *            例：price:asc,time:desc
     */
    public function order($sort)
    {
        $this->options['sort'] = [
            'sort' => $sort
        ];
        return $this;
    }

    /**
     * 数据分页
     * @author wen
     * @param int $size
     *            分页条数
     * @param int $from
     *            分页开始位置
     */
    public function limit($size = 10, $from = 0)
    {
        $this->options['limit'] = [
            'size' => $size,
            'from' => $from
        ];
        return $this;
    }

    /**
     * 数据分页-深度
     * @author wen
     * @param string $time
     *            查询时间
     * @param int $size
     *            分页条数
     */
    public function scroll($size = 10, $time = "30s")
    {
        $this->options['scroll'] = [
            // "search_type" => "scan",
            "scroll" => $time,
            "size" => $size
        ];
        return $this;
    }

    /**
     * scroll_id 数据分页-深度第二页及以后需要的参数
     * @author wen
     * @param string $scroll_id
     *            第一次请求或之后请求时得到的scroll_id
     */
    public function scrollId($scroll_id)
    {
        $this->options['scroll_id'] = $scroll_id;
        return $this;
    }

    /**
     * 索引数据 统计
     * @author wen
     */
    public function count()
    {
        $params = [
            'index' => $this->indexName,
            'type' => $this->indexType
        ];

        // 判断是否有查询条件
        if (!empty( $this->options['body'] )) {
            $params = array_merge( $params, $this->options['body'] );
        }
        $response = $this->client->count( $params );
        return $response['count'];
    }

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
    {
        $newFiledArr = [];
        foreach ($filedArr as $filedKey => $filedVal) {
            $newFiledArr[$filedVal] = [
                "require_field_match" => false
            ];
        }
        $this->options['highlight'] = [
            "pre_tags" => [
                $pre_tags
            ],
            "post_tags" => [
                $post_tags
            ],
            "fields" => $newFiledArr
        ];
        return $this;
    }

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
    {
        $params = [
            'index' => $this->indexName,
            'type' => $this->indexType
        ];

        // 判断是否有指定字段
        if (!empty( $this->options['fields'] )) {
            $params = array_merge( $params, $this->options['fields'] );
        }

        // 判断是否有查询条件
        if (!empty( $this->options['body'] )) {
            // 判断是否需要分组
            if (!empty( $this->options['group'] )) {
                $this->options['body']['body']['aggs'] = $this->options['group'];
            }
            $params = array_merge( $params, $this->options['body'] );
        } else {
            // 判断是否需要分组
            if (!empty( $this->options['group'] )) {
                $group = [
                    'body' => [
                        'aggs' => $this->options['group']
                    ]
                ];
                $params = array_merge( $params, $group );
            }
        }

        // 判断是否需要高亮
        if (!empty( $this->options['highlight'] )) {
            $params['body']['highlight'] = $this->options['highlight'];
        }

        // 判断是否需要排序
        if (!empty( $this->options['sort'] )) {
            $params = array_merge( $params, $this->options['sort'] );
        }

        // 判断是否需要分页
        if (!empty( $this->options['limit'] )) {
            $params = array_merge( $params, $this->options['limit'] );
        }

        // 判断是否需要分页-深度
        if (!empty( $this->options['scroll'] )) {
            $params = array_merge( $params, $this->options['scroll'] );
        }

        // 初始化数据变量
        $arr = [];

        // 判断是否存在scroll_id，存在将采用深度分页方式获取分页数据
        if (!empty( $this->options['scroll_id'] )) {
            $scrollWhere = [
                'scroll_id' => $this->options['scroll_id'],
                'scroll' => '30s'
            ];
            $response = $this->client->scroll( $scrollWhere );
            // print_r($response);
            // die();
        } else {
            $response = $this->client->search( $params );
            // print_r($response);
            // die();
        }

        // 深度化分页时需要赋值
        !empty( $response['_scroll_id'] ) && $arr['scroll_id'] = $response['_scroll_id'];

        if (!empty( $response ) && $response['hits']['total'] > 0) {
            // 判断是否需要分组
            if (!empty( $this->options['group'] )) {
                $arr = $response['aggregations']['group_data']['buckets'];
            } else {
                foreach ($response['hits']['hits'] as $key => $val) {
                    $data = $val['_source'] ?? [];

                    // 提取高亮内容
                    if (isset( $this->options['highlight'] ) && !empty( $this->options['highlight'] )) {
                        foreach ($val['highlight'] as $highlightfield => $highlightvalue) {
                            $data['highlight'][$highlightfield] = $highlightvalue[0];
                        }
                    }
                    if (!empty( $data )) $arr[] = $data;


//                    $data = [];
                    // 有指定字段数组整理

//                    if (!empty($this->options['fields'])) {
//
//                        foreach ($val['_source'] as $field => $value) {
//                            $data[$field] = $value[0];
//                        }
//                        // 提取高亮内容
//                        if (!empty($this->options['highlight'])) {
//                            foreach ($val['highlight'] as $highlightfield => $highlightvalue) {
//                                $data['highlight'][$highlightfield] = $highlightvalue[0];
//                            }
//                        }
//                        $arr[] = $data;
//                    } else {
//                        $data = $val['_source'];
//
//                        // 提取高亮内容
//                        if (!empty($this->options['highlight'])) {
//                            foreach ($val['highlight'] as $highlightfield => $highlightvalue) {
//                                $data['highlight'][$highlightfield] = $highlightvalue[0];
//                            }
//                        }
//                        $arr[] = $data;
//                    }
                }
            }
            // 查询过后清空表达式组装 避免影响下次查询
            //$this->options = [];
            return $arr;
        }

//      查询过后清空表达式组装 避免影响下次查询
//      $this->options = [];
        return false;
    }

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
    {
        $params = [
            'index' => $this->indexName,
            'type' => $this->indexType,
            'id' => $this->documentId,
            'body' => $data
        ];
        $response = $this->client->index( $params );
        return empty( $response ) ? false : $response;
    }

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
    {
        if (!$this->exists()) { // 判断索引数据是否存在
            return true;
        }
        $params = [
            'index' => $this->indexName,
            'type' => $this->indexType,
            'id' => $this->documentId
        ];
        $response = $this->client->delete( $params );
        return !empty( $response ) && $response['_shards']['successful'] > 0;
    }

    /**
     * 删除索引
     * 调用_set()方法完成赋值
     * @author wen
     * @param string $this ->indexName
     *            索引名称
     * @return bool
     */
    public function deleteIndex()
    {
        $deleteParams = [
            'index' => $this->indexName
        ];
        $response = $this->client->indices()->delete( $deleteParams );
        return !empty( $response ) && $response['acknowledged'] == 1 ? true : false;
    }
}
