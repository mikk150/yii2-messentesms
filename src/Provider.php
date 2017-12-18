<?php

namespace mikk150\messentesms;

use yii\httpclient\Client;
use mikk150\sms\BaseMessage;
use mikk150\sms\BaseProvider;
use Yii;

/**
 *
 */
class Provider extends BaseProvider
{
    const RESPONSE_OK_START = 'OK';

    /** @var  string API username */
    public $username;

    /** @var  string API password */
    public $password;

    /** @var  string messente API URL */
    public $apiUrl = 'https://api2.messente.com/';

    /**
     * @var string the default class name of the new message instances created by [[createMessage()]]
     */
    public $messageClass = 'mikk150\messentesms\Message';

    private $_client;

    /**
     * @return     Client  The client.
     */
    public function getClient()
    {
        if (!$this->_client) {
            $this->_client =  new Client([
                'baseUrl' => $this->apiUrl
            ]);
        }

        return $this->_client;
    }

    /**
     * @inheritdoc
     */
    protected function sendMessage($message)
    {
        $requests = [];
        foreach ((array) $message->getTo() as $recipient) {
            $requests[] = $this->getClient()->get('send_sms', [
                'username' => $this->username,
                'password' => $this->password,
                'from' => $message->getFrom(),
                'to' => $recipient,
                'text' => $message->getBody(),
            ])
            ->setFormat(Client::FORMAT_URLENCODED);
        }
        $responses = $this->getClient()->batchSend($requests);
        $messagesSent = true;

        foreach ($responses as $response) {
            if (substr($response->content, 0, 2) !== self::RESPONSE_OK_START) {
                Yii::error('Error "' . $response->content . '" sending SMS "' . $message->getBody() . '" to "' . $recipient . '"', 'messente');
                $messagesSent = false;
            }
        }
        return count($responses)? $messagesSent : false;
    }
}
