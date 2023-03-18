<?php

namespace Model;

use Sabo\Model\Attribute\TableColumn;
use Sabo\Model\Attribute\TableName;
use Sabo\Model\Model\SaboModel;

#[TableName("sabo-final")]
class TestModel extends SaboModel{
    #[TableColumn("id_bdd")]
    protected int $id;
}