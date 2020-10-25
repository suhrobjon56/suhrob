<?php
error_reporting(0);
ob_start();
$API_KEY = "1248294527:AAHsxEm3lvjlXW2zu_AYGH0L0zb-I2vOQV0";
echo "api.telegram.org/bot$API_KEY/setwebhook?url=".$_SERVER['SERVER_NAME']."".$_SERVER['SCRIPT_NAME'];
define('API_KEY',$API_KEY);
function bot($method,$datas=[]){
$url = "https://api.telegram.org/bot".API_KEY."/".$method;
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch,CURLOPT_POSTFIELDS,$datas);
$res = curl_exec($ch);
if(curl_error($ch)){
var_dump(curl_error($ch));
}else{
return json_decode($res);
}
}

$update = json_decode(file_get_contents('php://input'));
$message = $update->message;
$chat_id = $message->chat->id;
$text = $message->text;
$chat_id2 = $update->callback_query->message->chat->id;
$message_id = $update->callback_query->message->message_id;$bot = '@'.bot('getme',['bot'])->result->username;

$data = $update->callback_query->data;
$in_id = $update->callback_query->inline_message_id;
$id = $update->callback_query->from->id;
$inlineqt = $update->inline_query->query;
$msgs = json_decode(file_get_contents('msgs.json'),true);
$temp = json_decode(file_get_contents('temp.json'),true);

