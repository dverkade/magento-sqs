<?php
/**
 *  @package BelVG AWS Sqs.
 *  @copyright 2018
 *
 */
namespace Belvg\Sqs\Test\Unit\Model;

use Belvg\Sqs\Model\Config;
use Belvg\Sqs\Model\Exchange;
use Belvg\Sqs\Model\Queue;
use Belvg\Sqs\Model\QueueFactory;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\MessageQueue\Envelope;

class ExchangeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var QueueFactory
     */
    private $queueFactory;

    /**
     * @var Queue
     */
    private $queue;

    /**
     * @var Config
     */
    private $sqsConfig;

    /**
     * @var Envelope
     */
    private $envelope;

    /**
     * @var Exchange
     */
    private $exchange;

    /**
     * @var string
     */
    const TOPIC_NAME = 'testtopic';

    protected function setUp()
    {
        $this->objectManager = new ObjectManager($this);

        $this->queueFactory = $this->getMockBuilder(QueueFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->queue = $this->getMockBuilder(Queue::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->envelope = $this->getMockBuilder(Envelope::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->exchange = $this->objectManager->getObject(
            'Belvg\Sqs\Model\Exchange',
            [
                'queueFactory' => $this->queueFactory
            ]
        );
    }

    public function testEnqueue()
    {

        $this->queueFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->queue));

        $this->queue->expects($this->once())
            ->method('push')
            ->with($this->envelope);

        $this->exchange->enqueue(self::TOPIC_NAME, $this->envelope);
    }
}
