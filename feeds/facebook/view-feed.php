<?php
/**
 * Feed View: facebook
 */
$profile_image = UPLOADS_URL . "/$feed/profile_picture.jpg";
?>
    <div style="margin: <?= $margin ?>; padding: <?= $padding ?>; border: <?= $border_color ?> solid <?= $border_size ?>; border-radius: <?= $border_radius ?>;">
        <<?= $title_tag ?>><?= ucfirst($feed) ?> Feed</<?= $title_tag ?>>
        <div class="profile"><a href=" <?= $feed_data[0]->profile_url ?> " target="_blank"><img src="<?= $profile_image ?>" alt="" /></a></div>
        <?php
        foreach ($feed_data as $data) :
            ?>

            <hr />
            <div class="">
                <div class="profile-info">
                    <!--  
                    <div class="profile-photo"><img src="<?php $profile_image ?>" alt="" /> </div> -->
                    <div class="profile-name">
                        <div>
                            <a href=" <?= $data->profile_url ?> " target="_blank"><?= $data->page_name ?></a> 
                            shared a 
                            <a href="<?= $data->status ?> " target="_blank"><?= $data->type ?></a>
                        </div>
                        <div class="time-ago"><?= date_i18n('F j, Y  H:i:s', strtotime($data->created_time)) ?></div>
                    </div>
                </div>
                <div class="profile-message"><?= $data->message ?></div>
            </div>
            <div class="">
                <a href="<?= $data->status ?>" target="_blank" class="post-link">

                    <div class="post-content">
                        <?php if ($data->type == "status") { ?>
                            <div class='post-status'>
                                View on Facebook
                            </div>
                            <?php
                        } else if ($data->type == "photo") {
                            if ($image_size === '1') {
                                $img = new SimpleImage(UPLOADS_URL . $data->shared_photo_link);
                                ?>
                                <img src="<?= $img->thumbnail($image_width, $image_height)->output_base64() ?>" alt="" />
                                <?php
                            } else {
                                ?>
                                <img src="<?= UPLOADS_URL . $data->shared_photo_link ?>" alt="" />
                                <?php
                            }
                        } else {
                            if ($data->shared_link_img):
                                ?>
                                <div class="post-picture">
                                    <img src="<?= $data->shared_link_img ?>" alt="" />
                                </div>
                            <?php endif; ?>
                            <div class="post-info">
                                <div class="post-info-name"><?= wp_trim_words($data->shared_link_name, 10) ?></div>
                                <div class="post-info-description"><?= wp_trim_words($data->shared_link_desc, 13) ?></div>
                            </div>
                        <?php } ?>
                    </div>
                </a>
            </div>
            <?php
        endforeach;
        ?>
    </div>

