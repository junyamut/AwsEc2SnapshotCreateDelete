# AWS EC2 Snapshots Create and Delete

#### (terminal application only)

*Description: Supposedly a simple AWS snapshots create and delete terminal app*

## Intro

A terminal application to manage Snapshots of AWS EC2 volumes by creating a new snapshot and deleting old ones as necessary.

## Requirements
* Ubuntu 14.04+
* PHP 5.6+
* Composer

## Steps To Run A Task
1. Go to the App Home Folder
2. The command to run a task is *app/console {NAME_OF_TASK_PHP_CLASS_FILE} --execute {COMMAND}*
3. Alternatively you can go into the app folder and run the following
4. *./console {NAME_OF_TASK_PHP_CLASS_FILE} --execute {COMMAND}*
5. For example, *app/console KallitheaHousekeeping --execute create*
6. For additional command line options run, *app/console {NAME_OF_TASK_PHP_CLASS_FILE} --help*

## Other Dependencies
1. AWS SDK for PHP 3.x
2. piwik/ini - read/write to .INI files
3. monolog/monolog - write log messages to disk
4. kmelia/monolog-stdout-handler - print log messages to standard out (dependent on #3)
5. lavary/crunz - job scheduler
6. nategood/commando - CLI interface

**These packages are all available through Composer or via its respective repositories. Each comes with its own instructions and examples, so go have a look (re:RTFM!)**

## Things To Note
* Supply proper values @ app/Config/settings.ini. It is self-explanatory.
* Your AWS credentials must be @ app/Config/credentials.ini with permissions for EC2 describe/create/delete snapshots
* Sample Task definition is @ app/Tasks/KallitheaHousekeeping.php
* Sample Job schedule is @ jobs/KallitheaHousekeepingTasks.php
* Definitions @ app/Interfaces/TaskTemplates.php and @ app/Lib/BaseTask.php on how to create a Task class
* Class for handling AWS EC2 describe/create/delete methods @ app/Lib/Ec2SnapshotsManager.php
