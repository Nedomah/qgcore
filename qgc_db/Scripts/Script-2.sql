SELECT 
    PARAMETER_NAME,
    DATA_TYPE
FROM 
    INFORMATION_SCHEMA.PARAMETERS
WHERE 
    SPECIFIC_SCHEMA = 'qg_core'          -- or replace with your database name
    AND SPECIFIC_NAME   = 'stp_connections_select'
    AND PARAMETER_MODE  = 'IN'            -- only input parameters
    AND PARAMETER_NAME  IS NOT NULL        -- exclude the function RETURN row (if any)
ORDER BY 
    ORDINAL_POSITION;

SELECT id, `name`, objecttypeid, displaycode, parentid, core FROM Objects;
SELECT objectid, connectionid, `procedure`, displayname, displayparameters FROM views where objectid = '019ca971-499f-7384-8ba6-0a139e96978b';
SELECT objectid, connectionid, `procedure`, displayname, active FROM functions;

SELECT objectid, displayname, `position`, visible FROM qg_core.`columns`;
SELECT objectid, displayname, `position`, defaultvalue, visible FROM qg_core.parameters;

SHOW FULL PROCESSLIST;