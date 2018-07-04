<?php
   include('../function/function_basic.php');
   if(isset($_GET['select_mac']))
   {
      $select_mac = $_GET['select_mac'];
      $result_query = $connect_mysqli->query("SELECT auth
                                             FROM  quanlythietbi 
                                             WHERE mac = '$select_mac'
                                                ");
      if(mysqli_num_rows($result_query) > 0)
      {
         $result = mysqli_fetch_array($result_query);
         echo $result['auth'];
      }
   }
?>