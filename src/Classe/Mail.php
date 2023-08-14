<?php

namespace App\Classe;
use Mailjet\Client;
use Mailjet\Resources;


Class Mail
{
private $api_key = '6bc216f11ff88bf60df151d15866dc0f';
private $api_key_secret ='4b9ec2220d50cd6895823790d58dc365';

public function send($to_email, $to_name, $subject, $content){    

$mj= new Client($this->api_key, $this->api_key_secret, true,['version' => 'v3.1']);

$body = [
    'Messages' => [
        [
            'From' => [
                'Email' => "maboutique095@gmail.com",
                'Name' => "Mon Livre"
            ],
            'To' => [
                [
                    'Email' => $to_email,
                    'Name' => $to_name
                ]
            ],
            'TemplateID' => 5001170,
            'TemplateLanguage' => true,
            'Subject' => $subject,
            "Variables" => [
                'content' => $content,
            ]
        ]
    ]
];

$response = $mj->post(Resources::$Email, ['body' => $body]);
$response->success();

}

}




?>
