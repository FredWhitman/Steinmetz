<?php
//database
    define('DB_HOST', 'localhost');
    define('DB_USERNAME', 'root');
    define('DB_PASSWORD', '');
    define('DB_NAME', 'inventory_db');

        
    Function getLotChange($date1,$date2,$material)
    {   
        //get connection
        $mysqli = new mysqli(DB_HOST,DB_USERNAME,DB_PASSWORD,DB_NAME);
        
        if(!$mysqli)
        {
            die("Connection failed: " .$mysqli->error);
        }
        
        //execute query
        $result =$mysqli->query("SELECT MaterialName,ChangeDate,OldLot,NewLot,Comments"
            . " From Lotchange"
            . " where ChangeDate BETWEEN '" . $date1 . "' AND '" . $date2 ."' AND MaterialName = '" . $material . "'");
       
        return $result;
    }

    Function getMaterialNames()
    {
        //get connection
        $mysqli = new mysqli(DB_HOST,DB_USERNAME,DB_PASSWORD,DB_NAME);
        if(!$mysqli)
        {
            die("Connection failed: " .$mysqli->error);
        }
        
        //execute query
        $result =$mysqli->query("SELECT MaterialName FROM Material");
    
        return $result;
    }

    Function getPartNumber()
    {
        //get connection
        $mysqli = new mysqli(DB_HOST,DB_USERNAME,DB_PASSWORD,DB_NAME);
        if(!$mysqli)
        {
            die("Connection failed: " .$mysqli->error);
        }

        //execute query
        $result =$mysqli->query("SELECT PartName FROM  Products  Where customer = 'amsted'");
        
        return $result;
    }

