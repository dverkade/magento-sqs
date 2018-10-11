<?php
/**
 *  @package BelVG AWS Sqs.
 *  @copyright 2018
 *
 */

namespace Belvg\Sqs\Model;

use Magento\Framework\MessageQueue\Topology\Config\ExchangeConfigItem\BindingInterface;
use Magento\Framework\MessageQueue\Topology\ConfigInterface as TopologyConfig;
use Magento\Framework\MessageQueue\EnvelopeInterface;
use Magento\Framework\MessageQueue\ExchangeInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;

class Exchange implements ExchangeInterface
{
    /**
     * @var TopologyConfig
     */
    private $topologyConfig;

    /**
     * @var QueueFactory
     */
    private $queueFactory;

    /**
     * @var array
     */
    private $queues = [];

    /**
     * @var string
     */
    const DESTINATION_TYPE = 'queue';


    /**
     * Exchange constructor.
     * @param TopologyConfig $topologyConfig
     * @param QueueFactory $queueFactory
     */
    public function __construct(
        TopologyConfig $topologyConfig,
        QueueFactory $queueFactory
    )
    {
        $this->topologyConfig = $topologyConfig;
        $this->queueFactory = $queueFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function enqueue($topic, EnvelopeInterface $envelope)
    {
        $exchange = $this->topologyConfig->getExchange($topic, Config::SQS_CONFIG);

        if (!$exchange) {
            throw new LocalizedException(
                new Phrase('Exchange for "%topic" is not configured.', ['topic' => $topic])
            );
        }

        foreach ($exchange->getBindings() as $binding) {
            if ($this->isValidBinding($topic, $binding)) {
                $queue = $this->createQueue($binding->getDestination());
                $queue->push($envelope);
            }
        }

        return null;
    }

    /**
     * @param $queueName same as queue name
     * @return Queue
     */
    protected function createQueue($queueName)
    {
        if (!isset($this->queues[$queueName])) {
            $this->queues[$queueName] = $this->queueFactory->create($queueName);
        }

        return $this->queues[$queueName];
    }

    /**
     * @param $topic
     * @param BindingInterface $binding
     * @return bool
     */
    private function isValidBinding($topic, BindingInterface $binding)
    {
        return $binding->getTopic() == $topic &&
            $binding->getDestinationType() == self::DESTINATION_TYPE &&
            !$binding->isDisabled();
    }
}
