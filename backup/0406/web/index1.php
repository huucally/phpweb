<?php
    include('dbcon.php'); 
    include('check.php');


    if(is_login()){
        
        if ($_SESSION['user_id'] == 'admin' && $_SESSION['is_admin']==1){
            //echo "<script>alert('check 2')</script>";
            header("Location: welcome.php");
        }else{
            header("Location: welcome.php");
        }
    }
?>
<!DOCTYPE html>
<html>
<head>
	<title>로그인 예제</title>
	<link rel="stylesheet" href="bootstrap/css/bootstrap1.min.css">
	
</head>

<body>

<div class="container">

	<h2 align="center">로그인</h2><hr>
	<form class="form-horizontal" method="POST">
		<div class="form-group" style="padding: 10px 10px 10px 10px;">
			<label for="user_name">아이디:</label>
			<input type="text" name="user_name"  class="form-control" id="inputID" placeholder="아이디를 입력하세요." 
				required autocomplete="off" readonly onfocus="this.removeAttribute('readonly');" />
		</div>
		<div class="form-group" style="padding: 10px 10px 10px 10px;">
			<label for="user_password">패스워드:</label>
			<input type="password" name="user_password" class="form-control" id="inputPassword" placeholder="패스워드를 입력하세요." 
				required  autocomplete="off" readonly onfocus="this.removeAttribute('readonly');" />
		</div>
		<div class="checkbox">
			<label><input type="checkbox"> 아이디 기억</label>
		</div>
		
		<div class="from-group" style="padding: 10px 10px 10px 10px;" >
			<button type="submit" name="login" class="btn btn-success">로그인</button>
			<a class="btn btn-success" href="registration.php" style="margin-left: 50px">
			<span class="glyphicon glyphicon-user"></span>&nbsp;등록
			</a>
		</div>
		
	</form>
</div>
</body>
</html>
<?php
    
    $login_ok = false;


    $method = $_SERVER['REQUEST_METHOD'];

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
                //echo "<script>alert('ok')</script>";
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
						header('location:welcome.php');
                    }else{
						header('location:welcome.php');
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