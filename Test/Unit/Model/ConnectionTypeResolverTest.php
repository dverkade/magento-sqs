<?php
/**
 *  @package BelVG AWS Sqs.
 *  @copyright 2018
 *
 */
namespace Belvg\Sqs\Test\Unit\Model;

use Belvg\Sqs\Model\ConnectionTypeResolver;
use Magento\Framework\App\DeploymentConfig;

class ConnectionTypeResolverTest extends \PHPUnit\Framework\TestCase
{
    public function testGetConnectionType()
    {
        $config = $this->createMock(DeploymentConfig::class);
        $config->expects($this->once())
            ->method('getConfigData')
            ->with('queue')
            ->will($this->returnValue(
                [
                    'sqs' => [
                        'region' => 'test',
                        'version' => 'latest',
                        'access_key' => '123456',
                        'secret_key' => '654321',
                        'prefix' => 'prefix',
                        'endpoint' => 'https://localstack:4567'
                    ],
                    'connections' => [
                        'connection-01' => [
                            'region' => 'test',
                            'version' => 'latest',
                            'access_key' => '123456',
                            'secret_key' => '654321',
                            'prefix' => 'prefix',
                            'endpoint' => 'https://localstack:4567'
                        ]
                    ]
                ]
            ));

        $model = new ConnectionTypeResolver($config);
        $this->assertEquals('sqs', $model->getConnectionType('connection-01'));
        $this->assertEquals('sqs', $model->getConnectionType('sqs'));
    }
}