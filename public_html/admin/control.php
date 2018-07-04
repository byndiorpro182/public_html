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
        if (strlen($mac) == 17)
        {
            $result_query = $connect_mysqli->query("SELECT id , mac, auth
                                                    FROM  hardware 
                                                    WHERE mac = '$mac'
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

        /*
        while ($result = mysqli_fetch_array($result_query))
        {           
            echo $result['mac'];
        }
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
            <button type="submit" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse" onclick="logout()" name="submit" value="logout">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <div class="navbar-header">
                    <a class="navbar-brand" href="control.php"> BINARY182 </a>
            </div>     
        <!-- không đóng thẻ nav -->
        <div id="page-wrapper">

            <div class="container-fluid">
                <div class="row">                   
                    <div class="col-lg-12">

                        <div class="form-group">
                            <br />
                            <select class="form-control" onchange="project(),check_hardware()" id="select_mac">
                            <?php
                            while ($result = mysqli_fetch_array($result_query))
                            {           
                                echo "<option value='".$result['auth']."'>".$result['mac']."</option>";
                            }
                            ?>
                            <option value="disconnect">Ngắt kết nối</option>
                            </select><br />
                            <label id="status_hardware"></label><br />
                            <label id="linhtinh"></label>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th onclick="update_pin(16)" id="pin16"><button type="button" class="btn btn-lg btn-default btn-block">TẮT 16</button></th>
                                        <th onclick="update_pin(5)" id="pin5"><button type="button" class="btn btn-lg btn-default btn-block">TẮT 05</button></th>
                                        <th onclick="update_pin(4)" id="pin4"><button type="button" class="btn btn-lg btn-default btn-block">TẮT 04</button></th>
                                    </tr>
                                    <tr>
                                        <th onclick="update_pin(14)" id="pin14"><button type="button" class="btn btn-lg btn-default btn-block">TẮT 14</button></th>
                                        <th onclick="update_pin(12)" id="pin12"><button type="button" class="btn btn-lg btn-default btn-block">TẮT 12</button></th>
                                        <th onclick="update_pin(13)" id="pin13"><button type="button" class="btn btn-lg btn-default btn-block">TẮT 13</button></th>
                                    </tr>
                                    <tr>
                                        <th onclick="update_pin(0)" id="pin0"><button type="button" class="btn btn-lg btn-default btn-block">TẮT 00</button></th>
                                        <th onclick="update_pin(2)" id="pin2"><button type="button" class="btn btn-lg btn-default btn-block">TẮT 02</button></th>
                                        <th onclick="update_pin(15)" id="pin15"><button type="button" class="btn btn-lg btn-default btn-block">TẮT 15</button></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div><br /><br /><br />
                        <div class="form-group">
                            <div align="center">
                                <a href="setup_infrared.php">Điều khiển bằng hồng ngoại</a>
                            </div>
                        </div>
                        
                        <br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
                        <!--
                        <div class="form-group input-group">
                            <span class="input-group-addon">Tin nhắn</span>
                            <input type="text" class="form-control" placeholder="Nội dung" id="message">
                        </div>
                        <div align="center">
                            <button type="button" class="btn btn-primary" onclick="send()"> Gửi</button>
                        </div>
                        <br /><br /><br /><br /><br />
                        -->
                    </div>                                                
                </div>
            </div>
            <!-- /.container-fluid -->

        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script type="text/javascript" language="javascript">
        var pin16 = pin15 = pin14 = pin13 = pin12 = pin0 = pin2 = pin4 = pin5 = 0;
        var auth = $("#select_mac").val();
        var sync = 0;
        var dem_pin = 1;
        project();

        check_hardware();
        function project(){
            get_pin(16);
            get_pin(2);
            get_pin(0);
            get_pin(4);
            get_pin(5);
            get_pin(12);
            get_pin(13);
            get_pin(14);
            get_pin(15);
        }

        function get_pin(pin){
            if(pin != 100){
            
            }
            auth = $("#select_mac").val();
            if (auth.length == 32)

            {
                var xhttp = new XMLHttpRequest();
                xhttp.open("GET", "http://188.166.206.43/" + auth + "/get/d" + pin, true);
                xhttp.onreadystatechange = function() {
                    if(xhttp.readyState == 4)
                        {
                            if (xhttp.status == 200)
                            {
                                if(sync == 0)
                                {    
                                    if(pin == 100){}
                                    else if(pin == 16){
                                        if( pin16 != xhttp.responseText.substr(2,1)){
                                            pin16 = xhttp.responseText.substr(2,1);
                                            handle(pin);
                                        }
                                    }else if(pin == 15){
                                        if( pin15 != xhttp.responseText.substr(2,1)){
                                            pin15 = xhttp.responseText.substr(2,1);
                                            handle(pin);
                                        }
                                    }else if(pin == 14){
                                        if( pin14 != xhttp.responseText.substr(2,1)){
                                            pin14 = xhttp.responseText.substr(2,1);
                                            handle(pin);
                                        }
                                    }else if(pin == 13){
                                        if( pin13 != xhttp.responseText.substr(2,1)){
                                            pin13 = xhttp.responseText.substr(2,1);
                                            handle(pin);
                                        }
                                    }else if(pin == 12){
                                        if( pin12 != xhttp.responseText.substr(2,1)){
                                            pin12 = xhttp.responseText.substr(2,1);
                                            handle(pin);
                                        }
                                    }else if(pin == 5){
                                        if( pin5 != xhttp.responseText.substr(2,1)){
                                            pin5 = xhttp.responseText.substr(2,1);
                                            handle(pin);
                                        }
                                    }else if(pin == 4){
                                        if( pin4 != xhttp.responseText.substr(2,1)){
                                            pin4 = xhttp.responseText.substr(2,1);
                                            handle(pin);
                                        }
                                    }else if(pin == 2){
                                        if( pin2 != xhttp.responseText.substr(2,1)){
                                            pin2 = xhttp.responseText.substr(2,1);
                                            handle(pin);
                                        }
                                    }else if(pin == 0){
                                        if( pin0 != xhttp.responseText.substr(2,1)){
                                            pin0 = xhttp.responseText.substr(2,1);
                                            handle(pin);
                                        }
                                    }
                                    get_pin(pin);
                                }
                                else{
                                    get_pin(pin);
                                }
                            }
                        }
                };
                xhttp.send();
            }
        }

        function update_pin(pin)
        {
            var value = 0;
            switch(pin)
            {
                case 16 : if(pin16 == 0){value = 1; pin16 = 1;}else{value = 0; pin16 = 0;} break;
                case 15: if(pin15 == 0){value = 1; pin15 = 1;}else{value = 0;pin15 = 0;} break;
                case 14: if(pin14 == 0){value = 1; pin14 = 1;}else{value = 0;pin14 = 0;} break;
                case 13: if(pin13 == 0){value = 1;pin13 = 1;}else{value = 0;pin13 = 0;} break;
                case 12: if(pin12 == 0){value = 1;pin12 = 1;}else{value = 0;pin12 = 0;} break;
                case 5: if(pin5 == 0){value = 1;pin5 = 1;}else{value = 0;pin5 = 0;} break;
                case 4: if(pin4 == 0){value = 1;pin4 = 1;}else{value = 0;pin4 = 0;} break;
                case 2: if(pin2 == 0){value = 1;pin2 = 1;}else{value = 0;pin2 = 0;} break;
                case 0: if(pin0 == 0){value = 1;pin0 = 1;}else{value = 0;pin0 = 0;} break;
                default :
            }
            sync = 1;
            if (auth.length == 32)
            {
                var xhttp = new XMLHttpRequest();
                xhttp.open("GET", "http://188.166.206.43/"+ auth +"/update/d" + pin + "?value=" + value, true);
                xhttp.onreadystatechange = function() {
                    if(xhttp.readyState == 1 || xhttp.readyState == 2 || xhttp.readyState == 3)
                    {
                        sync = 1;
                    }
                    if(xhttp.readyState == 4)
                    {
                        if (xhttp.status == 200)
                        {
                            handle(pin);
                        }
                        else
                        {
                            alert(xhttp.status + " : " + xhttp.responseText);
                        }
                        sync = 0;
                    }
                };
                xhttp.send();
            }
        }
        function solve(content,name)
        {
            if (content.indexOf(name) == -1)
            {
                return 0;
            }
            var string = content.slice(content.indexOf(name) + name.length + 3);
            var result = "";
            for(var dem = 0; dem < string.length; dem++)
            {
                if(string.charAt(dem) == "\"")
                {
                break;
                }
                result = result + string.charAt(dem);
            }
            return result;   
        }

        function handle(pin) {
            switch (pin)
            {
                case 16 : 
                    if (pin16 == 0) {$('#pin16').html('<button type="button" class="btn btn-lg btn-default btn-block">TẮT 16</button>');}
                                else{$('#pin16').html('<button type="button" class="btn btn-lg btn-danger btn-block">BẬT 16</button>');}
                    break;
                case 15 : 
                    if (pin15 == 0) {$('#pin15').html('<button type="button" class="btn btn-lg btn-default btn-block">TẮT 15</button>');}
                                else{$('#pin15').html('<button type="button" class="btn btn-lg btn-danger btn-block">BẬT 15</button>');}
                    break;
                case  14: 
                    if (pin14 == 0){$('#pin14').html('<button type="button" class="btn btn-lg btn-default btn-block">TẮT 14</button>');}
                                else{$('#pin14').html('<button type="button" class="btn btn-lg btn-danger btn-block">BẬT 14</button>');}
                    break;
                case  13: 
                    if (pin13 == 0) {$('#pin13').html('<button type="button" class="btn btn-lg btn-default btn-block">TẮT 13</button>');}
                                else{$('#pin13').html('<button type="button" class="btn btn-lg btn-danger btn-block">BẬT 13</button>');}
                    break;
                case  12: 
                    if (pin12 == 0) {$('#pin12').html('<button type="button" class="btn btn-lg btn-default btn-block">TẮT 12</button>');}
                                else{$('#pin12').html('<button type="button" class="btn btn-lg btn-danger btn-block">BẬT 12</button>');}
                    break;
                case  5: 
                    if (pin5 == 0) {$('#pin5').html('<button type="button" class="btn btn-lg btn-default btn-block">TẮT 05</button>');}
                                else{$('#pin5').html('<button type="button" class="btn btn-lg btn-danger btn-block">BẬT 05</button>');}
                    break;
                case  4: 
                    if (pin4 == 0) {$('#pin4').html('<button type="button" class="btn btn-lg btn-default btn-block">TẮT 04</button>');}
                                else{$('#pin4').html('<button type="button" class="btn btn-lg btn-danger btn-block">BẬT 04</button>');}
                    break;
                case  2: 
                    if (pin2 == 0) {$('#pin2').html('<button type="button" class="btn btn-lg btn-default btn-block">TẮT 02</button>');}
                                else{$('#pin2').html('<button type="button" class="btn btn-lg btn-danger btn-block">BẬT 02</button>');}
                    break;
                case  0: 
                    if (pin0 == 0) {$('#pin0').html('<button type="button" class="btn btn-lg btn-default btn-block">TẮT 00</button>');}
                                else{$('#pin0').html('<button type="button" class="btn btn-lg btn-danger btn-block">BẬT 00</button>');}
                    break;
                default :
            }
        }
        function send()
        {
            var message = $("#message").val();
            if (auth.length == 32 && message != "")
            {
                var xhttp = new XMLHttpRequest();
                xhttp.open("GET", "http://188.166.206.43/"+ auth +"/update/v1?value=" + message, true);
                xhttp.onreadystatechange = function() {
                if(xhttp.readyState == 4)
                {   
                    if(xhttp.status == 200)
                    {
                        alert("Gửi thành công");
                        $("#message").val("");
                    }
                    else
                    {
                        alert("Gửi thất bại" + xhttp.status + " : " + xhttp.responseText);
                    }
                }
                };
                xhttp.send();
            }
            else
            {
                alert("Gửi thất bại");
                $("#message").val("");
            }

        }
        function logout()
        {
            var retVal = confirm("Bạn muốn thoát khỏi tài khoản này ?");
            if( retVal == true )
            {
                window.location="../admin/control.php?submit=logout";
            }
        }
        function check_hardware()
        {
            if ($("#select_mac").val() == "disconnect")
            {
                $("#status_hardware").html("Đã ngắt kết nối");
            }else
            {   
                auth = $("#select_mac").val();
                if(auth.length == 32)
                {
                    var xhttp = new XMLHttpRequest();
                    xhttp.open("GET", "http://188.166.206.43/"+ auth +"/isHardwareConnected", true);
                    xhttp.onreadystatechange = function()
                    {
                        if(xhttp.readyState == 4)
                        {   
                            if(xhttp.responseText == "true")
                            {
                                $("#status_hardware").html("Online");
                            }
                            else if (xhttp.responseText == "false")
                            {
                                $("#status_hardware").html("Offline");
                            }
                            else
                            {
                                $("#status_hardware").html("Không kết nối được ... ");
                            }
                        }
                    };
                    xhttp.send();
                }
            }
            setTimeout(check_hardware, 10000);
        }
    </script>
</body>

</html>

