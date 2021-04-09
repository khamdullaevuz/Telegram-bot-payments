<?php

require_once "functions.php";
require_once "config.php";

$update = json_decode(file_get_contents('php://input'));
if(empty($update))
exit;

$message = $update->message;
$cid = $message->chat->id;
$text = $message->text;
$mid = $message->message_id;
$pre_check = $update->pre_checkout_query;
$admin = ADMIN_ID;
mkdir("hisob");
if(!file_exists("hisob/$cid.txt")){
	file_put_contents("hisob/$cid.txt", 0);
}

if($pre_check){
bot('answerPreCheckoutQuery',[
'pre_checkout_query_id'=>$pre_check->id,
'ok'=>true
]);
}

if(mb_stripos($text, "/start") !== false or $text == "◀️ Ortga qaytish" or $text == "❌ Bekor qilish"){
  bot('sendMessage',[
    'chat_id'=>$cid,
    'text'=>"*Assalomu alaykum!*\nQuyidagi menyudan foydalaning 👇",
    'parse_mode'=>"markdown",
    'reply_markup'=>json_encode([
    'resize_keyboard'=>true,
    'keyboard'=>[
     [['text'=>"📥 Hisobni toʻldirish"],['text'=>"💰 Hisobim"]],
     [['text'=>"👨‍💻 Dasturchi"]],
    ]
    ])
  ]);
}

if(mb_stripos($text, "📥 Hisobni toʻldirish") !== false){
  bot('sendMessage',[
    'chat_id'=>$cid,
    'text'=>"Hisobingizni toʻldirish uchun kerakli toʻlov tizimini tanlang 👇",
    'parse_mode'=>"markdown",
    'reply_markup'=>json_encode([
    'resize_keyboard'=>true,
    'keyboard'=>[
     [['text'=>"🇺🇿 Click"],['text'=>"🇺🇿 Payme"]],
     [['text'=>"❌ Bekor qilish"]]
    ]
    ])
  ]);
}

if($text == "Pdf"){
	bot('sendDocument',[
    'chat_id'=>$cid,
    'document'=>new CURLFile("ok.txt")
  	]);
}

if(mb_stripos($text, "👨‍💻 Dasturchi") !== false){
  bot('sendMessage',[
    'chat_id'=>$cid,
    'text'=>"Dasturchi: @KhamdullaevUz",
    'parse_mode'=>"html"
  ]);
}

if($text == "🇺🇿 Click"){
	bot('sendInvoice',[
    'chat_id'=>$cid,
    'title'=>"Click orqali hisobni toʻldirish",
    'description'=>"Click toʻlov tizimi orqali hisobni toʻldirish",
    'payload'=>"telebot-test-invoice",
    'provider_token'=>CLICK_KEY,
    'start_parameter'=>"pay",
    'currency'=>"UZS",
    'prices'=>json_encode([
    [
    'label'=>"Hisobni toʻldirish",
    'amount'=>100000
    ]
    ])
	]);
}

if($text == "🇺🇿 Payme"){
	bot('sendInvoice',[
    'chat_id'=>$cid,
    'title'=>"Payme orqali hisobni toʻldirish",
    'description'=>"Payme tizimi orqali hisobni toʻldirish",
    'payload'=>"telebot-test-invoice",
    'provider_token'=>PAYME_KEY,
    'start_parameter'=>"pay",
    'currency'=>"UZS",
    'prices'=>json_encode([
    [
    'label'=>"Hisobni toʻldirish",
    'amount'=>100000
    ]
    ])
	]);
}

if(isset($message->successful_payment)){
  $miqdor = str_replace("000","'000",$message->successful_payment->total_amount/100);
  $qiymat = $message->successful_payment->total_amount/100;
	bot('sendMessage',[
    'chat_id'=>$cid,
    'text'=>"To'lov amalga oshirildi!\nHisobingizga *$miqdor soʻm* tushirildi!",
    'parse_mode'=>"markdown",
    'reply_markup'=>json_encode([
    'resize_keyboard'=>true,
    'keyboard'=>[
     [['text'=>"◀️ Ortga qaytish"]]
    ]
    ])
	]);
	$get = file_get_contents("hisob/$cid.txt");
	$pul = $get + $qiymat;
	file_put_contents("hisob/$cid.txt", $pul);
}

if($text == "💰 Hisobim"){
   $pul = str_replace("000","'000",file_get_contents("hisob/$cid.txt"));
   bot('sendMessage',[
    'chat_id'=>$cid,
    'text'=>"Sizning hisobingiz: *$pul soʻm*",
    'parse_mode'=>"markdown"
   ]);
}