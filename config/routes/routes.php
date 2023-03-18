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
        $testModel = new TestModel();

        $testModel
            ->setAttribute("id",1)
            ->setAttribute("name","yahaya")
            ->setAttribute("value","azir");

        $testModel->delete();
    },"Home:home_page",["page" => Regex::intRegex()])
]); 