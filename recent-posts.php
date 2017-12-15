<?php

require_once(ABSPATH . WPINC . '/widgets/class-wp-widget-recent-posts.php');

class Mlohr_Polylang_Widget_Recent_Posts extends WP_Widget_Recent_Posts {

    public function __construct() {
        WP_Widget::__construct(
            'mlohr_polylang_widget_recent_posts',
            __('Recent Posts - Fixed Target Language', 'mlohr_polylang_widget_domain'),
            array('description' => __( 'Extended Recent Post Widget with Polylang support', 'mlohr_polylang_widget_domain'))
        );
    }

    public function widget($args, $instance) {
        if ( ! isset( $args['widget_id'] ) ) {
            $args['widget_id'] = $this->id;
        }

        $title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Recent Posts' );

        /** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
        $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

        $number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;
        if ( ! $number ) {
            $number = 5;
        }
        $show_date = isset( $instance['show_date'] ) ? $instance['show_date'] : false;

        /**
         * Filters the arguments for the Recent Posts widget.
         *
         * @since 3.4.0
         * @since 4.9.0 Added the `$instance` parameter.
         *
         * @see WP_Query::get_posts()
         *
         * @param array $args     An array of arguments used to retrieve the recent posts.
         * @param array $instance Array of settings for the current widget.
         */
        $r = new WP_Query( apply_filters( 'widget_posts_args', array(
            'posts_per_page'      => $number,
            'no_found_rows'       => true,
            'post_status'         => 'publish',
            'ignore_sticky_posts' => true,
            'lang'                => isset($instance['target_lang']) ? $instance['target_lang'] : ''
        ), $instance ) );

        if ( ! $r->have_posts() ) {
            return;
        }
        ?>
        <?php echo $args['before_widget']; ?>
        <?php
        if ( $title ) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
        ?>
        <ul>
            <?php foreach ( $r->posts as $recent_post ) : ?>
                <?php
                $post_title = get_the_title( $recent_post->ID );
                $title      = ( ! empty( $post_title ) ) ? $post_title : __( '(no title)' );
                ?>
                <li>
                    <a href="<?php the_permalink( $recent_post->ID ); ?>"><?php echo $title ; ?></a>
                    <?php if ( $show_date ) : ?>
                        <span class="post-date"><?php echo get_the_date( '', $recent_post->ID ); ?></span>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
        <?php
        echo $args['after_widget'];
    }

    public function form($instance) {
        $target_lang = isset($instance['target_lang']) ? $instance['target_lang'] : '';
        parent::form($instance);

        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'target_lang' ); ?>"><?php _e( 'Show posts in language:' ); ?></label>
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
