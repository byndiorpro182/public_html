<?php
	include('../function/function_basic.php');

    if (isset($_GET['submit']) && $_GET['submit'] = "logout")
    {
        setcookie("user_name", "", time() - 10, "/admin","hoangvanhieu182.000webhostapp.com", 0);
        setcookie("user_pass", "", time() - 10, "/admin","hoangvanhieu182.000webhostapp.com", 0);
        header( "Location:  ../admin/login.php");
        exit();   
    }
    if(!isset($_COOKIE["user_name"]) || !isset($_COOKIE["user_pass"]))
    {
        header( "Location:  ../admin/login.php");
        exit();
    }
    else
    {
        $user_name = $_COOKIE["user_name"];
        $user_pass = $_COOKIE["user_pass"];
        if (preg_match("/^[a-z0-9]*$/",$user_name) && preg_match("/^[a-z0-9]*$/",$user_pass))
        {
            $result_query = $connect_mysqli->query("SELECT id , mac, auth
                                                    FROM  quanlythietbi 
                                                    WHERE user_name='$user_name' AND user_pass='$user_pass'
                                                    ");
            if ( mysqli_num_rows($result_query) == 0)
            {
                header( "Location:  ../admin/login.php");
                exit();
            }
        }
        else
        {
            header( "Location:  ../admin/login.php");
            exit();
        }
    }
    // Xử lý dữ liệu
    if (isset($_POST['mac']))
    {
        $mac = $_POST['mac'];
    }
    if ( isset($_GET['mac']))
    {
        $mac = $_GET['mac'];
    }
    if (isset($_POST['update']) && $_POST['update'] == "update" && strlen($mac) == 17)
    {
        $result_query = $connect_mysqli->query("SELECT id
                                                FROM  quanlythietbi 
                                                WHERE mac = '$mac'
                                                ");
        if ( mysqli_num_rows($result_query) != 0)
        {
            $SET_SQL = "";
            $auth = $_POST['auth'];
            $user_name = $_POST['user_name'];
            $user_pass = $_POST['user_pass'];
            $ap_ssid = $_POST['ap_ssid'];
            $ap_pass = $_POST['ap_pass'];
            $ssid_now = $_POST['ssid_now'];
            $pass_now = $_POST['pass_now'];
            $ssid1 = $_POST['ssid1'];
            $ssid2 = $_POST['ssid2'];
            $ssid3 = $_POST['ssid3'];
            $ssid4 = $_POST['ssid4'];
            $ssid5 = $_POST['ssid5'];
            $pass1 = $_POST['pass1'];
            $pass2 = $_POST['pass2'];
            $pass3 = $_POST['pass3'];
            $pass4 = $_POST['pass4'];
            $pass5 = $_POST['pass5'];
            $local_ip = $_POST['local_ip1']*16777216 + $_POST['local_ip2']*65536 + $_POST['local_ip3']*256 + $_POST['local_ip4'];
            $gateway = $_POST['gateway1']*16777216 + $_POST['gateway2']*65536 + $_POST['gateway3']*256 + $_POST['gateway4'];
            $subnet = $_POST['subnet1']*16777216 + $_POST['subnet2']*65536 + $_POST['subnet3']*256 + $_POST['subnet4'];
            $ota_update = $_POST['ota_update'];
            $message = $_POST['message'];
            $reply = $_POST['reply'];
            if ($_POST['auth'])
            {
                $SET_SQL = "$SET_SQL auth = '$auth',";
            }
            if ($_POST['user_name'])
            {
                $SET_SQL = "$SET_SQL user_name = '$user_name',";
            }
            if($_POST['user_pass'])
            {
                $SET_SQL = "$SET_SQL user_pass = '$user_pass',";
            }
            if ($_POST['ap_ssid'])
            {
                $SET_SQL = "$SET_SQL ap_ssid = '$ap_ssid',";
            }
            if ($_POST['ap_pass'])
            {
                $SET_SQL = "$SET_SQL ap_pass = '$ap_pass',";
            }
            if ($_POST['ssid_now'])
            {
                $SET_SQL = "$SET_SQL ssid_now = '$ssid_now',";
            }
            if ($_POST['pass_now'])
            {
                $SET_SQL = "$SET_SQL pass_now = '$pass_now',";
            }
            if ($_POST['ssid1'])
            {
                $SET_SQL = "$SET_SQL ssid1 = '$ssid1', ";
            }
            if ($_POST['ssid2'])
            {
                $SET_SQL = "$SET_SQL ssid2 = '$ssid2', ";
            }
            if ($_POST['ssid3'])
            {
                $SET_SQL = "$SET_SQL ssid3 = '$ssid3', ";
            }
            if ($_POST['ssid4'])
            {
                $SET_SQL = "$SET_SQL ssid4 = '$ssid4', ";
            }
            if ($_POST['ssid5'])
            {
                $SET_SQL = "$SET_SQL ssid5 = '$ssid5', ";
            }
            if ($_POST['pass1'])
            {
                $SET_SQL = "$SET_SQL pass1 = '$pass1', ";
            }
            if ($_POST['pass2'])
            {
                $SET_SQL = "$SET_SQL pass2 = '$pass2', ";
            }
            if ($_POST['pass3'])
            {
                $SET_SQL = "$SET_SQL pass3 = '$pass3', ";
            }
            if ($_POST['pass4'])
            {
                $SET_SQL = "$SET_SQL pass4 = '$pass4', ";
            }
            if ($_POST['pass5'])
            {
                $SET_SQL = "$SET_SQL pass5 = '$pass5', ";
            }
            if ($_POST['local_ip1'])
            {
                $SET_SQL = "$SET_SQL local_ip = '$local_ip', ";
            }
            if ($_POST['gateway1'])
            {
                $SET_SQL = "$SET_SQL gateway = '$gateway', ";
            }
            if ($_POST['subnet1'])
            {
                $SET_SQL = "$SET_SQL subnet = '$subnet', ";
            }
            if($_POST['ota_update'])
            {
                $SET_SQL = "$SET_SQL ota_update = '$ota_update', ";
            }
            if($_POST['message'])
            {
                $SET_SQL = "$SET_SQL message = '$message', ";
            }
            if($_POST['reply'])
            {
                $SET_SQL = "$SET_SQL reply = '$reply', ";
            }
            $SET_SQL = "$SET_SQL mac = '$mac'";
            $result_query = $connect_mysqli->query("UPDATE quanlythietbi 
                                                    SET $SET_SQL
                                                    WHERE mac = '$mac'
                                                    ");
            if($result_query)
            {
                $notification = "Update thành công"; 
            }
            else
            {
                $notification = "Update thất bại";
            }
        }   
    }
?>

<?php include('include/top.php'); ?>

<div class="row">
    <div class="col-lg-12">
        <ol class="breadcrumb">
            <li>
                <i class="fa fa-dashboard"></i>  <a href="../admin/admin.php">Admin</a>
            </li>
            <li class="active">
                <i class="fa fa-file"></i> <a href="../admin/hardware.php">Hardware</a>
            </li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <?php
            if (isset($_GET['mac']))
            {
                $mac = $_GET['mac'];
                $result_query = $connect_mysqli->query("SELECT *
                                                        FROM  quanlythietbi
                                                        WHERE mac = '$mac'");
                if( mysqli_num_rows($result_query) != 0)
                {
                    $result = mysqli_fetch_array($result_query);
                }
            }
        ?>
        <form role="form" action='/admin/hardware.php' method='GET'>
            <h3>
                <input class="form-control" name="mac" value="<?php echo $mac;?>"> <br />
                <button type="submit" class="btn btn-default" name='get_data' value="get_data"> GET DATA </button>
                <button type="submit" class="btn btn-default" name='update' value="update"> UPDATE </button>
            </h3><br />
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>NAME</th>
                            <th>VALUE</th>
                            <th>EDIT</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>start_time</th>
                            <th><?php echo $result['start_time']; ?></th>
                            <th><input class="form-control" name="start_time" placeholder="<?php echo $result['start_time']; ?>" disabled></th>           
                        </tr>
                        <tr>
                            <th>auth</th>
                            <th><?php echo $result['auth']; ?></th>
                            <th><input class="form-control" name="auth"></th>
                        </tr>
                        <tr>
                            <th>user_name</th>
                            <th><?php echo $result['user_name']; ?></th>
                            <th><input class="form-control" name="user_name"></th>
                        </tr>
                        <tr>
                            <th>user_pass</th>
                            <th><?php echo $result['user_pass']; ?></th>
                            <th><input class="form-control" name="user_pass"></th>
                        </tr>
                        <tr>
                            <th>ap_ssid</th>
                            <th><?php echo $result['ap_ssid']; ?></th>
                            <th><input class="form-control" name="ap_ssid"></th>
                        </tr>
                        <tr>
                            <th>ap_pass</th>
                            <th><?php echo $result['ap_pass']; ?></th>
                            <th><input class="form-control" name="ap_pass"></th>
                        </tr>
                        <tr>
                            <th>ssid_now</th>
                            <th><?php echo $result['ssid_now']; ?></th>
                            <th><input class="form-control" name="ssid_now"></th>
                        </tr>
                        <tr>
                            <th>pass_now</th>
                            <th><?php echo $result['pass_now']; ?></th>
                            <th><input class="form-control" name="pass_now"></th>
                        </tr>
                        <tr>
                            <th>ssid1</th>
                            <th><?php echo $result['ssid1']; ?></th>
                            <th><input class="form-control" name="ssid1"></th>
                        </tr>
                        <tr>
                            <th>ssid2</th>
                            <th><?php echo $result['ssid2']; ?></th>
                            <th><input class="form-control" name="ssid2"></th>
                        </tr>
                        <tr>
                            <th>ssid3</th>
                            <th><?php echo $result['ssid3']; ?></th>
                            <th><input class="form-control" name="ssid3"></th>
                        </tr>
                        <tr>
                            <th>ssi4</th>
                            <th><?php echo $result['ssid4']; ?></th>
                            <th><input class="form-control" name="ssid4"></th>
                        </tr>
                        <tr>
                            <th>ssid5</th>
                            <th><?php echo $result['ssid5']; ?></th>
                            <th><input class="form-control" name="ssid5"></th>
                        </tr>
                        <tr>
                            <th>pass1</th>
                            <th><?php echo $result['pass1']; ?></th>
                            <th><input class="form-control" name="pass1"></th>
                        </tr>
                        <tr>
                            <th>pass2</th>
                            <th><?php echo $result['pass2']; ?></th>
                            <th><input class="form-control" name="pass2"></th>
                        </tr>
                        <tr>
                            <th>pass3</th>
                            <th><?php echo $result['pass3']; ?></th>
                            <th><input class="form-control" name="pass3"></th>
                        </tr>
                        <tr>
                            <th>pass4</th>
                            <th><?php echo $result['pass4']; ?></th>
                            <th><input class="form-control" name="pass4"></th>
                        </tr>
                        <tr>
                            <th>pass5</th>
                            <th><?php echo $result['pass5']; ?></th>
                            <th><input class="form-control" name="pass5"></th>
                        </tr>
                        <tr>
                            <th>local_ip</th>
                            <th>
                                <?php
                                    $buf = $result['local_ip'];
                                    $ip1 = (int)($buf/16777216);
                                    $ip2 = (int)(($buf - (int)$ip1*16777216)/65536);
                                    $ip3 = (int)(($buf - (int)($ip1*16777216) - (int)($ip2*65536))/256);
                                    $ip4 = (int)(($buf - (int)($ip1*16777216) - (int)($ip2*65536) - (int)($ip3*256)));
                                    if ( $ip1 >= 0 && $ip1 <= 255){
                                        if ( $ip2 >= 0 && $ip2 <= 255){
                                            if ( $ip3 >= 0 && $ip3 <= 255){
                                                if ( $ip4 >= 0 && $ip4 <= 255){
                                                    echo "$ip1.$ip2.$ip3.$ip4";
                                                }
                                            }
                                        }
                                    }
                                ?>
                            </th>
                            <th>
                                    <select name="local_ip1">
                                        <?php for ($i = 0; $i < 256; $i++)
                                        {
                                            if ($i == $ip1)
                                            {
                                                echo "<option selected ='$i'>$i</option>";  
                                            }
                                            else
                                            {
                                                echo "<option>$i</option>";
                                            }
                                                
                                        }
                                        ?>
                                    </select>.
                                    <select name=local_ip2>
                                        <?php for ($i = 0; $i < 256; $i++)
                                        {
                                            if ($i == $ip2)
                                            {
                                                echo "<option selected ='$i'>$i</option>";  
                                            }
                                            else
                                            {
                                                echo "<option>$i</option>";
                                            }
                                                
                                        }
                                        ?>
                                    </select>.
                                    <select name="local_ip3">
                                        <?php for ($i = 0; $i < 256; $i++)
                                        {
                                            if ($i == $ip3)
                                            {
                                                echo "<option selected ='$i'>$i</option>";  
                                            }
                                            else
                                            {
                                                echo "<option>$i</option>";
                                            }
                                                
                                        }
                                        ?>
                                    </select>.
                                    <select name="local_ip4">
                                        <?php for ($i = 0; $i < 256; $i++)
                                        {
                                            if ($i == $ip4)
                                            {
                                                echo "<option selected ='$i'>$i</option>";  
                                            }
                                            else
                                            {
                                                echo "<option>$i</option>";
                                            }
                                                
                                        }
                                        ?>
                                    </select>
                            </th>
                        </tr>
                        <tr>
                            <th>gateway</th>
                            <th>
                                <?php
                                    $buf = $result['gateway'];
                                    $ip1 = (int)($buf/16777216);
                                    $ip2 = (int)(($buf - (int)$ip1*16777216)/65536);
                                    $ip3 = (int)(($buf - (int)($ip1*16777216) - (int)($ip2*65536))/256);
                                    $ip4 = (int)(($buf - (int)($ip1*16777216) - (int)($ip2*65536) - (int)($ip3*256)));
                                    if ( $ip1 >= 0 && $ip1 <= 255){
                                        if ( $ip2 >= 0 && $ip2 <= 255){
                                            if ( $ip3 >= 0 && $ip3 <= 255){
                                                if ( $ip4 >= 0 && $ip4 <= 255){
                                                    echo "$ip1.$ip2.$ip3.$ip4";
                                                }
                                            }
                                        }
                                    }
                                ?>
                            </th>
                            <th>
                                    <select name="gateway1">
                                        <?php for ($i = 0; $i < 256; $i++)
                                        {
                                            if ($i == $ip1)
                                            {
                                                echo "<option selected ='$i'>$i</option>";  
                                            }
                                            else
                                            {
                                                echo "<option>$i</option>";
                                            }
                                                
                                        }
                                        ?>
                                    </select>.
                                    <select name=gateway2>
                                        <?php for ($i = 0; $i < 256; $i++)
                                        {
                                            if ($i == $ip2)
                                            {
                                                echo "<option selected ='$i'>$i</option>";  
                                            }
                                            else
                                            {
                                                echo "<option>$i</option>";
                                            }
                                                
                                        }
                                        ?>
                                    </select>.
                                    <select name="gateway3">
                                        <?php for ($i = 0; $i < 256; $i++)
                                        {
                                            if ($i == $ip3)
                                            {
                                                echo "<option selected ='$i'>$i</option>";  
                                            }
                                            else
                                            {
                                                echo "<option>$i</option>";
                                            }
                                                
                                        }
                                        ?>
                                    </select>.
                                    <select name="gateway4">
                                        <?php for ($i = 0; $i < 256; $i++)
                                        {
                                            if ($i == $ip4)
                                            {
                                                echo "<option selected ='$i'>$i</option>";  
                                            }
                                            else
                                            {
                                                echo "<option>$i</option>";
                                            }
                                                
                                        }
                                        ?>
                                    </select>
                            </th>
                        </tr>
                        <tr>
                            <th>subnet</th>
                            <th>
                                <?php
                                    $buf = $result['subnet'];
                                    $ip1 = (int)($buf/16777216);
                                    $ip2 = (int)(($buf - (int)$ip1*16777216)/65536);
                                    $ip3 = (int)(($buf - (int)($ip1*16777216) - (int)($ip2*65536))/256);
                                    $ip4 = (int)(($buf - (int)($ip1*16777216) - (int)($ip2*65536) - (int)($ip3*256)));
                                    if ( $ip1 >= 0 && $ip1 <= 255){
                                        if ( $ip2 >= 0 && $ip2 <= 255){
                                            if ( $ip3 >= 0 && $ip3 <= 255){
                                                if ( $ip4 >= 0 && $ip4 <= 255){
                                                    echo "$ip1.$ip2.$ip3.$ip4";
                                                }
                                            }
                                        }
                                    }
                                ?>
                            </th>
                            <th>
                                    <select name="subnet1">
                                        <?php for ($i = 0; $i < 256; $i++)
                                        {
                                            if ($i == $ip1)
                                            {
                                                echo "<option selected ='$i'>$i</option>";  
                                            }
                                            else
                                            {
                                                echo "<option>$i</option>";
                                            }
                                                
                                        }
                                        ?>
                                    </select>.
                                    <select name=subnet2>
                                        <?php for ($i = 0; $i < 256; $i++)
                                        {
                                            if ($i == $ip2)
                                            {
                                                echo "<option selected ='$i'>$i</option>";  
                                            }
                                            else
                                            {
                                                echo "<option>$i</option>";
                                            }
                                                
                                        }
                                        ?>
                                    </select>.
                                    <select name="subnet3">
                                        <?php for ($i = 0; $i < 256; $i++)
                                        {
                                            if ($i == $ip3)
                                            {
                                                echo "<option selected ='$i'>$i</option>";  
                                            }
                                            else
                                            {
                                                echo "<option>$i</option>";
                                            }
                                                
                                        }
                                        ?>
                                    </select>.
                                    <select name="subnet4">
                                        <?php for ($i = 0; $i < 256; $i++)
                                        {
                                            if ($i == $ip4)
                                            {
                                                echo "<option selected ='$i'>$i</option>";  
                                            }
                                            else
                                            {
                                                echo "<option>$i</option>";
                                            }
                                                
                                        }
                                        ?>
                                    </select>
                            </th>
                        </tr>
                        <tr>
                            <th>last_time</th>
                            <th><?php echo $result['last_time']; ?></th>
                            <th><input class="form-control" name="last_time" placeholder="<?php echo $result['last_time']; ?>" disabled></th>
                        </tr>
                        <tr>
                            <th>ota_update</th>
                            <th><?php echo $result['ota_update']; ?></th>
                            <th><input class="form-control" name="ota_update"></th>
                        </tr>
                        <tr>
                            <th>version</th>
                            <th><?php echo $result['version']; ?></th>
                            <th><input class="form-control" name="version" placeholder="<?php echo $result['version']; ?>" disabled></th>
                        </tr>
                        <tr>
                            <th>message</th>
                            <th><?php echo $result['message']; ?></th>
                            <th><input class="form-control" name="message"></th>
                        </tr>
                        <tr>
                            <th>reply</th>
                            <th><?php echo $result['reply']; ?></th>
                            <th><input class="form-control" name="reply"></th>
                        </tr>
                    </tbody>
                </table>
            </div>
        </form>
    </div>
    <div class="col-lg-12">
        <H3>
            <label id="check_hardware"> Hardware </label><br />
            <input class="form-control" name="ip_server" id="ip_server" value="188.166.206.43"><br />
            <input class="form-control" name="auth" id="auth" value="<?php echo $result['auth'];?>">
            
        </H3>
        <div class="form-group"><br />
            <select class="form-control" onchange="get_pin()" id="pin">
                <option>project</option>
                <option>D0</option>
                <option>D1</option>
                <option>D2</option>
                <option>D3</option>
                <option>D4</option>
                <option>D5</option>
                <option>D12</option>
                <option>D13</option>
                <option>D14</option>
                <option>D15</option>
                <option>D16</option>
                <?php
                    for( $i = 0; $i < 128; $i++)
                    {           
                        echo "<option>V$i</option>";
                    }
                ?>
            </select><br />
        </div>
        <div class="form-group">
            <label>Message</label>
            <textarea class="form-control" rows="4" id="message"></textarea><br />
            <button type="button" id="send_data" onclick="send_data()">SEND</button>
            <label id="notification"></label>
        </div>
    </div>
</div>





<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script type="text/javascript" language="javascript">
    var ip_server = $("#ip_server").val();
    var auth = $("#auth").val();
    var pin = $("#pin").val();
    var message = $("#message").val();
    check_hardware();
    function get_pin()
    {
        ip_server = $("#ip_server").val();
        auth = $("#auth").val();
        pin = $("#pin").val();
        if( ip_server.length > 5)
        {
            if (auth.length == 32)
            {
                if(pin != "project")
                {
                    var xhttp = new XMLHttpRequest();
                    xhttp.open("GET", "http://" + ip_server + "/"+ auth +"/get/" + pin, true);
                    xhttp.onreadystatechange = function()
                    {
                        if(xhttp.readyState == 4)
                        {
                            if (xhttp.status == 200)
                            {
                                $("#message").val(xhttp.responseText);
                            }
                            else
                            {
                                $("#message").val("CODE : " + xhttp.status + "\nHEADER : " + xhttp.getAllResponseHeaders() + "\nCONTENT : " + xhttp.responseText);
                            }
                        }
                    };
                    xhttp.send();   
                }
                else
                {
                    var xhttp = new XMLHttpRequest();
                    xhttp.open("GET", "http://" + ip_server + "/"+ auth +"/project", true);
                    xhttp.onreadystatechange = function()
                    {
                        if(xhttp.readyState == 4)
                        {
                            if (xhttp.status == 200)
                            {
                                $("#message").val(xhttp.responseText);
                            }
                            else
                            {
                                $("#message").val("CODE : " + xhttp.status + "\nHEADER : " + xhttp.getAllResponseHeaders() + "\nCONTENT : " + xhttp.responseText);
                            }
                        }
                    };
                    xhttp.send();  
                }
            }
        }
    }
    function send_data()
    {
        ip_server = $("#ip_server").val();
        auth = $("#auth").val();
        pin = $("#pin").val();
        message = $("#message").val();
        if( ip_server.length > 5)
        {
            if (auth.length == 32)
            {
                if(pin != "project")
                {
                    var xhttp = new XMLHttpRequest();
                    xhttp.open("GET", "http://" + ip_server + "/"+ auth +"/update/" + pin + "?value=" + message, true);
                    xhttp.onreadystatechange = function()
                    {
                        if(xhttp.readyState == 4)
                        {
                            if (xhttp.status == 200)
                            {
                                $("#notification").html("Gửi thành công");
                                $("#message").val("");
                                setTimeout(close_notification, 5000);
                            }
                            else
                            {
                                $("#message").val("CODE : " + xhttp.status + "\nHEADER : " + xhttp.getAllResponseHeaders() + "\nCONTENT : " + xhttp.responseText);
                            }
                        }
                    };
                    xhttp.send();
                }
            }
        }
    }
    function close_notification()
    {
        $("#notification").html("");
    }
    function check_hardware()
    {        
        auth = $("#auth").val();
        ip_server = $("#ip_server").val();
        if(auth.length == 32)
        {
            var xhttp = new XMLHttpRequest();
            xhttp.open("GET", "http://"+ ip_server + "/" + auth +"/isHardwareConnected", true);
            xhttp.onreadystatechange = function()
            {
                if(xhttp.readyState == 4)
                {   
                    if(xhttp.responseText == "true")
                    {
                        $("#check_hardware").html("Hardware : Online");
                    }
                    else if (xhttp.responseText == "false")
                    {
                        $("#check_hardware").html("Hardware : Offline");
                    }
                    else
                    {
                        $("#status_hardware").html("Hardware : Auth false");
                    }
                }
            };
            xhttp.send();
        }
        else
        {
            $("#check_hardware").html("Hardware : chưa xác định");
        }
        setTimeout(check_hardware, 10000);
    }
</script>
<?php include('include/bot.php'); ?>

