<?php
// cli-config.php
require_once __DIR__ . "/../app/config/bootstrap.php";

return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($entityManager);
