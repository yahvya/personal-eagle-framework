<?php

use Model\TestModel;
use Sabo\Helper\Regex;
use Sabo\Model\System\Mysql\SaboMysql;
use Sabo\Model\System\QueryBuilder\QueryBuilder;
use Sabo\Model\System\QueryBuilder\SqlComparator;
use Sabo\Model\System\QueryBuilder\SqlFunction;
use Sabo\Model\System\QueryBuilder\SqlSeparator;
use Sabo\Sabo\Route;

return Route::generateFrom([
    Route::get("/",function():void{
        $model = TestModel::find([
            "name" => "nouveau nom"
        ]);

        echo "<pre>";
        var_dump($model[0]->delete() );
        die();
    },"Home:home_page",["page" => Regex::intRegex()])
]);