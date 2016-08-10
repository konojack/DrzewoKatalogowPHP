<?php
require_once('Dir.class.php');

class directoryMapper {
        
        const ROOT = 0;
        const UPDATE_TRUE = 1;
        const UPDATE_FALSE = 2;
        const ADD_TRUE = 3;
        const ADD_FALSE = 4;
        static private $_connect;
        
        
    public function __construct(){
        
        self::$_connect = self::getConnect();
        
    }
    
    
    static public function getConnect(){
        
        $connection = mysql_connect('localhost', 'root', '') or die("Poł±czenie z Twoj± baz± danych nie nast±piło poprawnie! Dlatego: ".mysql_error());
            mysql_query("SET NAMES 'latin2'");
        $db = mysql_select_db('drzewo', $connection) or exit("Nie wybrano żadnej bazy, generalnie bł±d: ".mysql_error());
        $connect = $connection;
        return $connect;
        
    }
    
    
    public function map($row){
        
        $dir = new dir($row['id'], $row['name'], $row['parent_id'], $row['priority']);
        return $dir;
    }
    
    
    public function getById($id){
        
        $query = "SELECT * FROM drzewko WHERE id = '$id'";
        $result = mysql_query($query, self::$_connect);
        $row = mysql_fetch_assoc($result);
        return $this->map($row);
        
    }
    
    
    public function getByParentIdInOrder($parent_id){
        
        $query = "SELECT * FROM drzewko WHERE parent_id = '$parent_id' ORDER BY priority ASC";
        $result = mysql_query($query, self::$_connect);
        $iterator = Array();
        
            while($row = mysql_fetch_assoc($result)){
                $iterator[$row['id']] = $this->map($row);
            }
        
        return $iterator;  
        
    }
    
    
    public function getByName($name){
        
        $query = "SELECT name FROM drzewko WHERE name = $name";
        $result = mysql_query($query, self::$_connect);
        $row = mysql_fetch_assoc($result);
        return $this->map($row);
        
    }
    
    
    public function add(dir $dir){
        
        $catalog_name = $dir->getName();
        $parent_id = $dir->getParentId();
        $priority = $dir->getPriority();
        $query = "INSERT INTO drzewko(name, parent_id, priority) VALUES('$catalog_name','$parent_id', '$priority')";
        return mysql_query($query, self::$_connect);
        
    }
    
    
    public function delete(dir $dir){
        
        $id = $dir->getId();
        $query = "DELETE FROM drzewko WHERE id = $id";
        return mysql_query($query, self::$_connect);
        
    }
    
    
    public function update(dir $dir){
        
        $id = $dir->getId();
        $catalog_name = $dir->getName();
        $parent_id = $dir->getParentId();
        $priority = $dir->getPriority();
        $query = "UPDATE drzewko SET name='$catalog_name', parent_id='$parent_id', priority='$priority' WHERE id='$id'";
        return mysql_query($query, self::$_connect);
        
    }
    
    
    public function getAllToSelectExcludeChildrens(dir $dir){
        
        $id = $dir->getId();
        $query = "SELECT DISTINCT * FROM drzewko WHERE id != '$id'";
        $result = mysql_query($query, self::$_connect);
        $iterator = Array();
        $childrens_table = $this->getAllChildrens($dir);
        $iterator_to_exclude_childrens = Array();
            
            while($row = mysql_fetch_assoc($result)){
                $mapped_row = $iterator[$row['id']] = $this->map($row);
                    if(!in_array($mapped_row, $childrens_table)){
                $iterator_to_exclude_childrens[$row['id']] = $this->map($row);
                }
                 
            }
            
        return $iterator_to_exclude_childrens;
        
    }
    
    
    
    public function getAllToSelect(){
        
        $query = "SELECT DISTINCT * FROM drzewko ORDER BY id ASC";
        $result = mysql_query($query, self::$_connect);
        $iterator = Array();
        
            while($row = mysql_fetch_assoc($result)){
                $iterator[$row['id']] = $this->map($row);
            }

        return $iterator;
    }
    
    public function getAllChildrens($dir){
        $childrens = $dir->getChildrensInOrder();
        $childs = Array();
        foreach($childrens as $children){
            $childs[$children->getId()] = $this->getById($children->getId());
            $childs = array_merge($this->getAllChildrens($children), $childs);
        }
        return $childs;
    }
    
    
    public function existName(dir $dir){
        
        $name = $dir->getName();
        $query = "SELECT name FROM drzewko WHERE name='$name'";
        $result = mysql_query($query, self::$_connect);
        return mysql_num_rows($result);
        
    }
    
        
    public function deleteNode(dir $dir){
        
        if($dir->isRoot()){
            return false;
        }
        
        if($dir->hasChildren()){
            foreach($dir->getChildrensInOrder() as $children){
                $this->deleteNode($children);
            }        
        }
        
        $this->changePriorityForSibilings($dir);
        $this->delete($dir);
        return true;
        
    }
    
    
    public function addNode(dir $dir){
        
        if($this->existName($dir)>0){
            return false;
        }else{
            $parent = $this->getById($dir->getParentId());
            $dir->setPriority($parent->getChildrenAmount()+1);
            return $this->add($dir);
        }
        
    }
    
    
    public function save(dir $dir){
        
        if($dir->isInDB()){
            if($this->updateNode($dir)){          
                return 1;
            }else{
                return 2;
            }
        }else{
            if($this->addNode($dir)){          
                return 3;
            }else{
                return 4;
            }
        }
        
    }
    
    
    public function updateNode(dir $dir){

        $clone = $this->getById($dir->getId());
        
            if($clone->getParentId() != $dir->getParentId()){
                $this->changePriorityForSibilings($clone);
            }
            
            if($clone->getParentId() == $dir->getParentId()){
                return $this->update($dir);
            }
            
        $parent = $this->getById($dir->getParentId());
        $dir->setPriority($parent->getChildrenAmount()+1);
        return $this->update($dir);
        
    }
    
    
    public function getRoot(){

       return $this->getByParentIdInOrder(self::ROOT)[1];
       
    }
    
    
    public function getByPriority($priority, $parent_id){
        
        $query = "SELECT * FROM drzewko WHERE priority = '$priority' AND parent_id = '$parent_id'";
        $result = mysql_query($query, self::$_connect);
        $row = mysql_fetch_assoc($result);
        return $this->map($row);
        
    }
    
    
    public function moveUp(dir $dir){
        
        $prev_dir = $this->getByPriority($dir->getPriority()-1, $dir->getParentId());
        $prev_dir->setPriority($dir->getPriority()); 
        $dir->setPriority($dir->getPriority()-1);
        return $this->update($dir) && $this->update($prev_dir);
        
    }
    
    
    public function moveDown(dir $dir){
        
        $prev_dir = $this->getByPriority($dir->getPriority()+1, $dir->getParentId());
        $prev_dir->setPriority($dir->getPriority()); 
        $dir->setPriority($dir->getPriority()+1);
        return $this->update($dir) && $this->update($prev_dir);
        
    }
    
    
    public function changePriorityForSibilings(dir $dir){
        
        return $this->setPriorityForSibilings($dir->getPriority(), $dir->getParentId());
        
    }
    
    
    public function setPriorityForSibilings($priority, $parent_id){
        
        $query = "UPDATE drzewko SET priority = priority-1 WHERE priority > '$priority' AND parent_id = '$parent_id'";
        return mysql_query($query, self::$_connect);
        
    }

}