<?php

require __DIR__ . '/../connections/conman.php';
require __DIR__ . '/../devtools/tools.php';

$pdo = ConMan::get();

// Plain query
$stmt = $pdo->prepare('SELECT * FROM Connections');
$stmt->execute();
$data = json_encode($stmt->fetchAll());

//echo $data

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
  <input type="hidden" id="table-data" value="<?= htmlspecialchars($data, ENT_QUOTES) ?>">

  <!-- passing parameters -->
  <div style="margin-bottom: 15px; padding: 10px; background:#f8f9fa; border:1px solid #ddd;">
    <label>Start Date:</label>
    <input type="date" id="startDate">
  
    <label>End Date:</label>
    <input type="date" id="endDate">
  
    <label>Status:</label>
    <select id="status">
      <option value="">All</option>
      <option value="active">Active</option>
      <option value="pending">Pending</option>
      <option value="cancelled">Cancelled</option>
    </select>
  
    <label>Search name:</label>
    <input type="text" id="searchName" placeholder="e.g. John">
  
    <button id="loadTable">Load / Refresh</button>
  </div>
  <div id="Connections" style="border: 1px solid black; padding: 0.25em;"></div>

  <!-- Local JS (use tabulator.min.js for full features) -->
  <script src="../lib/js/tabulator.js"></script>
  
  <!-- If you need extras with core, include and register modules like this -->
  <!-- <script src="js/modules/edit.min.js"></script> -->
  <!-- <script>Tabulator.registerModule(EditModule);</script> -->

  <script>
    // this is now automated as long as the query returns the columns expected
    var tabledata = JSON.parse(document.getElementById('table-data').value);
    
    // This needs to be generated based on the view conviguration in qg_core
    var table = new Tabulator("#Connections", {
      height: "450px",  // Optional: Set a height to enable scrolling
      data: tabledata,
      layout: "fitColumns",  // Optional: Auto-fit columns
      columns: [
        {title: "Id", field: "Id"},
        {title: "Name", field: "Name"},
        {title: "Type", field: "Type"},
        {title: "Is Active", field: "IsActive"}
      ]
    });
  </script>

</body>
</html>