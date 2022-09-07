<?php
include_once ("email-temp/welcome.php");
include_once ("email-temp/reg.php");
include_once ("email-temp/new_post.php");
include_once ("email-temp/friend.php");
include_once ("email-temp/direct.php");
include_once ("email-temp/comment.php");
include_once ("email-temp/premium.php");
include_once ("email-temp/ads.php");

$templates = array(
    'welcome' => $e_text_welcome,
    'reg' => $e_text_reg,
    'new_post' => $e_text_new_post,
    'friend' => $e_text_friend,
    'direct' => $e_text_direct,
    'comment' => $e_text_comment,
    'premium' => $e_text_premium,
    'ads' => $e_text_ads,
);

$attaches = array(
    'welcome' => $a_welcome,
    'reg' => $a_reg,
    'new_post' => $a_new_post,
    'friend' => $a_friend,
    'direct' => $a_direct,
    'comment' => $a_comment,
    'premium' => $a_premium,
    'ads' => $a_ads,
);


function send_sm_email($email_text, $need_email, $need_attaches){
    // ссылки на изображения на сервере
    $attach = $need_attaches;

// чтобы отображалась картинка и ее не было в аттаче
// путь к картинке задается через CID: - Content-ID

    $text = $email_text;

    $from = "webmaster@hyper-fin-market.ru";
    //$to = "kulberg.artem@gmail.com";
    //$to = "kulberg.artem@yandex.ru";
    $to = $need_email;
    $subject = "Тема письма";

// Заголовки письма === >>>
    $headers = "From: $from\r\n";
//$headers .= "To: $to\r\n";
    $headers .= "Subject: $subject\r\n";
    $headers .= "Date: " . date("r") . "\r\n";
    $headers .= "X-Mailer: zm php script\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/alternative;\r\n";
    $baseboundary = "------------" . strtoupper(md5(uniqid(rand(), true)));
    $headers .= "  boundary=\"$baseboundary\"\r\n";
// <<< ====================

// Тело письма === >>>
    $message  =  "--$baseboundary\r\n";
    $message .= "--$baseboundary\r\n";
    $newboundary = "------------" . strtoupper(md5(uniqid(rand(), true)));
    $message .= "Content-Type: multipart/related;\r\n";
    $message .= "  boundary=\"$newboundary\"\r\n\r\n\r\n";
    $message .= "--$newboundary\r\n";
    $message .= "Content-Type: text/html; charset=\"utf-8\"\r\n";
    $message .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
    $message .= $text . "\r\n\r\n";

// <<< ==============

// прикрепляем файлы ===>>>
    foreach($attach as $filename){
        $mimeType='image/png';
        $fileContent = file_get_contents($filename,true);
        $filename=basename($filename);
        $message.="--$newboundary\r\n";
        $message.="Content-Type: $mimeType;\r\n";
        $message.=" name=\"$filename\"\r\n";
        $message.="Content-Transfer-Encoding: base64\r\n";
        $message.="Content-ID: <$filename>\r\n";
        $message.="Content-Disposition: inline;\r\n";
        $message.=" filename=\"$filename\"\r\n\r\n";
        $message.=chunk_split(base64_encode($fileContent));
    }
// <<< ====================

// заканчиваем тело письма, дописываем разделители
    $message.="--$newboundary--\r\n\r\n";
    $message.="--$baseboundary--\r\n";

// отправка письма
    mail($to, $subject, $message , $headers);
};

// Настройки на стороне пользователя
$email_text_temp = $templates[$_POST['need_temp']];
$need_email = $_POST['email'];
$need_attaches = $attaches[$_POST['need_temp']];


// Отправка письма
send_sm_email($email_text_temp, $need_email, $need_attaches);
