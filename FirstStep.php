<?php

require_once "path/to/aws.phar";

use Aws\Swf\SwfClient;

// Create an instance of the SWF class
$client = SwfClient::factory(array(
    "key" => "your_aws_key",
    "secret" => "your_aws_secret_key",
    "region" => "your_aws_region"
));

// Register your domain
$client->registerDomain(array(
    "name" => "domain name you want",
    "description" => "this is a test domain",
    "workflowExecutionRetentionPeriodInDays" => "7"
));

// Register your workflow
$client->registerWorkflowType(array(
    "domain" => "domain name you registered in previous call",
    "name" => "workflow name you want",
    "version" => "1.0",
    "description" => "this is a sample",
    "defaultTaskList" => array(
        "name" => "mainTaskList"
    ),
    "defaultChildPolicy" => "TERMINATE"
));

// Register an activity
$client->registerActivityType(array(
    "domain" => "domain name you registered above",
    "name" => "activity name you want",
    "version" => "1.0",
    "description" => "first activity in our workflow",
    "defaultTaskList" => array(
        "name" => "mainTaskList"
    )
));