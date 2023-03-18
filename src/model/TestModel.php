<?php

namespace Model;

use Sabo\Model\Attribute\TableColumn;
use Sabo\Model\Attribute\TableName;
use Sabo\Model\Cond\PrimaryKeyCond;
use Sabo\Model\Model\SaboModel;

#[TableName("sabo-final")]
class TestModel extends SaboModel{
    #[TableColumn("id_bdd",new PrimaryKeyCond)]
    protected int $id;

    #[TableColumn("nom",new PrimaryKeyCond)]
    protected string $name;

    #[TableColumn("value")]
    protected string $value;    

    public string $y;
}