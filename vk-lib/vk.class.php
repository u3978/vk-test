<?php
// VKScript compressed in one line
class VkHelper
{
  public function get_friends($u_id) {
     $result = $this->vk_execute_q('var offset = 0; var n = 1; var q_friends = API.friends.get({"user_id": "'.$u_id.'", "count":5000, "offset": offset}); var friends = q_friends.items; while (offset < q_friends.count && (n < 25)){ offset = offset + 5000; n = n+1; friends = friends + API.friends.get({"user_id": "'.$u_id.'", "count": 5000, "offset": offset}).items;}; return {total: friends.length, friends: friends};');

     if(isset($result->execute_errors)){
       return $result->execute_errors;
     }else{
       return $result->response;
     }
  }

  public function get_followers($u_id, $offset) {
     $result = $this->vk_execute_q('var offset = '.$offset.'; var n = 1; var q_followers = API.users.getFollowers({"user_id": "'.$u_id.'", "count":1000, "offset": offset}); var followers = q_followers.items; while (offset < q_followers.count && (n < 25)){ offset = offset + 1000; n = n+1; followers = followers + API.users.getFollowers({"user_id": "'.$u_id.'", "count": 1000, "offset": offset}).items;}; return {total: q_followers.count, get: followers.length, followers: followers};');

     if(isset($result->execute_errors)){
       return $result->execute_errors;
     }else{
       return $result->response;
     }
  }

  private function vk_execute_q($code) {
    $url = 'https://api.vk.com/method/execute';
    $data = array(
      'access_token' => ACCESS_TOKEN,
      'v' => 5.101, // last version
      'code' => $code
     );

    $options = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        )
    );

    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    return json_decode($result);
  }
}
