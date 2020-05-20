<?php
  require_once dirname(__DIR__).'/{VENDOR_DIR}/autoload.php';
  use equilibrium\Bootstrap;
  $httpDir = getenv('APP_HTTP_DIRECTORY');
  $httpDir = !empty($_ENV['REDIRECT_APP_HTTP_DIRECTORY'])?$_ENV['REDIRECT_APP_HTTP_DIRECTORY']:!empty($httpDir)?$httpDir:'/';
  $b = new Bootstrap($httpDir);
  $b->run();
