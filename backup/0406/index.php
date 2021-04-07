<?php
    include('web/dbcon.php');
    include('web_ahyun/check.php');
    //******* zone value  ***/

    date_default_timezone_set('Asia/Seoul');
	$currdt = date("Y-m-d H:i:s"); 
	$userip = $_SERVER['REMOTE_ADDR'];
    if(isset($_SERVER['HTTP_REFERER'])) 
        $referer = $_SERVER['HTTP_REFERER'];  
    else 
        $referer = ""; 


    //******* zone value  ***/
    $usersdb = "users_ahyun";
    $visitdb = "tb_stat_visit_ahyun";

    
    if(is_login()){
        
        if ($_SESSION['user_id'] == 'admin' && $_SESSION['is_admin']==1){            
            header("Location:index_ma.htm");
        }else{
            header("Location:index_main.htm");
        }
    }

    $login_ok = false;
    $login_try = false;

    $method = $_SERVER['REQUEST_METHOD'];
    // login first start
    if ( ($_SERVER['REQUEST_METHOD'] == 'POST') and isset($_POST['login']) )
    {
        //log try 
        $login_try = true;
		$username=$_POST['user_name']; //username
		$usernumber=$_POST['user_number']; // usernumber 
        //$userpassowrd=$_POST['user_password']; // username

		if(empty($usernumber)){
			$errMSG = "사번을 입력하세요.";
		}else if(empty($username)){
			$errMSG = "이름을 입력하세요.";
		}else{
			

			try { 

				$stmt = $con->prepare('select * from '.$usersdb.' where usernumber=:usernumber');

				$stmt->bindParam(':usernumber', $usernumber);
				$stmt->execute();
			   
			} catch(PDOException $e) {
				die("Database error. " . $e->getMessage()); 
			}

			$row = $stmt->fetch();
			$salt = $row['salt'];
			$password = $row['password'];
            $dbusername = $row['username'];
			
			//$decrypted_password = decrypt(base64_decode($password), $salt);

			//if ( $userpassowrd == $decrypted_password) {
            if ( $username == $dbusername) {
				$login_ok = true;
			}else{
                $errMSG = "회원정보 불일치.";
            }

		}
        
        
        if ($login_ok){

            // 비활서화 된 아이디에 대해 체크 하는 로직 
            //if ($row['activate']==0)
			//	echo "<script>alert('$username 계정 활성이 안되었습니다. 관리자에게 문의하세요.')</script>";
            //else{
                
            //session_regenerate_id();
            // session_regenerate_id 오류 발생 
            $_SESSION['user_id'] = $usernumber;
            $_SESSION['is_admin'] = $row['is_admin'];

            try{
            // 여기서 방문자 처리
            $_SESSION['visit'] = "1";
            $stmt = $con->prepare('insert into '.$visitdb.' (regdate, regip, referer) values(:currdt, :userip, :referer)');
			$stmt->bindParam(':currdt', $currdt);
            $stmt->bindParam(':userip', $userip);
            $stmt->bindParam(':referer', $referer);
			$inserted = $stmt->execute();
            // 여기서 방문자 처리 끝
            }catch(PDOException $e) {
				die("Database error. " . $e->getMessage()); 
			}

            //
            if ($usernumber=='admin' && $row['is_admin']==1 ){
                header('Location:index_main.htm');
            }else{
                header("Location:index_main.htm");
            }
			//session_write_close();		
			//}
            
		}   
        
	}

    
    //초기 페이지 또는 인증 오류시
    if (!$login_ok){

        if($login_try){
            //입력 값 오류 및 입력값 불일치 
            if(isset($errMSG)){
                    echo "<script>alert('$errMSG')</script>";
            }else{
                echo "<script>alert('아이디 [ $usernumber ]인증 오류')</script>";
            }
        }
	}


    // 오늘 방문자수
    try{
        $stmt = $con->prepare('select count(*) as count from '.$visitdb.' where DATE(regdate) = DATE(:currdt)');
        $stmt->bindParam(':currdt', $currdt);
        $stmt->execute();

    }catch(PDOException $e) {
        die("Database error. " . $e->getMessage()); 
    }
    
    $row = $stmt->fetch(); 
	$today_visit_count = $row['count'];

    
    // 오늘 방문자수
    try{
        // 전체 방문자수
        $stmt = $con->prepare('select count(*) as count from '.$visitdb);
        $stmt->execute();

    }catch(PDOException $e) {
        die("Database error. " . $e->getMessage()); 
    }
    
    $row = $stmt->fetch();  
    $total_visit_count = $row['count'];
    // 전체 방문자수
    


    
