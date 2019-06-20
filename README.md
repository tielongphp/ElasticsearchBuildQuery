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


