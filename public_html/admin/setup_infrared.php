<?php
    include('../function/function_basic.php');

    if (isset($_GET['submit']) && $_GET['submit'] = "logout")
    {
        setcookie("mac", "", time() - 10, "/admin","hoangvanhieu182.000webhostapp.com", 0);
        header( "Location:  ../admin/login.php");
        exit();   
    }
    if(!isset($_COOKIE["mac"]))
    {
        header( "Location:  ../admin/login.php");
        exit();
    }
    else
    {
        $mac = $_COOKIE["mac"];
        if (strlen($mac) == 17){
            $result_query = $connect_mysqli->query("SELECT id , mac, auth
                                                    FROM  hardware 
                                                    WHERE mac = '$mac'
                                                    ");
            if ( mysqli_num_rows($result_query) == 0){
                header( "Location:  ../admin/login.php");
                exit();
            }else{
                $result = mysqli_fetch_array($result_query);
                $mac = $result['mac'];
            }
        }
        else{
            header( "Location:  ../admin/login.php");
            exit();
        }
        /*
        $("input[type=text]").val("");
        */
    }

?>



<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>B182</title>
    
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/sb-admin.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

</head>
<body>
  <div id="wrapper">

        <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <div class="navbar-header">
                <a class="navbar-brand" href="control.php">Trở lại</a>
            </div>
        </nav>   
        <!-- không đóng thẻ nav -->
        <div id="page-wrapper">

            <div class="container-fluid">
                <div class="row">                   
                    <div class="col-lg-12">
                        <div class="form-group">
                            <input type="hidden" id="mac" name="mac" value="<?php echo $result['mac']; ?>">
                            <input type="hidden" id="auth" name="auth" value="<?php echo $result['auth']; ?>">
                        </div>
                        <?php
                            if(isset($_POST['save_infrared'])){
                                if(isset($_POST['name']) && $_POST['name'] != ""){
                                    $name = $_POST['name'];
                                    $value = $_POST['value'];
                                    $value = trim($value, '["]');
                                    $result_query = $connect_mysqli->query("INSERT INTO display(mac,name,value,type)
                                                                            VALUES ('$mac','$name','$value','button')
                                                                            ");
                                    if($result_query){
                                        $message = "Lưu thành công";
                                    }else{
                                        $message = "Lưu thất bại";
                                    }
                                }else{
                                    $message = "Hãy đặt tên cho mã hồng ngoại!";
                                }
                            }
                            if(isset($_GET['delete_id'])){
                                $result_query = $connect_mysqli->query("DELETE FROM display 
                                                                        WHERE mac='$mac' AND id='".$_GET['delete_id']."'");
                                if($result_query){
                                    $message = "Xóa thành công";
                                }else{
                                    $message = "Xóa không thành công";
                                }
                            }else{
                                if(isset($_POST['submit']) && $_POST["name".$_POST['submit']] != ""){
                                    $result_query = $connect_mysqli->query("UPDATE display
                                                                            SET name='".$_POST["name".$_POST['submit']]."'".
                                                                            "WHERE id='".$_POST['submit']."'");
                                    if($result_query){
                                        $message="Lưu tên mới thành công";
                                    }else{
                                        $message="Lưu thất bại";
                                    }
                                }
                            }
                            $result_query = $connect_mysqli->query("SELECT id,name,value
                                                                    FROM  display 
                                                                    WHERE mac = '$mac'
                                                                    "); 
                        ?>
                        <div class="page-header">
                            <h2>Điều khiển hồng ngoại</h2>
                            
                            <label> Chọn PIN gửi tín hiệu hồng ngoại</label>
                            <select class="form-control" id="select_pin_send"  onchange="select_pin_send()">
                                <option>00</option>
                                <option>02</option>
                                <option>04</option>
                                <option>05</option>
                                <option>12</option>
                                <option>13</option>
                                <option>14</option>
                                <option>15</option>
                                <option>16</option>
                            </select><br>
                            <label id="notifical_send"></label><br>
                            <?php while($result = mysqli_fetch_array($result_query)){ ?>
                                <button type="button" id="<?php echo $result['id'];?>" class="btn btn-primary btn-lg btn-block" onclick="send_raw(<?php echo $result['id'];?>)" value="<?php echo $result['value']; ?>"><?php echo $result['name'];?></button>
                            <?php } ?>                          
                            <br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
                            <br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
                            <br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
                            <center><button type="button" class="btn btn-primary" onclick="hide_and_show()">Cài đặt hồng ngoại</button></center>
                        </div>
                        <div id="setup">                            
                            <div class="page-header">
                                <h2>Thêm nút điều khiển hồng ngoại</h2>
                                <label> Chọn PIN nhận tín hiệu hồng ngoại</label>
                                <select class="form-control" id="select_pin" onchange="select_pin()">
                                    <option >00</option>
                                    <option >02</option>
                                    <option >04</option>
                                    <option >05</option>
                                    <option >12</option>
                                    <option >13</option>
                                    <option >14</option>
                                    <option >15</option>
                                    <option >16</option>
                                </select><br>
                                <div align="center">
                                    <button type="button" class="btn btn-primary" onclick="delete_data_v2()"> Lấy mã hồng ngoại</button>  
                                </div>
                            </div>
                                
                            <form role="form" method="POST" action="setup_infrared.php">
                                <label id="wait_infrared"></label>    
                            
                                <div class="form-group has-primary" id="save_infrared">
                                </div>
                            </form>
                            <?php
                                $result_query = $connect_mysqli->query("SELECT id,name,value
                                                                        FROM  display 
                                                                        WHERE mac = '$mac'
                                                                        ");
                            ?>
                            <form role="form" method="POST" action="setup_infrared.php">
                                <h2> Sửa tên nút </h2>
                                <label id="notifical"><?php echo $message; ?></label>
                                <div class="table-responsive" >
                                    <table class="table" id="myTable">
                                        <thead>
                                            <tr>
                                                <th>Tên gọi</th>
                                                <th>Mã</th>
                                                <th>Lưu</th>
                                                <th>Xóa</th>
                                            </tr>
                                        </thead>
                                        <body >
                                            <?php while($result = mysqli_fetch_array($result_query)){ ?> 
                                            <tr>
                                                <th><input type="text" class="form-control" name="name<?php echo $result['id'];?>" value="<?php echo $result['name']; ?>" maxlength="20"></th>
                                                <th><a target=_blank href="http://chart.googleapis.com/chart?cht=qr&chs=500x500&chl=<?php echo $result['value'];?>">Xem</a></th>
                                                <th><button type="submit" name="submit" class="btn btn-primary" value="<?php echo $result['id'];?>">Lưu</button></th>
                                                <th><a href="?delete_id=<?php echo $result['id']; ?>">Xóa</a></th>
                                            </tr>
                                            <?php } ?>
                                        </body>
                                    </table>
                                </div>                          
                            </form>
                        </div>
                        <br />
                        
                        <br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
                        
                    </div>                                                
                </div>
            </div>
            <!-- /.container-fluid -->

        </div>
        <!-- /#page-wrapper -->

    </div>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script type="text/javascript" language="javascript">
    var pin_read_infrared = $("#select_pin").val();
    var pin_send_infrared = $("#select_pin_send").val();
    var auth = $("#auth").val();
    var buff_time = 0;
    function send_raw(id){
        var value = $("#"+id).val();
        var xhttp = new XMLHttpRequest();
        xhttp.open("GET", "http://188.166.206.43/"+ auth +"/update/v1?value=IR_SEND_" + pin_send_infrared + "_" + value, true);
        xhttp.onreadystatechange = function(){
            if(xhttp.readyState == 4 && xhttp.status == 200){   
                $("#notifical_send").html("Đã gửi tín hiệu ");
                setTimeout(close_notifical_send, 3000);
            }
        };
        xhttp.send();
    }
    function close_notifical_send(){
        $("#notifical_send").html("");
    }
    function select_pin_send(){
        pin_send_infrared = $("#select_pin_send").val();
    }
    function select_pin(){
        pin_read_infrared = $("#select_pin").val();
    }
    function delete_data_v2(){
        $("#save_infrared").html("");
        $("#notifical").html("");
        var xhttp = new XMLHttpRequest();
        xhttp.open("GET", "http://188.166.206.43/"+ auth +"/update/v2?value=0", true);
        xhttp.onreadystatechange = function(){
            if(xhttp.readyState == 4 && xhttp.status == 200){   
                $("#wait_infrared").html("*");
                get_infrared();
            }
        };
        xhttp.send();
    }
    function get_infrared(){
        var xhttp = new XMLHttpRequest();
        xhttp.open("GET", "http://188.166.206.43/"+ auth +"/update/v1?value=IR_RECV_" + pin_read_infrared, true);
        xhttp.onreadystatechange = function(){
            if(xhttp.readyState == 4 && xhttp.status == 200){   
                $("#wait_infrared").html("**");
                wait_infrared();
            }
        };
        xhttp.send();
    }
    function wait_infrared(){
        var xhttp = new XMLHttpRequest();
        xhttp.open("GET", "http://188.166.206.43/"+ auth +"/get/v2", true);
        xhttp.onreadystatechange = function(){
            if(xhttp.readyState == 4 && xhttp.status == 200){   
                if(xhttp.responseText == '["0"]'){
                    if(buff_time < 8){
                        var abcd = $("#wait_infrared").html();
                        $("#wait_infrared").html(abcd + "*");
                        setTimeout(wait_infrared,500);
                        buff_time++;
                    }else{
                        $("#wait_infrared").html("Không tìm thấy tín hiệu hồng ngoại");
                        buff_time = 0;
                    }  
                }
                else{
                    $("#wait_infrared").html("");
                    var content_save_infrared = "<input type=hidden class='form-control' name='value' value='" + xhttp.responseText + "'>";
                        content_save_infrared += "<label class='control-label'>Đặt tên cho mã hồng ngoại</label>";
                        content_save_infrared += "<input type=text class=form-control name=name maxlength='20'><br>";
                        content_save_infrared += "<div align=center><button type=submit name=save_infrared class='btn btn-primary'>Lưu lại</button></div>";
                    $("#save_infrared").html(content_save_infrared);      
                }
            }
        };
        xhttp.send();
    }
    function add_new() {
        var table = document.getElementById("myTable");
        var row = table.insertRow(1);
        var cell1 = row.insertCell(0);
        var cell2 = row.insertCell(1);
        var cell3 = row.insertCell(2);
        cell1.innerHTML = "<input type=text class=form-control>";
        cell2.innerHTML = "<a href='#'>Mã</a>";
        cell3.innerHTML = "<a href='#'>Xóa</a>";
    }
    function hide_and_show() {
        var x = document.getElementById("setup");
        if (x.style.display === "none") {
            x.style.display = "block";
        } else {
            x.style.display = "none";
        }
    }
    var x = document.getElementById("setup");
    x.style.display = "none";
</script>

</body>
</html>
