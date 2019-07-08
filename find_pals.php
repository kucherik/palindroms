<?php
// Проверка запуска скрипта через ajax
if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {
    exit();
}

// Проверка хоста
if (isset($_SERVER['HTTP_ORIGIN'])) {
    $address = 'http://' . $_SERVER['SERVER_NAME'];
    if (strpos($address, $_SERVER['HTTP_ORIGIN']) !== 0) {
        exit(json_encode([
            'error' => 'Invalid Origin header: ' . $_SERVER['HTTP_ORIGIN']
        ]));
    }
} else {
    exit(json_encode(['error' => 'No Origin header']));
}

session_start();
// Проверка csrf-токена
if (isset(apache_request_headers()['CsrfToken'])) {
    $csrf = apache_request_headers()['CsrfToken'];
    if ($csrf != $_SESSION['token']) {
        exit(json_encode([
            'error' => 'Invalid Token'
        ]));
    }
} else {
    exit(json_encode(['error' => 'Token not isset']));
}

$text = $_POST['input_text'];

####################
//Тривиальный подход
function isPalindrom($string) {
    $reversed_string = iconv("windows-1251", "utf-8", strrev(iconv("utf-8", "windows-1251", $string)));
    if ($string == $reversed_string) return true; else return false;
}

function getPalindromsTrivial($string){

    $length = mb_strlen($string);
    $result = array();

    for ($i = 0; $i < $length - 1; $i++)
        for ($j = 1; $j < $length - $i; $j++){
            $substring = mb_substr($string, $i, $length - $i - $j + 1);
            if (isPalindrom($substring)) $result[] = $substring;
        }

    return $result;
}

#####################
// Алгоритма Манакера
function getPalindromsManaker($string){

    $n = mb_strlen($string);
    $result = array();

    // Поиск нечетных палиндромов
    $d1 = array();
    $l = 0;
    $r = -1;
    for($i = 0; $i < $n; $i++){
        if($i > $r) $k = 1;
        else $k = min($d1[$l + $r - $i], $r - $i);

        while(0 <= $i-$k && $i+$k < $n && mb_substr($string,$i - $k , 1) == mb_substr($string ,$i + $k , 1)) $k++;

        if ($k > 1)
            for ($z = 2; $z <= $k; $z++){
                $result[] = mb_substr($string,$i - $z + 1, 2*$z - 1);
            }

        $d1[$i] = $k;
        if($i + $k - 1 > $r){
            $r = $i + $k - 1;
            $l = $i - $k + 1;
        }
    }

    // Поиск четных палиндромов
    $d2 = array();
    $l = 0;
    $r = -1;
    for($i = 0; $i < $n; $i++){
        if($i > $r) $k = 0;
        else $k = min($d2[$l + $r - $i ], $r - $i );

        while(0 <= $i-$k-1 && $i+$k < $n && mb_substr($string,$i - $k - 1 , 1) == mb_substr($string ,$i + $k , 1)) $k++;

        if ($k > 0)
            for ($z = 1; $z <= $k; $z++){
                $result[] = mb_substr($string, $i - $z, 2*$z);
            }

        $d2[$i] = $k;
        if($i + $k - 1 > $r){
            $r = $i + $k - 1;
            $l = $i - $k;
        }
    }

    return $result;
}

#################################
// Алгоритма Манакера сокращенный
function getPalindromsManakerRef($string){

    $n = mb_strlen($string);
    $result = array();

    for($p = 0; $p < 2; $p++) {

        $flag = ($p + 1)%2;
        $d{$p} = array();
        $l = 0;
        $r = -1;
        for ($i = 0; $i < $n; $i++) {

            if ($i > $r) $k = $flag;
            else $k = min($d{$p}[$l + $r - $i], $r - $i);

            while (0 <= $i - $k - $p && $i + $k < $n && mb_substr($string, $i - $k - $p, 1) == mb_substr($string, $i + $k, 1)) $k++;

            if ($k > $flag)
                for ($z = 1 + $flag; $z <= $k; $z++) {
                    $result[] = mb_substr($string, $i - $z + $flag, 2*$z - $flag);
                }

            $d2{$p}[$i] = $k;
            if ($i + $k - 1 > $r) {
                $r = $i + $k - 1;
                $l = $i - $k + $flag;
            }
        }
    }

    return $result;
}

$palindroms = getPalindromsManakerRef($text); // Получение полиндромов сокращенным алгоритмом Манакера
//$palindroms = getPalindromsManaker($text); // Получение полиндромов алгоритмом Манакера
//$palindroms = getPalindromsTrivial($text); // Получение полиндромов тривиальным способом

// Вывод
if (!empty($palindroms)){
    $output = '<h4>В строке нашлись следующие палиндромы:</h4>';
    foreach ($palindroms as $palindrom) $output .= $palindrom . '<br>';
}
else $output = '<h4>В строке не нашлось палиндромов</h4>';

echo $output;

?>