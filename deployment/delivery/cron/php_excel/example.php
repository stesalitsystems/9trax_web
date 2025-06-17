<?php
error_reporting(E_ALL ^ E_NOTICE);
require_once 'excel_reader2.php';
$xls = new Spreadsheet_Excel_Reader("test.xls");
echo($xls->rowcount());echo " ".$xls->colcount();//exit;
?>
<html>
<head>
<style>
table.excel {
	border-style:ridge;
	border-width:1;
	border-collapse:collapse;
	font-family:sans-serif;
	font-size:12px;
}
table.excel thead th, table.excel tbody th {
	background:#CCCCCC;
	border-style:ridge;
	border-width:1;
	text-align: center;
	vertical-align:bottom;
}
table.excel tbody th {
	text-align:center;
	width:20px;
}
table.excel tbody td {
	vertical-align:bottom;
}
table.excel tbody td {
    padding: 0 3px;
	border: 1px solid #EEEEEE;
}
</style>
</head>

<body>
<br><br>

<table border="1">
<?php for ($row=1;$row<=$xls->rowcount();$row++) { ?>
	<tr>
	<?php for ($col=1;$col<=$xls->colcount();$col++) {	?>
		<td><?= $xls->val($row,$col) ?>&nbsp;
		</td>
	<?php } ?>
	</tr>
<?php } ?>
</table>
<?php //echo $data->dump(true,true); ?>
</body>
</html>
