<?php

use Model\TestModel;
use Sabo\Helper\Regex;
use Sabo\Model\System\QueryBuilder\QueryBuilder;
use Sabo\Model\System\QueryBuilder\SqlComparator;
use Sabo\Model\System\QueryBuilder\SqlFunction;
use Sabo\Model\System\QueryBuilder\SqlSeparator;
use Sabo\Sabo\Route;

return Route::generateFrom([
    Route::get("/{lang}/{page}",function(string $lang,string $page):void{
        // $testModel = new TestModel();
    
        // $testModel
        //     ->setAttribute("id",1)
        //     ->setAttribute("name","yahaya")
        //     ->setAttribute("value","azir");
    
        // $testModel->update();
    
        TestModel::find([
            "id" => 1,
            "name" => ["yahaya",SqlComparator::LIKE,SqlSeparator::OR],
            "value" => [10,SqlComparator::SUPERIOR]
        ],["id"]);
    },"Home:home_page",["page" => Regex::intRegex()])
]);