if ($text == '/start') {
	bot('sendMessage',[
		'chat_id'=>$chat_id,
		'text'=>'Assalom Alaykum, Like Post yasovchi botga Hush kelibsiz',
		'reply_markup'=>json_encode([
			'inline_keyboard'=>[
				[['text'=>'Like tuzish⚡️ ','callback_data'=>'crPo']],
				[['text'=>'Yaratuvchi😎 🌪','url'=>'t.me/suhrob_Egamberdiyev']]
			]
		])
	]);
}
if ($data == 'crPo') {
	$temp[$chat_id2]['mode'] = 'cr';
	file_put_contents('temp.json', json_encode($temp));
	bot('editMessageText',[
		'chat_id'=>$chat_id2,
		'message_id'=>$message_id,
		'text'=>'Matnni Yuboring'
		'text'=>'Yana Yangi Matn Tayyorlash Uchun Qaytadan /start Berishni Unutmang!'
	]);
}
if ($text and $temp[$chat_id]['mode'] == 'cr') {
	$code = str_shuffle(md5('sajdsalass3525'));
	$msgs[$code]['text'] = $text;
	$msgs[$code]['id'] = $chat_id;
	$msgs[$code]['like'] = null;
	$msgs[$code]['dislike'] = null;
	$temp[$chat_id]['mode'] = null;
	$msgs[$code]['com'] = 0;
	file_put_contents('temp.json', json_encode($temp));
	file_put_contents('msgs.json', json_encode($msgs));
	bot('sendMessage',[
		'chat_id'=>$chat_id,
		'text'=>"$bot $code"
	]);
}
if ($inlineqt) {
    $dis = count(explode(',', $msgs[$inlineqt]['dislike'])) - 1;
	$lik = count(explode(',', $msgs[$inlineqt]['like'])) - 1;
	$bot = trim($bot,'@');
	bot('answerInlineQuery',[
        'inline_query_id'=>$update->inline_query->id,    
        'cache_time'=>'300',
        'results' => json_encode([[
            'type'=>'article',
            'id'=>base64_encode(rand(5,555)),
            'title'=>"Ulashish",
            'description'=>'sdds'.$msgs[$inlineqt]['text'],
            'input_message_content'=>['parse_mode'=>'HTML','message_text'=>$msgs[$inlineqt]['text']],
            'reply_markup' => [
				'inline_keyboard' => [
            [
                ['text' => "👍🏻 [".$lik."]", 'callback_data' => "like&--&$inlineqt"],
                ['text' => "👎🏻 [".$dis ."]", 'callback_data' => "dislike&--&$inlineqt"],
            ]
        ]]
            ]])
        ]);
}
$data = explode('&--&', $update->callback_query->data);
if ($data[0] == 'like') {
	$ids = explode(',', $msgs[$data[1]]['like']);
	if (!in_array($id, $ids)) {
	$msgs[$data[1]]['like'] = $id.','.$msgs[$data[1]]['like'];
		file_put_contents('msgs.json', json_encode($msgs));
	}
	$dis = count(explode(',', $msgs[$data[1]]['dislike'])) - 1;
	$lik =count(explode(',', $msgs[$data[1]]['like'])) - 1;
		$bot = trim($bot,'@');
	bot('editMessageReplyMarkup',[
			'inline_message_id'=>$in_id,
			'reply_markup'=>json_encode([
			'inline_keyboard' => [
            [
                ['text' =>"👍🏻 [".$lik."]", 'callback_data' => "like&--&$data[1]"],
                ['text' => '👎🏻 ['.$dis .']', 'callback_data' => "dislike&--&$data[1]"],
            ]
        ]
				])
			]);
	
}
if ($data[0] == 'dislike') {
	$ids = explode(',', $msgs[$data[1]]['dislike']);
	if (!in_array($id, $ids)) {
	$msgs[$data[1]]['dislike'] = $id.','.$msgs[$data[1]]['dislike'];
		file_put_contents('msgs.json', json_encode($msgs));
	}
	$dis = count(explode(',', $msgs[$data[1]]['dislike'])) - 1;
	$lik =count(explode(',', $msgs[$data[1]]['like'])) - 1;
		$bot = trim($bot,'@');
	bot('editMessageReplyMarkup',[
			'inline_message_id'=>$in_id,
			'reply_markup'=>json_encode([
			'inline_keyboard' => [
            [
                ['text' =>"👍🏻 [".$lik."]", 'callback_data' => "like&--&$data[1]"],
                ['text' => '👎🏻 ['.$dis .']', 'callback_data' => "dislike&--&$data[1]"],
            ]
        ]
				])
			]);
	
}
$start = explode(' ', $text);
if (isset($start[0]) and isset($start[1]) and $start[0]=='/start' and $start[1] != null) {
    $msgs[$chat_id] = $start[1];
    file_put_contents("msgs.json", json_encode($msgs));
    bot('sendMessage',[
        'chat_id'=>$chat_id,
        'text'=>'Endi sharhingizni yuboring',
        'reply_markup'=>json_encode([
                'inline_keyboard'=>[
                [['text'=>'Chiqish 🗳','callback_data'=>"out"]]
                ]])
        ]);
}
if($data[0] == 'out'){
    bot('editMessageText',[
        'chat_id'=>$chat_id2,
        'message_id'=>$message_id,
		'text'=>'☺Assalom Alaykum, Likeli👍 Post yasovchi botga Hush kelibsiz..!',
		'reply_markup'=>json_encode([
			'inline_keyboard'=>[
				[['text'=>'Like tuzish ⚡️ ','callback_data'=>'crPo']],
				[['text'=>'Yaratuvchi😎','url'=>'t.me/Like_kanaltg']]
			]
		])
		]);
        $msgs[$chat_id2] = null;
    file_put_contents("msgs.json", json_encode($msgs));
    }
    if($text != '/start' and $msgs[$chat_id] != null) {
        bot('forwardMessage',[
            'chat_id'=>$msgs[$chat_id],
            'from_chat_id'=>$chat_id,
            'message_id'=>$message->message_id
            ]);
            bot('sendMessage',[
        'chat_id'=>$chat_id,
        'text'=>'Matin yuborildi....',
        'reply_markup'=>json_encode([
                'inline_keyboard'=>[
                [['text'=>'Chiqish 🗳','callback_data'=>"out"]]
                ]])
        ]);
    }
	?>
