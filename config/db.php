<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=d_easyhin_his',
    'username' => 'root',
    'password' => '123456',
    'charset' => 'utf8',
    'tablePrefix'=>'gzh_',
    'queryCacheDuration' => 3600,
    'queryCache' => 'cache',
    'enableQueryCache' => true,

    'enableSchemaCache' => true,
    
    // Duration of schema cache.
    'schemaCacheDuration' => 3600,
    
    // Name of the cache component used to store schema information
    'schemaCache' => 'cache',



];