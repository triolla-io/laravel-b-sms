<?php

namespace Yna\BSms;

use DomainException;
use GuzzleHttp\Client as HttpClient;
use Yna\BSms\Exceptions\CouldNotSendNotification;

class BSmsApi
{
    const FORMAT_JSON = 3;

    /** @var string */
    protected $apiUrl = 'https://api.b-sms.co.il/SendMessageXml.ashx';

    /** @var HttpClient */
    protected $httpClient;

    /** @var string */
    protected $userID;

    /** @var string */
    protected $userPass;

    /** @var string */
    protected $sender;

    public function __construct($userID, $userPass, $sender = null)
    {
        $this->userID = $userID;
        $this->userPass = $userPass;
        $this->sender = $sender;

        $this->httpClient = new HttpClient([
            'timeout' => 10,
            'connect_timeout' => 5,
        ]);
    }

    /**
     * @param string $to
     * @param string $body
     * @param null|string $from
     * @return string
     *
     */
    public function send($to, $body, $from = null)
    {
        $from = htmlspecialchars($from ?: $this->sender);
        $to = htmlspecialchars($to);
        $body = htmlspecialchars($body);

        $xml = <<<XML
        <Inforu>
            <User>
                <Username>{$this->userID}</Username>
                <Password>{$this->userPass}</Password>
            </User>
            <Content Type="sms">
                <Message>{$body}</Message>
            </Content>
            <Recipients>
                <PhoneNumber>{$to}</PhoneNumber>
            </Recipients>
            <Settings>
                <Sender>{$from}</Sender>
            </Settings>
        </Inforu>
XML;

        try {
            $response = $this->httpClient->get($this->apiUrl, ['query' => ['InforuXML' => $xml]]);
            $response = new \SimpleXMLElement((string) $response->getBody());

            if ($response->Status != 1) {
                throw new DomainException($response->Description, intval($response->Status));
            }

            return $response->Status;
        } catch (DomainException $exception) {
            throw CouldNotSendNotification::bSmsRespondedWithAnError($exception);
        } catch (\Exception $exception) {
            throw CouldNotSendNotification::couldNotCommunicateWithPortToSms($exception);
        }
    }
}
