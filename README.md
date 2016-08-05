---
services: storage
platforms: php
author: woodp
---

# Azure Storage: Getting Started with Azure Storage in PHP
Samples documenting basic operations with Azure Queue storage services in PHP. 

## Running this sample
This sample can be run using either the Azure Storage Emulator (Windows) or by using your Azure Storage account name and key. Please update the config.php file with the appropriate properties.

To run the sample using the Storage Emulator:
1. Download and install the Azure Storage Emulator https://azure.microsoft.com/en-us/downloads/ 
2. Start the emulator (once only) by pressing the Start button or the Windows key and searching for it by typing "Azure Storage Emulator". Select it from the list of applications to start it.
3. Run the project. 

To run the sample using the Storage Service
1. Open the config.rb file and set the config setting IS_EMULATED to false. Also configure the settings ACCOUNT_NAME and ACCOUNT_KEY with the account name and account key from your subscription. See https://azure.microsoft.com/en-us/documentation/articles/storage-create-storage-account/ for more information
3.Set breakpoints and run the project. 

## Deploy this sample 

Either fork the sample to a local folder or download the zip file from https://github.com/Azure-Samples/storage-queue-php-getting-started/

To get the source code of the SDK via git, type:
git clone git://github.com/Azure-Samples/storage-queue-php-getting-started.git
cd .\storage-queue-php-getting-started

##Minimum Requirements
PHP 5.5 or above
To install PHP, please go to http://php.net/

## More information
  - What is a Storage Account - http://azure.microsoft.com/en-us/documentation/articles/storage-whatis-account/  
  - Getting Started with Queues - https://azure.microsoft.com/en-us/documentation/articles/storage-php-how-to-use-queues/
  - Queue Service Concepts - https://msdn.microsoft.com/en-us/library/azure/dd179353.aspx 
  - Queue Service REST API - https://msdn.microsoft.com/en-us/library/azure/dd179363.aspx 
  - Storage Emulator - http://azure.microsoft.com/en-us/documentation/articles/storage-use-emulator/