<?php
$conn = mysqli_connect("localhost", "root", "비밀번호", "test", 3307);


$email = $_POST['email'];
$password = $_POST['password'];

// DB 정보 가져오기 
$sql = "SELECT * FROM user WHERE email ='{$email}'";
$result = mysqli_query($conn, $sql);

$row = mysqli_fetch_array($result);
$hashedPassword = $row['password'];
$row['id'];

foreach($row as $key => $r){
    echo "{$key} : {$r} <br>";
}
// echo $row['id'];
// DB 정보를 가져왔으니 
// 비밀번호 검증 로직을 실행하면 된다.
$passwordResult = password_verify($password, $hashedPassword);
if ($passwordResult === true) {
    // 로그인 성공
    // 세션에 id 저장
    session_start();
    $_SESSION['userId'] = $row['id'];
    print_r($_SESSION);
    echo $_SESSION['userId'];
    
?>
    <script>
        alert("로그인에 성공하였습니다.")
        location.href = "index.php";
    </script>
<?php
} else {
    // 로그인 실패 
?>
    <script>
        alert("로그인에 실패하였습니다");
    </script>
<?php
}
?>