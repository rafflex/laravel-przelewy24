<?php
declare(strict_types=1);

namespace Tests\Feature\Requests\RefundNotificationRequest;

use Devpark\Transfers24\Contracts\Refund;
use Devpark\Transfers24\Models\RefundQuery;
use Devpark\Transfers24\Requests\RefundNotificationRequest;
use Devpark\Transfers24\Responses\InvalidResponse;
use Devpark\Transfers24\Responses\NotificationResponse;
use Devpark\Transfers24\Responses\RefundResponse;
use Devpark\Transfers24\Services\Amount;
use Devpark\Transfers24\Services\Gateways\ClientFactory;
use GuzzleHttp\Client;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;
use Mockery as m;
use Mockery\ExpectationInterface;
use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\Test\TestLogger;
use Ramsey\Uuid\UuidFactory;
use Tests\UnitTestCase;

class RefundNotificationRequestTest extends UnitTestCase
{
    use RefundNotificationRequestTrait;
    /**
     * @var RefundNotificationRequest
     */
    private $request;

    /**
     * @var MockInterface
     */
    private $client;
    /**
     * @var MockInterface
     */
    private $entry;

    protected function setUp()
    {
        parent::setUp();

        $this->entry = m::mock(Request::class);
        $this->request = $this->app->make(RefundNotificationRequest::class,[
            'request' => $this->entry,
        ]);
    }

    /**
     * @Feature Refund
     * @Scenario notify Refund
     * @Case Refund was started
     * @test
     */
    public function refund_notification_was_received()
    {
        //Given
        $notification = $this->makeRefundNotification();
        $received = $this->whenReceiveNotification($notification);

        //When
        $response = $this->request->execute();

        //Then
        $this->assertSame($notification->orderId, $response->getNotification()->orderId);
        $this->assertSame($notification->sessionId, $response->getNotification()->sessionId);
        $this->assertSame($notification->merchantId, $response->getNotification()->merchantId);
        $this->assertSame($notification->requestId, $response->getNotification()->requestId);
        $this->assertSame($notification->refundsUuid, $response->getNotification()->refundsUuid);
        $this->assertSame($notification->amount, $response->getNotification()->amount);
        $this->assertSame($notification->currency, $response->getNotification()->currency);
        $this->assertSame($notification->timestamp, $response->getNotification()->timestamp);
        $this->assertSame($notification->status, $response->getNotification()->status);
        $this->assertSame($notification->sign, $response->getNotification()->sign);
        $this->assertSame('ok', $response->getResponse());
        $received->once();

    }

    private function whenReceiveNotification($notification): ExpectationInterface
    {
        $refund_notification_raw = $notification->toArray();
        return $this->entry->shouldReceive('all')->andReturn($refund_notification_raw);
    }
}
