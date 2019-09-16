<?php
/*
get token: https://oauth.vk.com/authorize?client_id=6121396&scope=65536&redirect_uri=https://oauth.vk.com/blank.html&display=page&response_type=token&revoke=1

tested on php v7.0
*/

require_once dirname(__FILE__).'/vk-lib/vk.class.php';

define('ACCESS_TOKEN', 'token');

$vk = new VkHelper;

switch ($argv[1]) {
  case !isset($argv[1]):
      echo "Enter id of user.\nExample: php execute.php 1\n"; // durov
      break;

  case !is_numeric($argv[1]):
      echo "User id should be integer.\n";
      break;

  case (isset($argv[1]) && is_numeric($argv[1])):
      $result = $vk->get_friends($argv[1]); // check private profile & get all friends
      if(isset($result->execute_errors[0])){
        echo 'VK API error msg: '.$result->execute_errors[0]->error_msg."\n";
      }else{
        $txtFile = fopen("users.txt", "wr") or die("Unable to open users.txt");
        fwrite($txtFile, implode(PHP_EOL, $result->friends).PHP_EOL);

        echo 'Received '.$result->total.' friends.'."\nGetting followers...\n";

        $offset = 0;
        $result = $vk->get_followers($argv[1], 0);

        fwrite($txtFile, implode(PHP_EOL, $result->followers));

        while($result->total !== $offset){
          $offset = $offset+$result->get;

          $result = $vk->get_followers($argv[1], $offset);

          fwrite($txtFile, implode(PHP_EOL, $result->followers));

          usleep(350000); //avoid api limit
        }

        fclose($txtFile);
      }

      break;
}
