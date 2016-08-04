<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Instagram_Feed extends WPSF_Feed {

    public function __construct() {
        $this->feed = 'instagram';

        $in_options = WP_SocialFeeds::getOption($this->feed);
        
        /*
        if (!empty($in_options['page_id'])) {
            $newjson = self::request("https://api.instagram.com/v1/users/search?q={$in_options['page_id']}&access_token=258559306.da06fb6.c222db6f1a794dccb7a674fec3f0941f");
            $result = json_decode($newjson);
            
            var_dump($result);
            
            echo '<br/>';
            
           // $json = self::request("https://api.instagram.com/v1/users/{$in_options['page_id']}/media/recent/?count={$in_options['limit']}&access_token=258559306.da06fb6.c222db6f1a794dccb7a674fec3f0941f");
            //$rslt = json_decode($json);
            
           if ($result->meta->code != 200 || $json === FALSE) {
                WP_SocialFeeds::flash('error', $json === FALSE ? 'Could not connect!' : $result->meta->error_message);
                return false;
            } else {
                //delete existing data
                $this->reset();

                //creating subfolders
                $in_dir = UPLOADS_DIR . "/{$this->feed}";
                if (is_dir($in_dir) === false) {
                    mkdir($in_dir);
                }
                //delete all exsiting files
                //array_map( "unlink", glob( $fb_dir."/*.jpg" ) );
                $profile_pic_url = $result->data[0]->user->profile_picture;
                copy($profile_pic_url, $in_dir . '/profile_picture.jpg');

                //foreach result data
                foreach ($result->data as $data) {
                    $post_id = explode('_', $data->id)[0];
                    //shared photo url
                    $shared_image = explode('?', $data->images->standard_resolution->url);
                    //saving shared images in facebook folder in uploads
                    $shared_pic = $in_dir . "/{$post_id}.jpg";
                    if (!file_exists($shared_pic)) {
                        copy($shared_image[0], $shared_pic);
                    }

                    $in_data = array(
                        'social_feed' => $this->feed,
                        'postid' => $post_id,
                        'profile_url' => "https://www.instagram.com/{$data->user->username}",
                        'page_name' => $data->user->full_name,
                        'status' => isset($data->link) ? $data->link : "",
                        'type' => $data->type,
                        'created_time' => isset($data->caption) ? $data->caption->created_time : date('Y-m-d H:i:s'),
                        'message' => $data->caption->text,
                        'shared_photo_link' => "/{$this->feed}/{$post_id}.jpg",
                        //'shared_link_img' => $data,
                        //'shared_link' => $data,
                        //'shared_link_name' => $data,
                        //'shared_link_desc' => $data,
                        'likes' => isset($data->likes->count) ? $data->likes->count : ""
                    );
                    //Save record
                    $this->save($in_data);
                }//end foreach
            }
            // apply_filters('socialfeeds_get_feed_' . $this->id, $result);
            WP_SocialFeeds::flash('updated', 'Instegram feed updated with latest results.');
            return true;
        }//end if  */
            
       
            
        if (!empty($in_options['page_id']) && !empty($in_options['token'])) {

            //call instagram & get contents using JSON.
            //2690799068.c4be276.f231bc6c64964c26900003bef92db669 - 267791236.df31d88.30e266dda9f84e9f97d9e603f41aaf9e
            $json = self::request("https://api.instagram.com/v1/users/{$in_options['page_id']}/media/recent/?count={$in_options['limit']}&access_token={$in_options['token']}");
            //decode JSON string.
            $result = json_decode($json);
            //handdling invalid entries
            if ($result->meta->code != 200 || $json === FALSE) {
                WP_SocialFeeds::flash('error', $json === FALSE ? 'Could not connect!' : $result->meta->error_message);
                return false;
            } else {
                //delete existing data
                $this->reset();

                //creating subfolders
                $in_dir = UPLOADS_DIR . "/{$this->feed}";
                if (is_dir($in_dir) === false) {
                    mkdir($in_dir);
                }
                //delete all exsiting files
                //array_map( "unlink", glob( $fb_dir."/*.jpg" ) );
                $profile_pic_url = $result->data[0]->user->profile_picture;
                copy($profile_pic_url, $in_dir . '/profile_picture.jpg');

                //foreach result data
                foreach ($result->data as $data) {
                    $post_id = explode('_', $data->id)[0];
                    //shared photo url
                    $shared_image = explode('?', $data->images->standard_resolution->url);
                    //saving shared images in facebook folder in uploads
                    $shared_pic = $in_dir . "/{$post_id}.jpg";
                    if (!file_exists($shared_pic)) {
                        copy($shared_image[0], $shared_pic);
                    }

                    $in_data = array(
                        'social_feed' => $this->feed,
                        'postid' => $post_id,
                        'profile_url' => "https://www.instagram.com/{$data->user->username}",
                        'page_name' => $data->user->full_name,
                        'status' => isset($data->link) ? $data->link : "",
                        'type' => $data->type,
                        'created_time' => isset($data->caption) ? $data->caption->created_time : date('Y-m-d H:i:s'),
                        'message' => $data->caption->text,
                        'shared_photo_link' => "/{$this->feed}/{$post_id}.jpg",
                        //'shared_link_img' => $data,
                        //'shared_link' => $data,
                        //'shared_link_name' => $data,
                        //'shared_link_desc' => $data,
                        'likes' => isset($data->likes->count) ? $data->likes->count : ""
                    );
                    //Save record
                    $this->save($in_data);
                }//end foreach
            }
            // apply_filters('socialfeeds_get_feed_' . $this->id, $result);
            WP_SocialFeeds::flash('updated', 'Instegram feed updated with latest results.');
            return true;
        }//end if  */
    }

}

return new Instagram_Feed();
