<?php
/**
 *  @package BelVG AWS Sqs.
 *  @copyright 2018
 *
 */
namespace Belvg\Sqs\Test\Unit\Model;

use Belvg\Sqs\Model\Config;
use Belvg\Sqs\Model\Queue;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\MessageQueue\Envelope;
use Enqueue\Sqs\SqsContext;
use Enqueue\Sqs\SqsConsumer;
use Enqueue\Sqs\SqsDestination;
use Enqueue\Sqs\SqsMessage;

class QueueTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Queue
     */
    private $topology;

    /**
     * @var SqsContext
     */
    private $context;

    /**
     * @var SqsDestination
     */
    private $consumer;

    /**
     * @var SqsDestination
     */
    private $destination;

    /**
     * @var SqsMessage
     */
    private $message;

    /**
     * @var Config
     */
    private $sqsConfig;

    const QUEUE_NAME = 'testqueue';

    protected function setUp()
    {
        $this->objectManager = new ObjectManager($this);

        $this->context = $this->getMockBuilder(SqsContext::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->consumer = $this->getMockBuilder(SqsConsumer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->destination = $this->getMockBuilder(SqsDestination::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->sqsConfig = $this->getMockBuilder(Config::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->envelope = $this->getMockBuilder(Envelope::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->message = $this->getMockBuilder(SqsMessage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->queue = $this->objectManager->getObject(
            'Belvg\Sqs\Model\Queue',
            [
                'sqsConfig' => $this->sqsConfig,
                'queueName' => self::QUEUE_NAME
            ]
        );
    }

    public function testAcknowledge()
    {
        $this->sqsConfig->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($this->context));

        $this->context->expects($this->once())
            ->method('createQueue')
            ->with(self::QUEUE_NAME)
            ->will($this->returnValue($this->destination));

        $this->context->expects($this->once())
            ->method('createMessage')
            ->will($this->returnValue($this->message));

        $this->context->expects($this->once())
            ->method('createConsumer')
            ->will($this->returnValue($this->consumer));

        $this->envelope->expects($this->once())
            ->method('getProperties')
            ->will($this->returnValue([]));

        $this->queue->acknowledge($this->envelope);
    }

    public function testDequeue()
    {
        $this->sqsConfig->expects($this->exactly(2))
            ->method('getConnection')
            ->will($this->returnValue($this->context));

        $this->context->expects($this->once())
            ->method('createQueue')
            ->with(self::QUEUE_NAME)
            ->will($this->returnValue($this->destination));

        $this->context->expects($this->once())
            ->method('createConsumer')
            ->will($this->returnValue($this->consumer));

        $this->queue->dequeue();
    }
}
