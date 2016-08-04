<?php

/**
 * Social Feeds Facebook Feed
 *
 * @author      Amal Ranganath
 * @category    Feed
 * @package     WPSocialFeeds/Facebook_Feed
 * @version     1.0.0
 */
class Facebook_Feed extends WPSF_Feed {

    public function __construct() {
        $this->feed = 'facebook';
        $fb_options = WP_SocialFeeds::getOption($this->feed);

        if (!empty($fb_options['page_id']) && !empty($fb_options['token'])) {
            $fb_page_id = $fb_options['page_id'];
            $fields = "id,message,picture,link,name,description,type,icon,created_time,from,object_id";
            //call facebook API & get contents using JSON. //vidulowa - 190798824358059|19bf0f9ffbce731f81db53233f31bbe9
            $fb_api_url = "https://graph.facebook.com/{$fb_page_id}/feed?access_token={$fb_options['token']}&fields={$fields}&limit={$fb_options['limit']}";
            $json = self::request($fb_api_url);
            //decode JSON string.
            $result = json_decode($json);
            //handdling invalid entries
            if (isset($result->error) || $json === FALSE) {
                WP_SocialFeeds::flash('error', $json === FALSE ? 'Could not connect!' : $result->error->message);
                return false;
            } else {
                //delete existing data
                $this->reset();

                //creating subfolders
                $fb_dir = UPLOADS_DIR . "/{$this->feed}";
                if (is_dir($fb_dir) === false) {
                    mkdir($fb_dir);
                }
                //delete all exsiting files
                //array_map( "unlink", glob( $fb_dir."/*.jpg" ) );
                $profile_pic_url = "https://graph.facebook.com/{$fb_page_id}/picture?type=square";
                copy($profile_pic_url, $fb_dir . '/profile_picture.jpg');

                //foreach result data
                foreach ($result->data as $data) {

                    $post_id_arr = explode('_', $data->id);
                    $post_id = $post_id_arr[1];

                    //shared photo url
                    $shared_photo_link = "https://graph.facebook.com/{$data->object_id}/picture";
                    //saving shared images in facebook folder in uploads
                    $shared_pic = $fb_dir . "/{$post_id}.jpg";
                    if (!file_exists($shared_pic)) {
                        copy($shared_photo_link, $shared_pic);
                    }

                    $fb_data = array(
                        'social_feed' => $this->feed,
                        'postid' => $post_id,
                        'profile_url' => "https://fb.com/{$fb_page_id}",
                        'page_name' => $data->from->name,
                        'status' => "https://www.facebook.com/{$fb_page_id}/posts/{$post_id}",
                        'type' => $data->type,
                        'created_time' => $data->created_time,
                        'message' => $data->message,
                        'shared_photo_link' => "/{$this->feed}/{$post_id}.jpg",
                        'shared_link_img' => $data->picture,
                        'shared_link' => $data->link,
                        'shared_link_name' => $data->name,
                        'shared_link_desc' => $data->description,
                        'likes' => count($data->likes->data)
                    );
                    //Save record
                    $this->save($fb_data);
                }//end foreach
            }
            //apply_filters('socialfeeds_get_feed_' . $this->feed, $result);
            WP_SocialFeeds::flash('updated', 'Facebook feed updated with latest results.');
            return true;
        }//end if
    }

}

return new Facebook_Feed();
