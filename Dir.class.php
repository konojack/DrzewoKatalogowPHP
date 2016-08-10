<?php

require_once('DirectoryMapper.class.php');
    
    class dir {
    
    private $_id;
    private $_name;
    private $_parent_id; 
    private $_priority;
    
    
    public function __construct($id, $name, $parent_id, $priority=0){
        
        $this->_id = (int) $id;
        $this->_name = (string) $name;
        $this->_parent_id = (int) $parent_id;
        $this->_priority = (int) $priority;
        
    }
    
    
    public function getId(){
        
        return $this->_id;
        
    }
    
    
    public function setId($id){
        
        $this->_id = (int) $id;
        
    }
    
    
    public function getName(){
        
        return $this->_name;
        
    }
    
    
    public function setName($name){
        
        $this->_name = (string) $name;
        
    }
    
    
    public function getParentId(){
        
        return $this->_parent_id;
        
    }
    
    
    public function setParentId($parent_id){
        
        $this->_parent_id = (int) $parent_id;
        
    }
    
    
    public function getPriority(){
        
        return $this->_priority;
        
    }
    
    
    public function setPriority($priority){
        
        $this->_priority = (int) $priority;
        
    }
    
    
    public function hasChildren(){
        
        return (bool) $this->getChildrenAmount();
        
    }
    
    
    public function getChildrensInOrder(){
        
        $mapper = new directoryMapper();
        return $mapper->getByParentIdInOrder($this->getId());
        
    }
    
    
    public function isRoot(){
        
        return $this->getId()==1;
        
    }
    
            
    public function isInDB(){
        
        return $this->getId();
        
    }
    
    
    public function getChildrenAmount(){
        
        $mapper = new directoryMapper();
        return count($mapper->getByParentIdInOrder($this->getId()));
        
    }
    
    
    public function getParent(){
        
        $mapper = new directoryMapper();
        return $mapper->getById($this->getParentId());
        
    }

}