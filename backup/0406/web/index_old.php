<?php
    include('dbcon.php'); 
    include('check.php');


    if(is_login()){
        
        if ($_SESSION['user_id'] == 'admin' && $_SESSION['is_admin']==1){
            //echo "<script>alert('check 2')</script>";
            header("Location: a/index.htm");
        }else{
          header("Location: a/index.htm");
        }
    }
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>title</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="restyle.css">
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
          background-image: url('https://www.w3schools.com/w3images/parallax1.jpg');
          min-height: 100%;
        }

        /* Turn off parallax scrolling for tablets and phones */
        @media only screen and (max-device-width: 1600px) {
          .bgimg-1, .bgimg-2, .bgimg-3 {
            background-attachment: scroll;
            min-height: 400px;
          }
        }
        
        
    
    </style>
</head>



<body>
    <div class="bgimg-1 w3-display-container w3-opacity-min" id="home">
     <div class="w3-display-middle" style="white-space:nowrap; width: 40%; min-width:300px;">
      
            <div class="mySlides w3-animate-opacity" style="width: 100%;">
              <!--img class="w3-image" src="https://www.w3schools.com/w3images/app4.jpg" alt="Image 3" style="min-width:500px" width="1500" height="1000"-->
              <div class="w3-display-left w3-padding " style="width: 100%">
                <div class="w3-white w3-opacity w3-hover-opacity-off w3-padding-large w3-round-large">

                  <form method="POST">
                    <p><input class="w3-input w3-padding-16" type="text" placeholder="성명" required name="user_name" maxlength="50"></p>
                    <p><input class="w3-input w3-padding-16" type="password" placeholder="사번" required name="user_password" maxlength="50"></p>
                    <p><button class="w3-button w3-light-grey w3-section" type="submit" name="login">SEND MESSAGE</button><a class="btn btn-success" href="registration.php" style="margin-left: 50px"><span class="glyphicon glyphicon-user"></span>&nbsp;등록</a></p>
                    
                  </form>
                  <!-- 로그인 박스 끝 -->

                </div>
              </div>
            </div>
         </div>
    
        
    </div>
    
    
</body>
</html>
<?php
    
    $login_ok = false;


    $method = $_SERVER['REQUEST_METHOD'];

    //echo "<script>alert('$method')</script>";

    if(isset($_SESSION['user_id'])){
        $session_id = $_SESSION['user_id'];
        //echo "<script>alert('111.$session_id')</script>";
    }
        


    if ( ($_SERVER['REQUEST_METHOD'] == 'POST') and isset($_POST['login']) )
    {
        
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
                echo "<script>alert('ok')</script>";
				$login_ok = true;
			}
		}

		
		
		

        
        if ($login_ok){

            // 비활서화 된 아이디에 대해 체크 하는 로직 
            //if ($row['activate']==0)
			//	echo "<script>alert('$username 계정 활성이 안되었습니다. 관리자에게 문의하세요.')</script>";
            //else{
					session_regenerate_id();
					$_SESSION['user_id'] = $username;
					$_SESSION['is_admin'] = $row['is_admin'];

					if ($username=='admin' && $row['is_admin']==1 ){
						header('location:a/index.htm');
                    }else{
                      header("Location: a/index.htm");
                    }
					session_write_close();
			//}
		}
		else{
            
            //입력 값 오류 및 입력값 불일치 
            if(isset($errMSG)){
			     echo "<script>alert('$errMSG')</script>";
            }else{
                echo "<script>alert('아이디 [ $username ]인증 오류')</script>";
            }
			//echo "<script>alert('$username 인증 오류')</script>";
            // 여기서 로그인 불가 메세지 출력
		}
        
        
	}
    //else 초기 페이지 로딩

?>









