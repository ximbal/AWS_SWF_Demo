<?php

require_once "path/to/aws.phar";

use Aws\Swf\SwfClient;

// Ask SWF for things the decider needs to know
$result = $client->pollForDecisionTask(array(
    "domain" => "your domain name",
    "taskList" => array(
        "name" => "mainTaskList"
    ),
    "identify" => "default",
    "maximumPageSize" => 50,
    "reverseOrder" => true
));

// Current version of activity types we are using
$activity_type_version = "1.0";

// Parse info we need returned from the pollForDecisionTask call
$task_token = $result["taskToken"];
$workflow_id = $result["workflowExecution"]["workflowId"];
$run_id = $result["workflowExecution"]["runId"];
$last_event = $result["events"][0]["eventId"];

// Our logic that decides what happens next
if($last_event == "3"){
    $activity_type_name = "activity to start if last event ID was 3";
    $task_list = "mainTaskList";
    $activity_id = "1";
    $continue_workflow = true;
}
elseif($last_event == "8"){
    $activity_type_name = "activity to start if last event ID was 8";
    $task_list = "mainTaskList";
    $activity_id = "2";
    $continue_workflow = false;
}

// Now that we populated our variables based on what we received from SWF, we need to tell SWF what we want to do next
if($continue_workflow === true){
    $client->respondDecisionTaskCompleted(array(
        "taskToken" => $task_token,
        "decisions" => array(
            array(
                "decisionType" => "ScheduleActivityTask",
                "scheduleActivityTaskDecisionAttributes" => array(
                    "activityType" => array(
                        "name" => $activity_type_name,
                        "version" => $activity_type_version
                    ),
                    "activityId" => $activity_id,
                    "control" => "this is a sample message",
                    // Customize timeout values
                    "scheduleToCloseTimeout" => "360",
                    "scheduleToStartTimeout" => "300",
                    "startToCloseTimeout" => "60",
                    "heartbeatTimeout" => "60",
                    "taskList" => array(
                        "name" => $task_list
                    ),
                    "input" => "this is a sample message"
                )
            )
        )
    ));
}
// End workflow if last event ID was 8
else if($continue_workflow === false){
    $client->respondDecisionTaskCompleted(array(
        "taskToken" => $task_token,
        "decisions" => array(
            array(
                "decisionType" => "CompleteWorkflowExecution"
            )
        )
    ));
}