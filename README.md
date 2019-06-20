# elasticsearch-build-query
对Elasticsearch-PHP进行查询语句封装 可实现链式调用 方便 es查询

注：elasticsearch-php git地址：https://github.com/elastic/elasticsearch-php

Documentation
Full documentation can be found here. Docs are stored within the repo under /docs/, so if you see a typo or problem, please submit a PR to fix it!

Installation via Composer
The recommended method to install Elasticsearch-PHP is through Composer.

Add elasticsearch/elasticsearch as a dependency in your project's composer.json file (change version to suit your version of Elasticsearch, for instance for ES 7.0):

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


Quickstart


