<?php

require_once(ABSPATH . WPINC . '/widgets/class-wp-widget-tag-cloud.php');

class Mlohr_Polylang_Widget_Tag_Cloud extends WP_Widget_Tag_Cloud {

    public function __construct() {
        WP_Widget::__construct(
            'mlohr_polylang_widget_tag_cloud',
            __('Tag Cloud - Fixed Target Language', 'mlohr_polylang_widget_domain'),
            array('description' => __( 'Extended Tag Cloud Widget with Polylang support', 'mlohr_polylang_widget_domain'))
        );
    }

    public function widget($args, $instance) {
        $current_taxonomy = $this->_get_current_taxonomy( $instance );

        if ( ! empty( $instance['title'] ) ) {
            $title = $instance['title'];
        } else {
            if ( 'post_tag' === $current_taxonomy ) {
                $title = __( 'Tags' );
            } else {
                $tax = get_taxonomy( $current_taxonomy );
                $title = $tax->labels->name;
            }
        }

        $show_count = ! empty( $instance['count'] );

        /**
         * Filters the taxonomy used in the Tag Cloud widget.
         *
         * @since 2.8.0
         * @since 3.0.0 Added taxonomy drop-down.
         * @since 4.9.0 Added the `$instance` parameter.
         *
         * @see wp_tag_cloud()
         *
         * @param array $args     Args used for the tag cloud widget.
         * @param array $instance Array of settings for the current widget.
         */
        $tag_cloud = wp_tag_cloud( apply_filters( 'widget_tag_cloud_args', array(
            'taxonomy'   => $current_taxonomy,
            'echo'       => false,
            'show_count' => $show_count,
            'lang'       => isset($instance['target_lang']) ? $instance['target_lang'] : ''
        ), $instance ) );

        if ( empty( $tag_cloud ) ) {
            return;
        }

        /** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
        $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

        echo $args['before_widget'];
        if ( $title ) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        echo '<div class="tagcloud">';

        echo $tag_cloud;

        echo "</div>\n";
        echo $args['after_widget'];
    }

    public function form($instance) {
        $target_lang = isset($instance['target_lang']) ? $instance['target_lang'] : '';
        parent::form($instance);

        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'target_lang' ); ?>"><?php _e( 'Show Tags in language:' ); ?></label>
            <select id="<?php echo $this->get_field_id( 'target_lang' ); ?>" name="<?php echo $this->get_field_name( 'target_lang' ); ?>">
                <option value="" <? if ($target_lang == "") echo ("selected=\"selected\""); ?>>All languages</option>
                <?php foreach (pll_languages_list() as $lang) { ?>
                    <option value="<?php echo($lang); ?>" <? if ($target_lang == $lang) echo ("selected=\"selected\""); ?>><?php echo($lang); ?></option>
                <?php } ?>
            </select>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = parent::update($new_instance, $old_instance);
        $instance['target_lang'] = isset($new_instance['target_lang']) ? $new_instance['target_lang'] : '';
        return $instance;
    }
}
