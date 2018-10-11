# Magento SQS MessageQueue

With this module you are able to connect to Amazon SQS from Magento to publish and consume messages. Use it like other queues in Magento 2 

Compatible with:
* Magento 2.1 EE
* Magento 2.2 Commerce
* Magento 2.3-alpha Open Source and Commerce
 
# Installation #
 
## Via composer ##
 
To install this package with composer you need access to the command line of your server and you need to have Composer. Install with the following commands:
 
```
#!bash
 
cd <your magento path>
composer require belvg/module-sqs:dev-master
php bin/magento setup:upgrade
php bin/magento cache:clean
php bin/magento setup:static-content:deploy
```
 
## Manually ##
 
To install this package manually you need access to your server file system and you need access to the command line of your server. And take the following steps:
 
* Download the zip file from the Bitbucket repository.
* Upload the contents to <your magento path>/{path name}.
* Execute the following commands:
 
```
#!bash
cd <your magento path>
php bin/magento setup:upgrade
php bin/magento cache:clean
phpbin/magento setup:static-content:deploy
```

## Configuration ##

Add the SQS queue configuration to the env.php in order to connet to Amazon SQS.
```
    'queue' => [
        'sqs' => [
            'region' => 'eu-west-1',
            'prefix' => 'development',
            'version' => 'latest',
            'access_key' => 'access_key',
            'secret_key' => 'secret_key',
            'endpoint' => 'http://localstack:4576/'
        ]
    ]
```

* region: The name of the region in Amazon to use.
* prefix: A variable to will be prefixed to the name of the queue. 
* version: The version to use.
* access_key: Your AWS access key.
* secret_key: Your AWS secret key.
* endpoint: Overwrite the region, you can specify a specific endpoint to use, i.e. for the use of SQS on Localstack.

# Usage #
 
## Publisher ##

communication.xml
```
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="urn:magento:framework-message-queue:etc/communication.xsd">

    <!-- Topic is defined here -->
    <topic name="sg_erp_test_topic" request="SG\ERP\Api\Data\TestTopicMessageInterface" />

</config>
```

queue_publisher.xml
```
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="urn:magento:framework-message-queue:etc/publisher.xsd">

    <!-- Setting exchange as sg_erp_test_topic for all messages posted to sg_erp_test_topic -->
    <publisher topic="sg_erp_test_topic">
        <connection name="sqs" exchange="sg_erp_exchange" />
    </publisher>
</config>
```

queue_topology.xml
```
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework-message-queue:etc/topology.xsd">

    <!-- Exchange is defined here -->
    <exchange name="sg_erp_exchange" type="topic" connection="sqs" />

</config>
```

## Consumer ##

queue_consumer.xml
```
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="urn:magento:framework-message-queue:etc/consumer.xsd">

    <!-- Defines a consumer for our test topic -->
    <consumer name="sg_inventory_erp_test_queue_consumer" queue="sg_erp_test_topic" connection="sqs"
              consumerInstance="Magento\Framework\MessageQueue\Consumer"
              handler="SG\Inventory\Model\ResourceModel\ConsumerTest::process"/>
</config>
```

queue_topology.xml
```
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework-message-queue:etc/topology.xsd">

    <!-- Binds the queue sg_erp_test_inventory_queue to the topic sg_erp_test_topic -->
    <exchange name="sg_erp_exchange" connection="sqs">
        <binding id="sg_erp_sg_inventory_test_binding" topic="sg_erp_test_topic"
                 destinationType="queue" destination="sg_erp_test_inventory_queue"/>
    </exchange>
</config>
```
