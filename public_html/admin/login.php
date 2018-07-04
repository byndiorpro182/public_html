<?php
    include('../function/function_basic.php');
    $message = "***";

    if (isset($_GET['submit']) && $_GET['submit'] = "logout")
    {
        setcookie("mac", "", time() - 10, "/admin","hoangvanhieu182.000webhostapp.com", 0);
    }
    else
    {
        if( isset($_COOKIE["mac"]))
        {
            $mac = $_COOKIE["mac"];
            if (strlen($mac) == 17)
            {
                $result_query = $connect_mysqli->query("SELECT id, auth
                                                        FROM  hardware 
                                                        WHERE mac='$mac'
                                                        ");
                if ( mysqli_num_rows($result_query) > 0 )
                {
                    $result = mysqli_fetch_array($result_query);
                    $auth = $result['auth'];
                    $ch = curl_init();

                    curl_setopt($ch, CURLOPT_URL, "http://http://188.166.206.43/".$auth."/isHardwareConnected");
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                    curl_setopt($ch, CURLOPT_HEADER, FALSE);
                    $response = curl_exec($ch);
                    curl_close($ch);
                    if($response != "Invalid token.")
                    {
                        header("Location:  ../admin/control.php");
                        exit();
                    }
                    else
                    {
                        $message = "BUG AUTH : Liên hệ";
                    }
                }
            }else{
                $result_query = $connect_mysqli->query("SELECT pass
                                                        FROM  user 
                                                        WHERE user = 'admin'
                                                        ");
                $result = mysqli_fetch_array($result_query);
                if($mac == $result['pass']){
                    header("Location: admin.php");
                    exit();
                }
            }
        }
    }
    if (isset($_POST["mac"]))
    {
        $mac = $_POST["mac"];
        if (strlen($mac) == 17)
        {
            $result_query = $connect_mysqli->query("SELECT id , auth
                                                    FROM  hardware
                                                    WHERE mac='$mac'
                                                    ");
            if ( mysqli_num_rows($result_query) > 0 )
            {    
                $result = mysqli_fetch_array($result_query);
                $auth = $result['auth'];     
                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, "http://http://188.166.206.43/".$auth."/isHardwareConnected");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                $response = curl_exec($ch);
                curl_close($ch);
                if ($response != "Invalid token.")
                {
                    setcookie("mac", "$mac", time()+15552000, "/admin","hoangvanhieu182.000webhostapp.com", 0);
                    header("Location:  ../admin/control.php");
                    exit();
                }
                else
                {
                    $message = "BUG AUTH : Liên hệ ...";
                }
                
            }
            else
            {
                $message = "Mã MAC không tồn tại";
            }
        }
        else
        {
            $result_query = $connect_mysqli->query("SELECT pass
                                                    FROM  user 
                                                    WHERE user = 'admin'
                                                    ");
            $result = mysqli_fetch_array($result_query);
            if($mac == $result['pass']){
                setcookie("mac", "$mac", time() + 86400, "/admin","hoangvanhieu182.000webhostapp.com", 0);
                header("Location: admin.php");
                exit();
            }else{
                $message = "Mã MAC không tồn tại";  
            }   
        }
    }
?>




<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>B182</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">

    <link href="css/sb-admin.css" rel="stylesheet">

    <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">


</head>

<body>

    <div id="wrapper">

        <!-- Navigation -->
        <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <a class="navbar-brand" href="login.php"> BINARY182 </a>
            </div>
        <!-- không đóng thẻ nav -->
        <div id="page-wrapper">

            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <form action="login.php" method='POST'>                         
                            <br /><br />
                            <h2> Đăng nhập </h2><br /><br />
                            <div class="form-group input-group">
                                <span class="input-group-addon">MAC</span>
                                <input type="text" class="form-control" placeholder="5E:CF:7F:C1:02:80" name="mac">
                            </div><br />
                            <div align="center">
                                <button type="submit" class="btn btn-primary"> Đăng nhập </button>
                            </div><br /><br /><?php echo $message; ?><br /><br />
                            <?php
                                $result_query = $connect_mysqli->query("SELECT link_app
                                                                        FROM app
                                                                        WHERE id=1
                                                                        ");
                                $result = mysqli_fetch_array($result_query);   
                            ?>
                            <center><a href="<?php echo $result['link_app'];?>">Tải app cho android</a></center><br><br>
                            <center><a href="https://drive.google.com/open?id=1vb8M4EUfSOO07Rt_a0kkUtKuIcsZb4Ks">Hướng dẫn</a></center><br><br>
                            <center><a href="https://www.facebook.com/Binary182-324906648033328/">Liên hệ, trợ giúp, góp ý,...</a></center>
                            <br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />  

                        </form>
                    </div>                
                </div>
            </div>
        </div>
    </div>
    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>

</html>



