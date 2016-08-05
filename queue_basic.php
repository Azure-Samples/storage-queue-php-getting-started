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
use MicrosoftAzure\Storage\Queue\Models\CreateQueueOptions;
use MicrosoftAzure\Storage\Queue\Models\PeekMessagesOptions;
use MicrosoftAzure\Storage\Queue\Models\ListMessagesOptions;

class QueueBasicSamples
{
    public function runAllSamples() {
      $connectionString = Config::getConnectionString();
      $queueService = ServicesBuilder::getInstance()->createQueueService($connectionString);

      try {
        echo PHP_EOL;
        echo "* Basic queue operations *".PHP_EOL;
        $this->basicQueueOperations($queueService);
        echo PHP_EOL;
        echo "* Basic messag oeperations *".PHP_EOL;
        $this->basicMessageOperations($queueService);
      }
      catch(ServiceException $e) {
        echo "Error occurred in the sample.".$e->getMessage().PHP_EOL;
      }
    }

    function basicQueueOperations($queueService){
      $queuePrefix = "queue-".generateRandomString();

      echo "Create multiple queues with prefix {$queuePrefix}".PHP_EOL;

      for ($i = 1; $i <= 5; $i++) {
        $queueService->createQueue($queuePrefix.(string)$i);
      }

      echo "List queues with prefix {$queuePrefix}".PHP_EOL;
      $queuesListResult = $queueService->listQueues();

      foreach ($queuesListResult->getQueues() as $queue) {
        echo "  queue ".$queue->getName().PHP_EOL;
      }

      for ($i = 1; $i <= 5; $i++) {
        $queueService->deleteQueue($queuePrefix.(string)$i);
      }
    }

    function basicMessageOperations($queueService){
        $queueName = "queue-".generateRandomString();

        echo "Create queue with name {$queueName}".PHP_EOL;

        $queueService->createQueue($queueName);

        /* 
          Add a number of messages to the queue.
          if you do not specify time_to_live, the message will expire after 7 days
          if you do not specify visibility_timeout, the message will be immediately visible
        */
        $message = "test message ";
        for($i = 1; $i <= 10; $i++){
            $queueService->createMessage($queueName, $message.(string)$i);
            echo "Successfully added message: ".$message.(string)$i.PHP_EOL;
        }

        echo "Get number of messages in the queue {$queueName}".PHP_EOL;

        $metadataResult = $queueService->getQueueMetadata($queueName);

        echo "Approximate length of the queue: ".$metadataResult->getApproximateMessageCount().PHP_EOL;

        echo "Peek first message from queue without changing visibility".PHP_EOL;
        
        # Look at the first message without dequeueing it
        $peekMessagesResult = $queueService->peekMessages($queueName);

        foreach ($peekMessagesResult->getQueueMessages() as $message) {
          echo "  Peeked message content is: ".$message->getMessageText().PHP_EOL;
        }

        $listMessagesOptions = new ListMessagesOptions();
        $listMessagesOptions->setNumberOfMessages(5);
        $listMessagesOptions->setVisibilityTimeoutInSeconds(5);

        $listMessagesResult = $queueService->listMessages($queueName, $listMessagesOptions);

        foreach ($listMessagesResult->getQueueMessages() as $message) {
          echo "  Dequeue message content is: ".$message->getMessageText().PHP_EOL;

          /*
            Then delete it. 
            Deleting requires the message id and pop receipt (returned by get_messages)
            Attempt for 60 seconds. Timeout if it does not complete by that time.
          */
          $queueService->deleteMessage($queueName, $message->getMessageId(), $message->getPopReceipt());
          echo "Successfully dequeued message".PHP_EOL;
        }

        # Clear out all messages from the queue
        $queueService->clearMessages($queueName);
        echo "Successfully cleared out all queue messages".PHP_EOL;

        echo "Delete queue with name {$queueName}".PHP_EOL;
        $queueService->deleteQueue($queueName);
    }
}
?>