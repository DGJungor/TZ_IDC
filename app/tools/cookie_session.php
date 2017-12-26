<?php

/**
 * 获取token中的用户ID
 * 方法名：byTokenGetUser
 * 参数：$token
 */
function byTokenGetUser($token) {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "new_idckx";
    $conn = new mysqli($servername, $username, $password,$dbname);
    if ($conn->connect_error) {
        // die("连接失败: " . $conn->connect_error);
        return ["userId"=>-1,"msg"=>"数据库链接错误"];
    }
    $sql = "SELECT * FROM idckx_user_token";
    $data = $conn->query($sql);
    $result = [];
    if ($data->num_rows > 0) {
        while($row = $data->fetch_assoc()) {
            if($row["token"]==$token) {
                $result = $row;
            }
        }
        if(count($result)==0) {
            $conn->close();
            return ["userId"=>-1,"msg"=>"token不存在"];
        }
        if(!$result["expire_time"]<time()) {
            $conn->close();
            return ["userId"=>$result["user_id"],"msg"=>"获取成功"];
        }else {
            $conn->close();
            return ["userId"=>-1,"msg"=>"token已过期"];
        }
    } else {
        $conn->close();
        return ["userId"=>-1,"msg"=>"账号不存在"];
    }
}