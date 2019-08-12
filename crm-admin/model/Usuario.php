<?php 
/* *******************************
 *
 * Developer by FelipheGomez
 *
 * Git: https://github.com/Feliphegomez/crm-crud-api-php
 * *******************************
 */

class Usuario extends EntidadBase {
    public $id;
    public $username;
    public $names;
    public $surname;
    public $phone;
    public $mobile;
    public $email;
    public $password;
    public $permissions;
    public $registered;
    public $updated;
    public $last_connection;
     
    public function __construct() {
        $table = TBL_USERS;
        parent::__construct($table);
    }
	
	public function __toString(){
		return json_encode($this);
	}
	
	public function getThisAll(){
		return json_encode($this);
	}
	
	public function isUser(){
		if($this->getId() > 0){ return true; } else { return false; }
	}
	
	public function getById($id){
		$id = (isset($id) && $id > 0) ? $id : 0;
		$items = parent::getById($id);
		if(isset($items[0])){
			$this->setAllData($items[0]);
		}
	}
	
	public function getByUsername($username){
		$username = (isset($username) && is_string($username)) ? $username : 'guest';
		$items = parent::getBy('username', $username);
		if(isset($items[0])){
			$this->setAllData($items[0]);
		}
	}
	
	public function setAllData($item){
		parent::setAllData($item);
		foreach($item as $k=>$v){
			if($k === 'permissions'){
				$this->permissions = new Permiso();
				$this->permissions->getById($v);
			}
		}
	}
}