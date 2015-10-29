<?php
/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

require_once("internals.php");

require_once("lib/RetriggerController.php");
require_once("lib/DB/TaskQueue.php");
require_once("lib/DB/QueuedTask.php");

init_database();

if ($unit = GET_int("unit")) {

    $queue = new TaskQueue($unit);
    if ($queue->has_active_task())
		slack("requesting new task, while old task is still running!");

    if (!$queue->has_queued_tasks()) {
        $retrigger = RetriggerController::fromUnit($unit);
        $retrigger->enqueue();
	}

    $task = $queue->pop();

	echo json_encode(Array(
        "task" => $task->task(),
        "id" => $task->id()
    ));

    die();

} else if ($task_id = GET_int("finish")) {

    $task = QueuedTask($task_id);
    $task->setFinished();

	die();
}