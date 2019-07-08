<?php

session_start();

if (empty($_SESSION['token'])) {
    if (function_exists('mcrypt_create_iv')) {
        $_SESSION['token'] = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
    } else {
        $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32));
    }
}

$token = $_SESSION['token'];

header ("Content-Type: text/html; charset=utf-8");

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <!--[if lt IE 9]><meta http-equiv='X-UA-Compatible' content='IE=edge'><![endif]-->
    <title>Поиск палиндромов</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        .center{text-align: center;}
    </style>
</head>

<body>
<div class="container">
    <div class="row justify-content-center mt-2 mb-4">
        <div class="col-xs-12 col-md-6 center">
            <h2>Поиск палиндромов</h2>
        </div>
    </div>
    <div class="row justify-content-center mb-4">
        <div class="col-xs-12 col-md-9 center">
            <label for="text">Введите строку для поиска палиндромов:</label><br>
            <input type="text" id="text" class=" col-xs-12 col-md-6 mb-3" placeholder="" autocomplete="off">
            <input type="hidden" id="token" name="token" value="<?php echo $token; ?>">
            <br>
            <button name="send" id="send" type="submit" class="btn btn-info">Отправить</button>
        </div>
    </div>
    <div class="row justify-content-center">
        <div id="result" class="col-9 center">
        </div>
    </div>
</div>


<script>
    document.addEventListener("DOMContentLoaded",function() {

        var mybutton = document.getElementById('send');
        mybutton.addEventListener("click", function(){

            var text = document.getElementById('text').value;
            var token = document.getElementById('token').value;

            text = 'input_text=' +  encodeURIComponent(text);
            token = encodeURIComponent(token);

            var request = new XMLHttpRequest();

            request.open('POST','find_pals.php',true);
            request.addEventListener('readystatechange', function() {

                if ((request.readyState==4) && (request.status==200)) {
                    var result = document.getElementById('result');
                    result.innerHTML = request.responseText;
                }
            });

            request.setRequestHeader("X-Requested-With", "XMLHttpRequest");
            request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            request.setRequestHeader('CsrfToken', token);
            request.send(text);
        });
    });


    var input = document.getElementById("text");
    input.addEventListener("keyup", function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            document.getElementById("send").click();
        }
    });

</script>

</body>
</html>

