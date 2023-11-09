<?php

use Illuminate\Support\Facades\Event;

use function Pest\Laravel\withHeaders;
use function Pest\Laravel\withoutMiddleware;

use Assist\Webhook\Http\Middleware\VerifyAwsSnsRequest;
use Assist\IntegrationAwsSesEventHandling\Events\SesOpenEvent;
use Assist\IntegrationAwsSesEventHandling\Events\SesSendEvent;
use Assist\IntegrationAwsSesEventHandling\Events\SesClickEvent;
use Assist\IntegrationAwsSesEventHandling\Events\SesBounceEvent;
use Assist\IntegrationAwsSesEventHandling\Events\SesRejectEvent;
use Assist\IntegrationAwsSesEventHandling\Events\SesDeliveryEvent;
use Assist\IntegrationAwsSesEventHandling\Events\SesComplaintEvent;
use Assist\IntegrationAwsSesEventHandling\Events\SesSubscriptionEvent;
use Assist\IntegrationAwsSesEventHandling\Events\SesDeliveryDelayEvent;
use Assist\IntegrationAwsSesEventHandling\Events\SesRenderingFailureEvent;

beforeEach(function () {
    Event::fake();

    withoutMiddleware(VerifyAwsSnsRequest::class);
});

it('handles a bounce event', function () {
    $snsData = $this->loadFixtureFromModule('integration-aws-ses-event-handling', 'sns-notification');

    $snsData['Message'] = json_encode($this->loadFixtureFromModule('integration-aws-ses-event-handling', 'bounce'));

    $response = withHeaders(
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
    )->postJson(
        route('inbound.webhook.awsses'),
        $snsData,
    );

    $response->assertOk();

    Event::assertDispatched(SesBounceEvent::class);
});

it('handles a complaint event', function () {
    $snsData = $this->loadFixtureFromModule('integration-aws-ses-event-handling', 'sns-notification');

    $snsData['Message'] = json_encode($this->loadFixtureFromModule('integration-aws-ses-event-handling', 'complaint'));

    $response = withHeaders(
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
    )->postJson(
        route('inbound.webhook.awsses'),
        $snsData,
    );

    $response->assertOk();

    Event::assertDispatched(SesComplaintEvent::class);
});

it('handles a delivery event', function () {
    $snsData = $this->loadFixtureFromModule('integration-aws-ses-event-handling', 'sns-notification');

    $snsData['Message'] = json_encode($this->loadFixtureFromModule('integration-aws-ses-event-handling', 'delivery'));

    $response = withHeaders(
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
    )->postJson(
        route('inbound.webhook.awsses'),
        $snsData,
    );

    $response->assertOk();

    Event::assertDispatched(SesDeliveryEvent::class);
});

it('handles a send event', function () {
    $snsData = $this->loadFixtureFromModule('integration-aws-ses-event-handling', 'sns-notification');

    $snsData['Message'] = json_encode($this->loadFixtureFromModule('integration-aws-ses-event-handling', 'send'));

    $response = withHeaders(
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
    )->postJson(
        route('inbound.webhook.awsses'),
        $snsData,
    );

    $response->assertOk();

    Event::assertDispatched(SesSendEvent::class);
});

it('handles a reject event', function () {
    $snsData = $this->loadFixtureFromModule('integration-aws-ses-event-handling', 'sns-notification');

    $snsData['Message'] = json_encode($this->loadFixtureFromModule('integration-aws-ses-event-handling', 'reject'));

    $response = withHeaders(
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
    )->postJson(
        route('inbound.webhook.awsses'),
        $snsData,
    );

    $response->assertOk();

    Event::assertDispatched(SesRejectEvent::class);
});

it('handles a open event', function () {
    $snsData = $this->loadFixtureFromModule('integration-aws-ses-event-handling', 'sns-notification');

    $snsData['Message'] = json_encode($this->loadFixtureFromModule('integration-aws-ses-event-handling', 'open'));

    $response = withHeaders(
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
    )->postJson(
        route('inbound.webhook.awsses'),
        $snsData,
    );

    $response->assertOk();

    Event::assertDispatched(SesOpenEvent::class);
});

it('handles a click event', function () {
    $snsData = $this->loadFixtureFromModule('integration-aws-ses-event-handling', 'sns-notification');

    $snsData['Message'] = json_encode($this->loadFixtureFromModule('integration-aws-ses-event-handling', 'click'));

    $response = withHeaders(
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
    )->postJson(
        route('inbound.webhook.awsses'),
        $snsData,
    );

    $response->assertOk();

    Event::assertDispatched(SesClickEvent::class);
});

it('handles a renderingFailure event', function () {
    $snsData = $this->loadFixtureFromModule('integration-aws-ses-event-handling', 'sns-notification');

    $snsData['Message'] = json_encode($this->loadFixtureFromModule('integration-aws-ses-event-handling', 'renderingFailure'));

    $response = withHeaders(
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
    )->postJson(
        route('inbound.webhook.awsses'),
        $snsData,
    );

    $response->assertOk();

    Event::assertDispatched(SesRenderingFailureEvent::class);
});

it('handles a DeliveryDelay event', function () {
    $snsData = $this->loadFixtureFromModule('integration-aws-ses-event-handling', 'sns-notification');

    $snsData['Message'] = json_encode($this->loadFixtureFromModule('integration-aws-ses-event-handling', 'DeliveryDelay'));

    $response = withHeaders(
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
    )->postJson(
        route('inbound.webhook.awsses'),
        $snsData,
    );

    $response->assertOk();

    Event::assertDispatched(SesDeliveryDelayEvent::class);
});

it('handles a Subscription event', function () {
    $snsData = $this->loadFixtureFromModule('integration-aws-ses-event-handling', 'sns-notification');

    $snsData['Message'] = json_encode($this->loadFixtureFromModule('integration-aws-ses-event-handling', 'Subscription'));

    $response = withHeaders(
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
    )->postJson(
        route('inbound.webhook.awsses'),
        $snsData,
    );

    $response->assertOk();

    Event::assertDispatched(SesSubscriptionEvent::class);
});
