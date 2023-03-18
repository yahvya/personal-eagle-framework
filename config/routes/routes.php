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
        $queryBuilder = QueryBuilder::createFrom(TestModel::class)
            ->as("sb")
            ->select([SqlFunction::AVG,"id"])
            ->where()
            ->whereGroupSep(SqlSeparator::AND,["id",1,SqlComparator::SUPERIOR,SqlSeparator::OR],["id",1,SqlComparator::EQUAL])
            ->whereGroup(["id",1,SqlComparator::SUPERIOR,SqlSeparator::OR],["id",1,SqlComparator::EQUAL])
            ->orderBy("id",["id",SqlSeparator::DESC]);

        die($queryBuilder->getSqlString() );
    },"Home:home_page",["page" => Regex::intRegex()])
]); 