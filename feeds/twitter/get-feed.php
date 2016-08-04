<?php


if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
class Twitter_Feed extends WPSF_Feed {

    public function __construct() {
        $this->feed = 'twitter';

        $twitter_options = WP_SocialFeeds::getOption($this->feed);
        extract($twitter_options);
        if (!empty($consumer_key) && !empty($consumer_secret) && !empty($secret) && !empty($token)) {
            //call twitter API & get contents using JSON. 
            include 'tmhOAuth/tmhOAuth.php';
            $tmhOAuth = new tmhOAuth(array(
                'consumer_key' => $consumer_key,
                'consumer_secret' => $consumer_secret,
                'token' => $token,
                'secret' => $secret,
            ));
            $code = $tmhOAuth->request("GET", $tmhOAuth->url("1.1/statuses/user_timeline"), array(
                'screen_name' => $screen_name,
                'count' => $limit,
                'exclude_replies' => true
            ));

            $result = json_decode($tmhOAuth->response['response']);
            //handdling invalid entries
            if ($code != 200) {
                WP_SocialFeeds::flash('error', $result->errors[0]->message);
                return false;
            } else {
                //delete existing data
                $this->reset();
                //creating subfolders
                $pi_dir = UPLOADS_DIR . "/{$this->feed}";
                if (is_dir($pi_dir) === false) {
                    mkdir($pi_dir);
                }
                //delete all exsiting files
                //array_map( "unlink", glob( $pi_dir."/*.jpg" ) );
                $profile_pic_url = $result[0]->user->profile_image_url;
                copy($profile_pic_url, $pi_dir . '/profile_picture.jpg');

                //foreach result data
                foreach ($result as $k => $data) {

                    $post_id = $data->id;
                    //shared photo url
                    $shared_images = $data->entities->media;
                    //saving shared images in facebook folder in uploads
                    $shared_pic = $pi_dir . "/{$post_id}.jpg";
                    $type = 'status';
                    if (!empty($shared_images) && !file_exists($shared_pic)) {
                        copy($shared_images[0]->media_url, $shared_pic);
                        $type = 'image';
                    }
                    $profile = "https://twitter.com/{$data->user->screen_name}";
                    $tweet = array(
                        'social_feed' => $this->feed,
                        'postid' => $post_id,
                        'profile_url' => $profile,
                        'page_name' => $data->user->name,
                        'status' => "{$profile}/status/{$post_id}",
                        'type' => $type,
                        'created_time' => date('Y-m-d H:i:s', strtotime($data->created_at)),
                        'message' => $data->text,
                        'shared_photo_link' => "/{$this->feed}/{$post_id}.jpg",
                        //'shared_link_img' => $data->picture,
                        //'shared_link' => $data->link,
                        //'shared_link_name' => $data->name,
                        //'shared_link_desc' => $data->description,
                        'likes' => $data->favorite_count
                    );
                    //Save record
                    $this->save($tweet);
                }//end foreach
            }
            //apply_filters('socialfeeds_get_feed_' . $this->id, $result);
            WP_SocialFeeds::flash('updated', 'Twitter feed updated with latest results.');
            return true;
        }//end if
    }

}

return new Twitter_Feed();
