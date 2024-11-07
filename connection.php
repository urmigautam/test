<?php
 class Connection {
    private $con;
    
    public function __construct(){
        $this->con = mysqli_connect("localhost","root","","crud1");
        if(mysqli_connect_error()){
           echo mysqli_connect_error();
        }
    }
 }

 $con = new Connection();

?>