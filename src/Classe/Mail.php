<?php // Pour gérer MailJet

namespace App\Classe;

use Mailjet\Client;
use Mailjet\Resources;

class Mail 
{
    private $api_key = 'e5135e16b0467f1d72c00e889f657c80'; // Récuprer l'api sur le site MailJet
    private $api_key_secret = 'd2b0e43422735bcd6d2c74a8c44abe45';

    public function send($to_email, $to_name, $subject, $content) 
    {
        $mj = new Client($this->api_key, $this->api_key_secret,true,['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "gusto-coffee@outlook.fr",
                        'Name' => "Gusto-Coffee"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name
                        ]
                    ],
                    'TemplateID' => 2358132, // Utilisation de l'id fourni par mailjet
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    'Variables' => [
                        'content' => $content,
                    ]
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success();

    }

}