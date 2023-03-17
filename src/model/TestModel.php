<?php

namespace Model;

use Sabo\Model\Attribute\TableColumn;
use Sabo\Model\Attribute\TableName;
use Sabo\Model\Cond\PrimaryKeyCond;
use Sabo\Model\Cond\RegexCond;
use Sabo\Model\Model\SaboModel;

#[TableName("table_de_test")]
class TestModel extends SaboModel{
    
    #[TableColumn("nom",new PrimaryKeyCond,new RegexCond("[a-zA-Z]","Le nom doit être uniquement composé de caractères") )]
    protected string $name;
}