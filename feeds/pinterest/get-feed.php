<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
class Pinterest_Feed extends WPSF_Feed {

    public function __construct() {
        $this->feed = 'pinterest';

        $pi_options = WP_SocialFeeds::getOption($this->feed);

        if (!empty($pi_options['pin_id'])) {
            //call pinterest API & get contents using JSON. 
            $pi_api_url = empty($pi_options['board_name']) ? "https://api.pinterest.com/v3/pidgets/users/{$pi_options['pin_id']}/pins/" : "https://api.pinterest.com/v3/pidgets/boards/{$pi_options['pin_id']}/{$pi_options['board_name']}/pins/";
            $json = self::request($pi_api_url);
            //decode JSON string.
            $result = json_decode($json);
            //handdling invalid entries
            if ($result->code != 0 || $json===false) {
                WP_SocialFeeds::flash('error', $json===false ? 'Could not connect' : $result->message);
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
                $profile_pic_url = $result->data->user->image_small_url;
                copy($profile_pic_url, $pi_dir . '/profile_picture.jpg');

                //foreach result data
                foreach ($result->data->pins as $k => $data) {

                    $post_id = $data->id;

                    //shared photo url
                    $shared_photo_link = (array) $data->images;
                    //saving shared images in facebook folder in uploads
                    $shared_pic = $pi_dir . "/{$post_id}.jpg";
                    if (!file_exists($shared_pic)) {
                        copy($shared_photo_link["237x"]->url, $shared_pic);
                    }

                    $pi_data = array(
                        'social_feed' => $this->feed,
                        'postid' => $post_id,
                        'profile_url' => $data->pinner->profile_url,
                        'page_name' => $data->pinner->full_name,
                        'status' => "https://www.pinterest.com{$data->board->url}",
                        'type' => 'pin',
                        'created_time' => date('Y-m-d H:i:s'),
                        'message' => $data->board->description,
                        'shared_photo_link' => "/{$this->feed}/{$post_id}.jpg",
                        //'shared_link_img' => $data->picture,
                        //'shared_link' => $data->link,
                        //'shared_link_name' => $data->name,
                        //'shared_link_desc' => $data->description,
                        'likes' => $data->like_count
                    );
                    //Save record
                    $this->save($pi_data);
                    if ($k > $pi_options['limit'])
                        break;
                }//end foreach
            }
            //apply_filters('socialfeeds_get_feed_' . $this->id, $result);
            WP_SocialFeeds::flash('updated', 'Pinterest feed updated with latest results.');
            return true;
        }//end if
    }

}

return new Pinterest_Feed();
