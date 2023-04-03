<?php

namespace Model;

use Sabo\Model\Attribute\TableColumn;
use Sabo\Model\Attribute\TableName;
use Sabo\Model\Cond\PrimaryKeyCond;
use Sabo\Model\Model\SaboModel;

/**
 * model
 * @name UserModel
 */
#[TableName("user")]
class UserModel extends SaboModel{
	#[TableColumn("id",false,new PrimaryKeyCond(true) )]
	protected int $id;

	#[TableColumn("name",true)]
	protected ?string $name = null;

	#[TableColumn("email",false)]
	protected string $email;

	#[TableColumn("password",true)]
	protected ?string $mdp = null;
}