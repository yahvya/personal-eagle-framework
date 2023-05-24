<?php

use Sabo\Model\Attribute\TableColumn;
use Sabo\Model\Attribute\TableName;
use Sabo\Model\Cond\PrimaryKeyCond;
use Sabo\Model\Model\SaboModel;
use Sabo\Model\System\Mysql\MysqlReturn;
use Sabo\Model\System\QueryBuilder\QueryBuilder;
use Sabo\Model\System\QueryBuilder\SqlSeparator;
use Sabo\Sabo\Route;

#[TableName("test_table")]
class TestTableModel extends SaboModel{
    #[TableColumn("id",false,new PrimaryKeyCond(true) )]
    protected int $id;

    #[TableColumn("name",false)]
    protected string $name;
}

// routes à placer ici
return Route::generateFrom([
    Route::get("/",function():void{
        // select * from test_table as q1 where q1.id = 1 and q1.id in ((select q2.id from test_table as q2 where q2.id = q1.id and q2.name = "yahaya"))

        $q1 = QueryBuilder::createFrom(TestTableModel::class);
        $q2 = QueryBuilder::createFrom(TestTableModel::class);

        $q1->as("q1");
        $q2->as("q2");

        $q2
            ->select("id")
            ->where()
            ->addSql("{$q2->getAttributeLinkedColName("id",true)} = {$q1->getAttributeLinkedColName("id",true)} and ")
            ->whereCond("name","yahaya");

        $q1
            ->select()
            ->where()
            ->whereCond("id",1,nextSeparator: SqlSeparator::AND)
            ->joinQuery($q2,"{$q1->getAttributeLinkedColName("id",true)} in (",")");

        echo "<pre>";
        var_dump(SaboModel::execQuery($q1,MysqlReturn::OBJECTS) );
    },"Home:home")
]);
