<?php 
error_reporting(E_ALL);
ini_set('display_errors', '1');
?>
<?php 
include "storescripts/connect_to_mysql.php"; 
$dynamicList = "";
$sql = mysqli_query($db_connection,"SELECT * FROM products ORDER BY RAND() LIMIT 3");
$productCount = mysqli_num_rows($sql);
if ($productCount > 0) {
	while($row = mysqli_fetch_array($sql)){ 
             $id = $row["id"];
			 $product_name = $row["product_name"];
			 $price = $row["price"];
			 $date_added = strftime("%b %d, %Y", strtotime($row["date_added"]));
			 $dynamicList .= '<table width="100%" border="0" cellspacing="0" cellpadding="6">
        <tr>
          <td width="17%" valign="top"><a href="product.php?id=' . $id . '"><img style="border:#666 1px solid;" src="inventory_images/' . $id . '.jpg" alt="' . $product_name . '" width="77" height="102" border="1" /></a></td>
          <td width="83%" valign="top">' . $product_name . '<br />
            $' . $price . '<br />
            <a href="product.php?id=' . $id . '">View Product Details</a></td>
        </tr>
      </table>';
    }
} else {
	$dynamicList = "We have no products listed in our store yet";
}
mysqli_close($db_connection);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Panda Home Page</title>
<link rel="stylesheet" href="style/style.css" type="text/css" media="screen" />
<link href="style/panda.png" rel="icon">
</head>
<body>
<div align="center" id="mainWrapper">
  <?php include_once("template_header.php");?>
  <div id="pageContent">
  <table width="100%" border="0" cellspacing="0" cellpadding="10">
  <tr>
    <td width="32%" valign="top"><h3>Hi there! Welcome to Panda</h3>
      <p>An Online shopping website from the earth's biggest selection of fashion men's clothing & just about anything else.<br />
    </td>
    <td width="35%" valign="top"><h3>Latest Designer Fashions</h3>
      <p><?php echo $dynamicList; ?><br />
        </p>
      <p><br />
      </p></td>
    <td width="33%" valign="top"><h3>About our Safe Technology</h3>
      <p>Panda secure socket layer (SSL) software is the industry standard and among the best software available today for secure commerce transactions. It encrypts your credit card number so that it cannot be read as the information travels over the Internet.</td>
  </tr>
</table>

  </div>
  <?php include_once("template_footer.php");?>
</div>
</body>
</html>