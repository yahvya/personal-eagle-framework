<?php

namespace Model;

use Sabo\Model\Attribute\TableColumn;
use Sabo\Model\Attribute\TableName;
use Sabo\Model\Cond\PrimaryKeyCond;
use Sabo\Model\Model\SaboModel;

#[TableName("test_table")]
class TestModel extends SaboModel{
    #[TableColumn("id",new PrimaryKeyCond(true) )]
    protected int $id;

    #[TableColumn("name")]
    protected string $name;  

    public string $y;
}