<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Bundle with Popper -->
    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>

    <title>TRANSACTION REPORT</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <style>
        body{

            background-color: #ffffff;
            font-family: 'Nunito', sans-serif;
            padding-left: 5%;
            padding-right: 5%;
            padding-top: 5%;
        }

    </style>

</head>
<body>
<?php
define('DEBUG', false);

if (DEBUG) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    error_reporting(0);
}
require 'db_conf.php';
db_conn();
if (isset($_GET['page_no']) && $_GET['page_no']!="") {
    $page_no = $_GET['page_no'];
} else {
    $page_no = 1;
}
$total_records_per_page = 10;
$offset = ($page_no-1) * $total_records_per_page;
$previous_page = $page_no - 1;
$next_page = $page_no + 1;
$adjacents = "2";


$sql = "select DISTINCT SUM(order_details.qantity) as total_quantity from order_details WHERE distributor_id != '' GROUP BY purchaser_id ORDER BY total_quantity desc limit 100";

$rank_id = db_select_rank($sql);

$sql = "SELECT COUNT(*) total FROM (select count(purchaser_id) from order_details WHERE distributor_id != '' GROUP BY purchaser_id) as total";
$total_records  = db_select_one($sql);
//var_dump($resultCount);
//die();
$total_records = (int)$total_records;
$total_no_of_pages = ceil($total_records / $total_records_per_page);
$second_last = $total_no_of_pages - 1; // total pages minus 1
$sql = "select distributor_id, CONCAT(users.first_name,' ',users.last_name) as distributor, SUM(qantity) as total_sales from order_details LEFT JOIN users on order_details.distributor_id = users.id WHERE distributor_id != '' GROUP BY purchaser_id ORDER BY total_sales DESC LIMIT ".$total_records_per_page." OFFSET ".$offset." ";
//echo $offset;
//die();
$result = db_select_all($sql);
//print_r($result);
//die();
?>
<div class="">
    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped table-bordered border-white table-responsive-sm">

                <thead>
                <tr class="table-primary">
                    <th>Top</th>
                    <th>Distributor Name</th>
                    <th>Total Sales</th>
                </tr>

                </thead>

                <tbody>
                <?php
                    $zero = 0;
                    foreach ($result as $results){
                        echo "<tr>
                         <td>".isset($rank_id[$results->total_sales]) ? $rank_id[$results->total_sales] : $zero."</td>
                         <td>".$results->distributor."</td>
                         <td>$".$results->total_sales."</td>
                         </tr>";
                    }
                ?>
                </tbody>

            </table>
            <div class="row">
                <div class="col-6">
                    <div style='padding: 10px 20px 0px; border-top: dotted 1px #CCC;'>
                        <strong>Page <?php echo $page_no." of ".$total_no_of_pages; ?></strong>
                    </div>
                </div>
                <div class="col-6">
                    <nav aria-label="Page navigation example">
                        <ul class="pagination justify-content-end">
                            <?php if($page_no > 1){
                                if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
                                    $url = "https://";
                                else
                                    $url = "http://";
                                $url.= $_SERVER['HTTP_HOST'];
                                $url.= '/NAXUM_raw/index.php';
//                                echo "<li><a class='page-link' href=".$url."'>First Page</a></li>";
                            } ?>

                            <li <?php if($page_no <= 1){ echo "class='disabled'"; } ?>>
                                <a class='page-link' <?php if($page_no > 1){
                                    echo "href=".$url."'?page_no=$previous_page'";
                                } ?>>Previous</a>
                            </li>



                            <?php if($page_no < $total_no_of_pages){
                                    for ($counter = $page_no; $counter <= ($page_no+5); $counter++){
                                        if ($counter == $page_no) {
                                            echo "<li class='active'><a class='page-link' >$counter</a></li>";
                                        }else{
                                            echo "<li><a class='page-link'  href=".$url."'?page_no=$counter'>$counter</a></li>";
                                        }
                                    }
                                echo "<li><a class='page-link' >...</a></li>";

//                                echo "<li><a class='page-link' href='?page_no=$total_no_of_pages'>Last &rsaquo;&rsaquo;</a></li>";
                            } ?>

                            <li <?php if($page_no >= $total_no_of_pages){
                                echo "class='disabled'";
                            } ?>>
                                <a class='page-link' <?php if($page_no < $total_no_of_pages) {
                                    echo "href=".$url."'?page_no=$next_page'";
                                } ?>>Next</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>

            <div class="align-content-end"></div>
        </div>
    </div>
</div>
</body>
</html>
