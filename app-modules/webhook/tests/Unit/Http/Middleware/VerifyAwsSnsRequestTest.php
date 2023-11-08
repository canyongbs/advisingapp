<?php

use Mockery\MockInterface;
use Illuminate\Http\Request;
use Aws\Sns\MessageValidator;

use function Pest\Laravel\mock;

use Assist\Webhook\Http\Middleware\VerifyAwsSnsRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

it('will abort the request if the request cannot be verified to have originated from AWS SNS', function () {
    mock(MessageValidator::class, function (MockInterface $mock) {
        $mock->shouldReceive('isValid')->andReturn(false);
    });

    $request = Request::create(
        uri: route('inbound.webhook.awsses'),
        method: 'POST',
        content: json_encode($this->loadFixtureFromModule('webhook', 'sns-notification')),
    );

    $request->headers->add(
        [
            'x-amz-sns-message-type' => 'Notification',
            'x-amz-sns-message-id' => '22b80b92-fdea-4c2c-8f9d-bdfb0c7bf324',
            'x-amz-sns-topic-arn' => 'arn:aws:sns:us-west-2:123456789012:MyTopic',
            'x-amz-sns-subscription-arn' => 'arn:aws:sns:us-west-2:123456789012:MyTopic:c9135db0-26c4-47ec-8998-413945fb5a96',
            'Content-Length' => '773',
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Host' => 'example.com',
            'Connection' => 'Keep-Alive',
            'User-Agent' => 'Amazon Simple Notification Service Agent',
        ]
    );

    $next = function () {
        return response('test');
    };

    (new VerifyAwsSnsRequest())->handle($request, $next);
})->throws(exception: NotFoundHttpException::class);

it('will process the request if the request can be verified to have originated from AWS SNS', function () {
    mock(MessageValidator::class, function (MockInterface $mock) {
        $mock->shouldReceive('isValid')->andReturn(true);
    });

    $request = Request::create(
        uri: route('inbound.webhook.awsses'),
        method: 'POST',
        content: json_encode($this->loadFixtureFromModule('webhook', 'sns-notification')),
    );

    $request->headers->add(
        [
            'x-amz-sns-message-type' => 'Notification',
            'x-amz-sns-message-id' => '22b80b92-fdea-4c2c-8f9d-bdfb0c7bf324',
            'x-amz-sns-topic-arn' => 'arn:aws:sns:us-west-2:123456789012:MyTopic',
            'x-amz-sns-subscription-arn' => 'arn:aws:sns:us-west-2:123456789012:MyTopic:c9135db0-26c4-47ec-8998-413945fb5a96',
            'Content-Length' => '773',
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Host' => 'example.com',
            'Connection' => 'Keep-Alive',
            'User-Agent' => 'Amazon Simple Notification Service Agent',
        ]
    );

    $next = function () {
        return response('test');
    };

    $response = (new VerifyAwsSnsRequest())->handle($request, $next);

    expect($response->getContent())->toBe('test')
        ->and($response->getStatusCode())->toBe(200);
});
