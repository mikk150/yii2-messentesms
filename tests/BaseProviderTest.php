<?php

namespace yiiunit;

use mikk150\messentesms\Message;
use mikk150\messentesms\Provider;
use yii\httpclient\Client;
use yii\httpclient\Transport;
use yii\httpclient\Response;
use yii\httpclient\Request;
use Yii;

/**
 *
 */
class BaseProviderTest extends TestCase
{
    public function testSendingMessage()
    {
        $providerMock = $this->getMockBuilder(Provider::className())
            ->setMethods(['getClient'])
            ->setConstructorArgs([[
                'username' => 'testuser',
                'password' => 'testpassword'
            ]])
            ->getMock();

        $transportMock = $this->getMockBuilder(Transport::className())
            ->setMethods(['send'])
            ->getMock();

        $transportMock->expects($this->once())
            ->method('send')
            ->with($this->callback(function ($request) {
                if ($request->data['text'] !== 'test') {
                    return false;
                }
                if ($request->data['to'] !== '12345') {
                    return false;
                }
                if ($request->data['username'] !== 'testuser') {
                    return false;
                }
                if ($request->data['password'] !== 'testpassword') {
                    return false;
                }
                if ($request->data['from'] !== 'test sender') {
                    return false;
                }

                return true;
            }))->will($this->returnValue(new Response([
                'content' => 'OK 123456'
            ])));

        $providerMock->expects($this->any())->method('getClient')->will($this->returnValue(new Client([
            'transport' => $transportMock
        ])));

        $providerMock->compose('test')->setTo('12345')->setFrom('test sender')->send();
    }

    public function testSendingMessageToMultipleRecipients()
    {
        $providerMock = $this->getMockBuilder(Provider::className())
            ->setMethods(['getClient'])
            ->getMock();

        $transportMock = $this->getMockBuilder(Transport::className())
            ->setMethods(['send'])
            ->getMock();

        $transportMock->expects($this->exactly(2))->method('send')->will($this->returnValue(new Response([
            'content' => 'OK 123456'
        ])));

        $providerMock->expects($this->any())->method('getClient')->will($this->returnValue(new Client([
            'transport' => $transportMock
        ])));

        $providerMock->compose('test')->setTo(['12345', '2234'])->send();
    }

    /**
     * @dataProvider reportProvider
     */
    public function testFailedSendingMessage($response, $expected)
    {
        $providerMock = $this->getMockBuilder(Provider::className())
                             ->setMethods(['getClient'])
                             ->setConstructorArgs([[
                                'username' => 'testuser',
                                'password' => 'testpassword'
                             ]])
                             ->getMock();

        $transportMock = $this->getMockBuilder(Transport::className())
                              ->setMethods(['send'])
                              ->getMock();

        $transportMock->expects($this->once())
            ->method('send')
            ->with($this->callback(function ($request) {
                if ($request->data['text'] !== 'test') {
                    return false;
                }
                if ($request->data['to'] !== '12345') {
                    return false;
                }
                if ($request->data['username'] !== 'testuser') {
                    return false;
                }
                if ($request->data['password'] !== 'testpassword') {
                    return false;
                }
                if ($request->data['from'] !== 'test sender') {
                    return false;
                }

                return true;
            }))->will($this->returnValue(new Response([
                'content' => $response
            ])));

        $providerMock->expects($this->any())->method('getClient')->will($this->returnValue(new Client([
            'transport' => $transportMock
        ])));

        $sendResponse = $providerMock->compose('test')->setTo('12345')->setFrom('test sender')->send();

        $this->assertEquals($expected, $sendResponse);
    }

    public function reportProvider()
    {
        return [
            ['OK 12345678', true],
            ['ERROR 101', false],
            ['ERROR 102', false],
            ['ERROR 103', false],
            ['ERROR 111', false],
            ['FAILED 209', false],
        ];
    }

    public function testGetClient()
    {
        $provider = new Provider([
            'apiUrl' => 'test'
        ]);

        $this->assertEquals('test', $provider->getClient()->baseUrl);
    }
}
