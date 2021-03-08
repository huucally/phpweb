<?php
    include('dbcon.php'); 
    include('check.php');
    
    if(is_login()){
        
        if ($_SESSION['user_id'] == 'admin' && $_SESSION['is_admin']==1){            
            header("Location:a/index.htm");
        }else{
            header("Location:a/index.htm");
        }
    }

    $login_ok = false;
    $login_try = false;

    $method = $_SERVER['REQUEST_METHOD'];

    if ( ($_SERVER['REQUEST_METHOD'] == 'POST') and isset($_POST['login']) )
    {
        //log try 
        $login_try = true;
		$username=$_POST['user_name'];  
		$userpassowrd=$_POST['user_password'];

		if(empty($username)){
			$errMSG = "아이디를 입력하세요.";
		}else if(empty($userpassowrd)){
			$errMSG = "패스워드를 입력하세요.";
		}else{
			

			try { 

				$stmt = $con->prepare('select * from users where username=:username');

				$stmt->bindParam(':username', $username);
				$stmt->execute();
			   
			} catch(PDOException $e) {
				die("Database error. " . $e->getMessage()); 
			}

			$row = $stmt->fetch();  
			$salt = $row['salt'];
			$password = $row['password'];
			
			$decrypted_password = decrypt(base64_decode($password), $salt);

			if ( $userpassowrd == $decrypted_password) {
				$login_ok = true;
			}

		}
        
        
        if ($login_ok){

            // 비활서화 된 아이디에 대해 체크 하는 로직 
            //if ($row['activate']==0)
			//	echo "<script>alert('$username 계정 활성이 안되었습니다. 관리자에게 문의하세요.')</script>";
            //else{
                
            //session_regenerate_id();
            // session_regenerate_id 오류 발생 
            $_SESSION['user_id'] = $username;
            $_SESSION['is_admin'] = $row['is_admin'];
            //
            if ($username=='admin' && $row['is_admin']==1 ){
                header('Location:a/index.htm');
            }else{
                header("Location:a/index.htm");
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
                echo "<script>alert('아이디 [ $username ]인증 오류')</script>";
            }
        }
	}
    
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>oddeyefactory</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style/last.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    
    <style>
        body,h1,h2,h3,h4,h5,h6 {font-family: "Lato", sans-serif;}
        body, html {
          height: 100%;
          color: #777;
          line-height: 1.8;
        }

        
        /* Create a Parallax Effect */
        .bgimg-1, .bgimg-2, .bgimg-3 {
          background-attachment: fixed;
          background-position: center;
          background-repeat: no-repeat;
          background-size: cover;
        }

        /* First image (Logo. Full height) */
        .bgimg-1 {
          background-image: url('img/loginbg.jpg');
          min-height: 100%;
        }

        /* Turn off parallax scrolling for tablets and phones */
        @media only screen and (max-device-width: 1920px) {
          .bgimg-1, .bgimg-2, .bgimg-3 {
            background-attachment: scroll;
            --min-height: 100%;
          }
        }


        .loginbox__top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            --background-color: bisque;
            padding: 8px 12px;
        }

        .loginbox__top__logo {
            padding-left: 0;
            --background-color: bisque;
        }

        .loginbox__bottom {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            padding: 8px 12px;
            --background-color: rgb(82, 55, 100);
        }

        .kt{max-width:100%;height:auto}img{vertical-align:middle}a{color:inherit}      

        
        .loginbox,.w3-hover-white:hover{color:#000!important;background-color:#fff!important}
        
                

    </style>
    <script>
        function changeColor() {
            var elem = document.getElementById('para');
            elem.style.color = red;
        }
    </script>
</head>
<body>

    <div class="bgimg-1 w3-display-container w3-opacity-min" id="home">
        <div class="w3-display-middle" style="white-space:nowrap; width: 45%; min-width:300px;">





            <div class="loginbox w3-opacity w3-hover-opacity-off w3-padding-large w3-round-large">
            
                <form method="POST">
                    <div class="loginbox__top">
                        <div class="loginbox__top__img"><span>LOGIN</span></div>
                        <div class="loginbox__top__logo">
                            <!--img class="kt" src="img/kt.gif" alt="Image 3"--> 
                            <!--img class="kt"
                            alt="A baby smiling with a yellow headband."
                            srcset="
                                img/kt.gif  300w,
                                img/kt.gif  600w,
                                img/kt.gif  1200w,
                            "
                            sizes="70vmin"
                            -->
                            <img class="kt" srcset="img/kt.gif 320w,
                                    img/kt.gif 480w,
                                    img/kt.gif 800w"
                            sizes="(max-width: 320px) 280px,
                                    (max-width: 480px) 440px,
                                    800px"
                            src="img/kt.gif" alt="kt">
                        </div>
                        <div class="loginbox__top__blank">&nbsp;&nbsp;</div>        
                    </div>

                    
                    <div class="loginbox__mid">
                        <p><input class="w3-input w3-padding-14" type="text" placeholder="성명" required name="user_name" maxlength="50"></p>
                        <p><input class="w3-input w3-padding-14" type="password" placeholder="사번" required name="user_password" maxlength="50"></p>
                        <!--<p id="para">어떤 글</p-->
                    </div>


                    <div class="loginbox__bottom">
                        <div>&nbsp;</div>
                        <div><button class="w3-button w3-light-grey w3-section" type="submit" name="login" style="width: 120px;">SIGNIN</button>
                        </div>
                    </div>
                </form>
            </div>





            </div>
    </div>



</body>
</html>