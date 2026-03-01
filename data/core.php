<?php
require __DIR__ . '/../connections/conman.php';

class Core
{
    private static $pdoCore;
    private static $pdoApp;
    private static $objectTypeId;
    private static $coreObject;
    private static $objectId;
    private static $objectInfo = [];
    private static $objectTypeInfo = [];
    private static $objectConnectionInfo = [];
    

    // initiate building by display code
    public static function GetObjectByDC(string $displayCode)
    {
        self::$pdoCore = ConMan::getCore();
        Core::GetObjectInfoByDC($displayCode);
        Core::GetObjectTypeInfo(self::$objectInfo);
        Core::GetObjectConnectionInfo(self::$objectTypeInfo);
        
        return self::$objectConnectionInfo;
    }

    // initiate building by id
    public static function GetObjectById(string $objectId)
    {
        self::$pdoCore = ConMan::getCore();
        Core::GetObjectInfoById($objectId);
        Core::GetObjectTypeInfo(self::$objectInfo);
        Core::GetObjectConnectionInfo(self::$objectTypeInfo);
        
        return self::$objectConnectionInfo;
    }

    // get object information based on display code
    private static function GetObjectInfoByDC(string $displayCode)
    {
        $stmt = self::$pdoCore->prepare("SELECT id, `name`, objecttypeid, displaycode, parentid, core FROM Objects WHERE displaycode = :displaycode");
        $stmt->bindValue(':displaycode', $displayCode, PDO::PARAM_STR);
        $stmt->execute();

        self::$objectInfo = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // get object information based on object id
    private static function GetObjectInfoById(string $objectId)
    {
        $stmt = self::$pdoCore->prepare("SELECT id, `name`, objecttypeid, displaycode, parentid, core FROM Objects WHERE id = :objectId");
        $stmt->bindValue(':objectId', $objectId, PDO::PARAM_STR);
        $stmt->execute();

        self::$objectInfo = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // provide type information depending on object type
    private static function GetObjectTypeInfo(array $objectInfo)
    {       
        
        self::$objectTypeId = $objectInfo[0]["objecttypeid"];
        self::$coreObject = $objectInfo[0]["core"];
        self::$objectId = $objectInfo[0]["id"];
        

        if (self::$objectTypeId === 1) //view
        {
            $stmt = self::$pdoCore->prepare("SELECT objectid, connectionid, displayname, `procedure`, displayparameters FROM views WHERE objectid = :objectid");
            $stmt->bindValue(':objectid', self::$objectId, PDO::PARAM_STR);
            $stmt->execute();
            
            self::$objectTypeInfo = $stmt->fetchAll(PDO::FETCH_ASSOC);;
        }
        
        if (self::$objectTypeId === 2) //function
        {
            $stmt = self::$pdoCore->prepare("SELECT objectid, connectionid, `procedure`, displayname, active FROM functions WHERE objectid = :objectid");
            $stmt->bindValue(':objectid', self::$objectId, PDO::PARAM_STR);
            $stmt->execute();
            
            self::$objectTypeInfo = $stmt->fetchAll(PDO::FETCH_ASSOC);; 
        }

        if (self::$objectTypeId === 3) //column
        {
            $stmt = self::$pdoCore->prepare("SELECT objectid, displayname, `position`, visible FROM columns WHERE objectid = :objectid");
            $stmt->bindValue(':objectid', self::$objectId, PDO::PARAM_STR);
            $stmt->execute();
            
            self::$objectTypeInfo = $stmt->fetchAll(PDO::FETCH_ASSOC);; 
        } 

         if (self::$objectTypeId === 4) //parameter
        {
            $stmt = self::$pdoCore->prepare("SELECT objectid, displayname, `position`, defaultvalue, visible FROM parameter WHERE objectid = :objectid");
            $stmt->bindValue(':objectid', self::$objectId, PDO::PARAM_STR);
            $stmt->execute();
            
            self::$objectTypeInfo = $stmt->fetchAll(PDO::FETCH_ASSOC);; 
        } 
    }

    // gets connection ifnormation if available for the object type and object id not a core object
    private static function GetObjectConnectionInfo(array $objectTypeInfo)
    {
        $validTypes = [1, 2]; // only views, functions for now (need to expand for lists, validationss, calculations, locks, visibles)

        if (in_array(self::$objectTypeId, $validTypes, true) && self::$coreObject === 0)
        {
            $connectionId = $objectTypeInfo[0]["connectionid"];

            $stmt = self::$pdoCore->prepare("SELECT id, `name`, `type`, active, host, port, `database`, username, `password` FROM connections WHERE id = :connectionId");
            $stmt->bindValue(':connectionId', $connectionId, PDO::PARAM_STR);
            $stmt->execute();
            
            self::$objectConnectionInfo = $stmt->fetchAll(PDO::FETCH_ASSOC);

            self::$pdoApp = ConMan::getApp(self::$objectConnectionInfo[0]["host"], self::$objectConnectionInfo[0]["port"], self::$objectConnectionInfo[0]["database"], self::$objectConnectionInfo[0]["username"], self::$objectConnectionInfo[0]["password"]); 
        }
    }

    


}
