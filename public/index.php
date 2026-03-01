<?php

require __DIR__ . '/../devtools/tools.php';
require __DIR__ . '/../data/core.php';

$view = [];
if (isset($_GET['dc']))
{
    $view = Core::GetObjectByDC($_GET['dc']);
}
else
{
    $view = Core::GetObjectById($_GET['object']);
}



VAR_DUMP($view);

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>demo</title>
  <!-- Local CSS -->
  <link href="../lib/css/tabulator_simple.css" rel="stylesheet">
</head>
<body>
   <!-- passing parameters -->
  <div style="border-left: 1px solid #ddd; border-right: 1px solid #ddd; border-top: 1px solid #ddd; padding: 0.25em; background:#f8f9fa;">
    <button id="refresh_table" style="margin-right: 0.5em;">Refresh</button>
    <label for="view">Active:</label>
    <input type="checkbox" id="active">
  </div>

  <div id="Connections" style="border: 1px solid #ddd; padding: 0.25em;"></div>

  <!-- Local JS (use tabulator.min.js for full features) -->
  <script src="../lib/js/tabulator.js"></script>
  
  <!-- If you need extras with core, include and register modules like this -->
  <!-- <script src="js/modules/edit.min.js"></script> -->
  <!-- <script>Tabulator.registerModule(EditModule);</script> -->

  <script>
  
    // This needs to be generated based on the view conviguration in qg_core
    var table = new Tabulator("#Connections", {
    //   height: "450px",  // Optional: Set a height to enable scrolling
    //   data: tabledata,
    //   layout: "fitColumns",  // Optional: Auto-fit columns
        height: "500px",
        layout: "fitColumns",
        ajaxURL: "../data/fetchdata.php",
        ajaxConfig: "GET",
        ajaxFiltering: true,           // optional – if you later want header filters too
        ajaxLoading: true,             // shows loading overlay
        ajaxLoadingError: "Failed to load connections!",

        // This function is called **every time** Tabulator makes an AJAX request
        ajaxParams: function() {
        const checkbox = document.getElementById("active");
        const in_active = checkbox?.checked ? 1 : 0; 

        return {
          in_active: in_active
        };
      },

      columns: [
        {title: "id", field: "id"},
        {title: "Name", field: "name"},
        {title: "type", field: "type"},
        {title: "active", field: "active", hozAlign: "center", formatter: "toggle",
        formatterParams: {
            size: 18,              // pixel size of switch
            onValue: true,         // value when "on"
            offValue: false,
            onTruthy: true,        // treat any truthy value as on
            onColor: "#59b0d8",    // green when on
            offColor: "#696969",   // red when off
            clickable: false,       // ← crucial: clicking cell toggles value
        }},
        {title: "host", field: "host"},
        {title: "port", field: "port"},
        {title: "database", field: "database"},
        {title: "username", field: "username"}        
      ]
    });

    // Initial load (very reliable pattern)
    table.on("tableBuilt", function(){
        table.setData();  // now safe — runs after full init
    });

    // Refresh button – just triggers reload with current params
    document.getElementById("refresh_table").addEventListener("click", function() {
      table.setData();   // ← cleanest way – re-uses ajaxParams function
    });

    // // Optional: auto-refresh when checkbox changes (very user-friendly)
    // document.getElementById("active").addEventListener("change", function() {
    //   table.setData();   // reload with new in_active value
    // });

  </script>

</body>
</html>