?>
<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KT Ahyun</title>
    <style>

        /* W3.CSS 4.15 December 2020 by Jan Egil and Borge Refsnes */
        html{box-sizing:border-box}*,*:before,*:after{box-sizing:inherit}
            /* Extract from normalize.css by Nicolas Gallagher and Jonathan Neal git.io/normalize */
            html{-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%}body{margin:0}

            body,h1,h2,h3,h4,h5,h6 {font-family: "Lato", sans-serif;}
            body, html {
            height: 100%;
            color: #777;
            line-height: 1.8;
            }


            /* Create a Parallax Effect */
            .hero__image {
                background-attachment: fixed;
                background-position: center;
                background-repeat: no-repeat;
                background-size: cover;
            }

            /* First image (Logo. Full height) */
            .hero__image {
                background-image: url('img/loginbg.jpg');
                min-height: 100%;
                min-width: 100%;
            }

            /* Turn off parallax scrolling for tablets and phones */
            @media only screen and (max-device-width: 1920px) {
            .hero__image {
                background-attachment: scroll;
                --min-height: 100%;
            }
            }

            @media only screen and (max-device-width: 400px) {
                .input_margin {
                    margin-top: 2;
                }
            }

        






        .box {
            background-color: white;
                position:absolute;
                top:50%;
                left:50%;
                transform:translate(-50%,-50%);
                -ms-transform:translate(-50%,-50%);
                width: 50%; 
                height: 40%;
                min-height: 240px;
                min-width:330px;
        }



        .box__in{
            display: flex;
            flex-direction:column;
            justify-content:space-between;
            width: 100%;
            height: 100%;
            --background-color: burlywood;
        }


        
        .box__in__top {
            --background-color:forestgreen ;
            height: 15%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            
            --vertical-align: middle;
            --background-color: bisque;
            --padding: 8px 12px;
        }

        .box__in__mid {
            --background-color:gray;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 70%;
            

        }



        .box__in__mid_margin {
            width: 80%;
            height: 80%;
            --background-color: blue;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 8px;
        }

        
        .box__in__mid_input {
            width: 100%;
            --background-color: green;
        }
        


        .box__in_bottom {
            --background-color:forestgreen ;
            background-color: rgb(57, 67, 97);
            height: 15%;
            display: flex;
        }

        .bottom__table {
            width: 100%;
            height: 100%;
            display: table;
        }

        .bottom__box__item {
           
            display:table-cell;
            vertical-align:middle;
            text-align: center;
            
        }

        .bottom__table_item_left {
            display:table-cell;
            vertical-align:middle;
            text-align: center;
            background-color: rgb(192, 190, 190);

        }

        .bottom__table_item_right {
            display:table-cell;
            vertical-align:middle;
            text-align: center;
            background-color: rgb(57, 67, 97);

        }
        /**
        .box__in_bottom__left {
            width: 50%;
            background-color: rgb(192, 190, 190);
            margin: auto;
            
        }

        .box__in_bottom__right {
            width: 50%;
            background-color: rgb(57, 67, 97);
            margin: auto;
            
        }
        */
       

        .w3-input{margin-left: 9px; padding:8px;border:none;border-bottom:1px solid #ccc;width:80%}
        .w3-padding-14{padding-top:8px;padding-bottom:8px;}
        .w3-padding-16{padding-top:17px;padding-bottom:8px;}
            
 


        .w3-btn,.w3-button{
                border:none;display:inline-block;padding:8px 16px;vertical-align:middle;overflow:hidden;
                text-decoration:none;color:inherit;background-color:inherit;text-align:center;cursor:pointer;white-space:nowrap}
         
           
        .w3-button:disabled{cursor:not-allowed;opacity:0.3}.w3-disabled *,:disabled *{pointer-events:none}



        .w3-bar .w3-button{ 
            margin-top:3px; margin-bottom:16px; white-space:normal;
        }
        .w3-button:hover{color:#000;background-color:#ccc}

        .w3-light-grey,.w3-hover-light-grey:hover,.w3-light-gray,.w3-hover-light-gray:hover{
            color:#000;background-color:#f1f1f1}


        .w3-opacity-max{opacity:0.25}.w3-opacity-min{opacity:0.75}
        .w3-animate-opacity{animation:opac 0.8s}@keyframes opac{from{opacity:0} to{opacity:1}}




        .w3-opacity,.w3-hover-opacity:hover{opacity:0.79}.w3-opacity-off,.w3-hover-opacity-off:hover{opacity:1}
        .w3-opacity-max{opacity:0.25}.w3-opacity-min{opacity:0.75}

        .w3-round-large{border-radius:8px}

    </style>
</head>
<body>
    <div class="hero__image" id="home">



        <div class="box w3-opacity w3-hover-opacity-off w3-padding-large ">
            <div class="box__in">


                <div class="box__in__top">
                    <div style="padding-left: 13px; width: 20%;"> <font size=5> LOGIN</font></div>
                    <div><img class="kt" style="vertical-align: middle;" srcset="img/kt.gif 320w,
                        img/kt.gif 480w,
                        img/kt.gif 800w"
                sizes="(max-width: 320px) 280px,
                        (max-width: 480px) 320px,
                        (max-width: 800px) 320px,
                        400px"
                src="img/kt.gif" alt="kt"></div>
                    <div style="width: 20%;">&nbsp;</div>
                </div>

                <!-- box mid 시작 -->
                <div class="box__in__mid">
                    
                    
                        <div class="box__in__mid_margin">
                            <div class="box__in__mid_input">
                                <form method="POST">
                                    <p>
                                        <input class="w3-input w3-padding-14" type="text" placeholder="ID" required name="user_number" maxlength="30">
                                    </p>
                                    <p class="input_margin" style="margin-top: 46px;"></p>
                                    <p>
                                        <!--input class="w3-input w3-padding-14" type="password" placeholder="사번" required name="user_password" maxlength="30"-->
                                        <input class="w3-input w3-padding-14" type="password" placeholder="PASSWORD" required name="user_name" maxlength="30">
                                    </p>
                                    <p style="text-align: right;">
                                        <button class="w3-button w3-light-grey" type="submit" name="login" style="width: 120px;">
                                        <font size=4>SIGNIN</font></button>
                                    </p>
                                </form>
                            </div>

                        </div>
                        
                </div>
                <!-- box mid 끝 -->


                
                <div class="box__in_bottom">
                    <div class="bottom__table">
                        <div class="bottom__table_item_left"><span class="todaycnt"><?php echo "오늘 방문자: " . $today_visit_count ?></span></div>
                        <div class="bottom__table_item_right"><span class="totalcnt"><?php echo "전체 방문자 : " . $total_visit_count ?></span></div>
                    </div>
                </div>


            </div>
        </div>




    </div>
</body>
</html>