### AWS EC2 Snapshots Management (terminal application only)

*Hosted on NanoAppstore.com: App Home Folder @ /home/nano/ec2-snapshots-manager*

## Steps To Run A Task
1. Go to the App Home Folder
2. The command to run a task is *app/console {NAME_OF_TASK_PHP_CLASS_FILE} --execute {COMMAND}*
3. Alternatively you can go into the app folder and run the following
4. *./console {NAME_OF_TASK_PHP_CLASS_FILE} --execute {COMMAND}*
5. For example, *app/console RedmineHousekeeping --execute create*
6. For additional command line options run, *app/console {NAME_OF_TASK_PHP_CLASS_FILE} --help*

## Server Requirements
* Ubuntu 14.04+
* PHP 5.6+
* Composer

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
* Sample Task definition is @ app/Tasks/RedmineHousekeeping.php
* Sample Job schedule is @ jobs/RedmineHousekeepingTasks.php
* Definitions @ app/Interfaces/TaskTemplates.php and @ app/Lib/BaseTask.php on how to create a Task class
* Class for handling AWS EC2 describe/create/delete methods @ app/Lib/Ec2SnapshotsManager.php
