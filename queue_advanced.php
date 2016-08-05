<?php
/**----------------------------------------------------------------------------------
* Microsoft Developer & Platform Evangelism
*
* Copyright (c) Microsoft Corporation. All rights reserved.
*
* THIS CODE AND INFORMATION ARE PROVIDED "AS IS" WITHOUT WARRANTY OF ANY KIND, 
* EITHER EXPRESSED OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE IMPLIED WARRANTIES 
* OF MERCHANTABILITY AND/OR FITNESS FOR A PARTICULAR PURPOSE.
*----------------------------------------------------------------------------------
* The example companies, organizations, products, domain names,
* e-mail addresses, logos, people, places, and events depicted
* herein are fictitious.  No association with any real company,
* organization, product, domain name, email address, logo, person,
* places, or events is intended or should be inferred.
*----------------------------------------------------------------------------------
**/

/** -------------------------------------------------------------
* Azure Queue Service Sample - The Queue Service provides reliable messaging for workflow processing and for communication 
* between loosely coupled components of cloud services. This sample demonstrates how to perform common tasks including 
* inserting, peeking, getting and deleting queue messages, as well as creating and deleting queues. 
* 
* Documentation References: 
* - What is a Storage Account - http://azure.microsoft.com/en-us/documentation/articles/storage-whatis-account/
* - Getting Started with Queues - https://azure.microsoft.com/en-us/documentation/articles/storage-php-how-to-use-queues/
* - Queue Service Concepts - http://msdn.microsoft.com/en-us/library/dd179353.aspx
* - Queue Service REST API - http://msdn.microsoft.com/en-us/library/dd179363.aspx
* - Azure Storage PHP API - https://github.com/Azure/azure-sdk-for-php/
* - Storage Emulator - http://msdn.microsoft.com/en-us/library/azure/hh403989.aspx
* -------------------------------------------------------------
**/

namespace MicrosoftAzure\Storage\Samples;
require_once "vendor/autoload.php";
require_once "./config.php";
require_once "./random_string.php";

use Config;
use MicrosoftAzure\Storage\Common\ServicesBuilder;
use MicrosoftAzure\Storage\Common\ServiceException;
use MicrosoftAzure\Storage\Common\Models\Logging;
use MicrosoftAzure\Storage\Common\Models\Metrics;
use MicrosoftAzure\Storage\Common\Models\RetentionPolicy;
use MicrosoftAzure\Storage\Common\Models\ServiceProperties;

class QueueAdvancedSamples
{
    public function runAllSamples() {
      $connectionString = Config::getConnectionString();
      $queueService = ServicesBuilder::getInstance()->createQueueService($connectionString);

      try {
        echo PHP_EOL;
        echo "* Queue metadata *".PHP_EOL;
        $this->queueMetadata($queueService);

        echo PHP_EOL;
        echo "* Queue service properties *".PHP_EOL;
        $this->queueServiceProperties($queueService);

      }
      catch(ServiceException $e) {
        echo "Error occurred in the sample.".$e->getMessage().PHP_EOL;
      }
    }


    // Get and Set Queue Metadata
    function queueMetadata($queueService){
        $queueName = "queue" . generateRandomString();
        
        // Create queue
        echo "Create queue" . PHP_EOL;
        $queueService->createQueue($queueName);

        // Set queue metadata
        echo "Set queue metadata" . PHP_EOL;
        $metadata = array(
            'key' => 'value',
            'foo' => 'bar',
            'baz' => 'boo');
        $queueService->setQueueMetadata($queueName, $metadata);

        echo "Get queue metadata" . PHP_EOL;
        // Get queue properties
        $properties = $queueService->getQueueMetadata($queueName);

        foreach ($properties->getMetadata() as $key => $value) {
            echo $key . ": " . $value . PHP_EOL; 
        }

        echo "Delete queue" . PHP_EOL;
        $queueService->deleteQueue($queueName);
    }

    // Get and Set Queue Service Properties
    function queueServiceProperties($queueService) {
        // Get queue service properties
        echo "Get Queue Service properties" . PHP_EOL;
        $originalProperties = $queueService->getServiceProperties();

        // Set queue service properties
        echo "Set Queue Service properties" . PHP_EOL;
        $retentionPolicy = new RetentionPolicy();
        $retentionPolicy->setEnabled(true);
        $retentionPolicy->setDays(10);
        
        $logging = new Logging();
        $logging->setRetentionPolicy($retentionPolicy);
        $logging->setVersion('1.0');
        $logging->setDelete(true);
        $logging->setRead(true);
        $logging->setWrite(true);
        
        $metrics = new Metrics();
        $metrics->setRetentionPolicy($retentionPolicy);
        $metrics->setVersion('1.0');
        $metrics->setEnabled(true);
        $metrics->setIncludeAPIs(true);

        $serviceProperties = new ServiceProperties();
        $serviceProperties->setLogging($logging);
        $serviceProperties->setMetrics($metrics);

        $queueService->setServiceProperties($serviceProperties);
        
        // revert back to original properties
        echo "Revert back to original service properties" . PHP_EOL;
        $queueService->setServiceProperties($originalProperties->getValue());

        echo "Service properties sample completed" . PHP_EOL;
    }   
}

?>