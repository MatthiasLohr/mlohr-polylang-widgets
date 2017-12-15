<?php

require_once(ABSPATH . WPINC . '/widgets/class-wp-widget-categories.php');

class Mlohr_Polylang_Widget_Categories extends WP_Widget_Categories
{
    public function __construct()
    {
        WP_Widget::__construct(
            'mlohr_polylang_widget_categories',
            __('Categories - Fixed Target Language', 'mlohr_polylang_widget_domain'),
            array('description' => __('Extended Categories Widget with Polylang support', 'mlohr_polylang_widget_domain'))
        );
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

    public function widget( $args, $instance ) {
        static $first_dropdown = true;

        $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Categories' );

        /** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
        $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

        $c = ! empty( $instance['count'] ) ? '1' : '0';
        $h = ! empty( $instance['hierarchical'] ) ? '1' : '0';
        $d = ! empty( $instance['dropdown'] ) ? '1' : '0';

        echo $args['before_widget'];

        if ( $title ) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        $cat_args = array(
            'orderby'      => 'name',
            'show_count'   => $c,
            'hierarchical' => $h,
            'lang'         => isset($instance['target_lang']) ? $instance['target_lang'] : ''
        );

        if ( $d ) {
            echo sprintf( '<form action="%s" method="get">', esc_url( home_url() ) );
            $dropdown_id = ( $first_dropdown ) ? 'cat' : "{$this->id_base}-dropdown-{$this->number}";
            $first_dropdown = false;

            echo '<label class="screen-reader-text" for="' . esc_attr( $dropdown_id ) . '">' . $title . '</label>';

            $cat_args['show_option_none'] = __( 'Select Category' );
            $cat_args['id'] = $dropdown_id;

            /**
             * Filters the arguments for the Categories widget drop-down.
             *
             * @since 2.8.0
             * @since 4.9.0 Added the `$instance` parameter.
             *
             * @see wp_dropdown_categories()
             *
             * @param array $cat_args An array of Categories widget drop-down arguments.
             * @param array $instance Array of settings for the current widget.
             */
            wp_dropdown_categories( apply_filters( 'widget_categories_dropdown_args', $cat_args, $instance ) );

            echo '</form>';
            ?>

            <script type='text/javascript'>
                /* <![CDATA[ */
                (function() {
                    var dropdown = document.getElementById( "<?php echo esc_js( $dropdown_id ); ?>" );
                    function onCatChange() {
                        if ( dropdown.options[ dropdown.selectedIndex ].value > 0 ) {
                            dropdown.parentNode.submit();
                        }
                    }
                    dropdown.onchange = onCatChange;
                })();
                /* ]]> */
            </script>

            <?php
        } else {
            ?>
            <ul>
                <?php
                $cat_args['title_li'] = '';

                /**
                 * Filters the arguments for the Categories widget.
                 *
                 * @since 2.8.0
                 * @since 4.9.0 Added the `$instance` parameter.
                 *
                 * @param array $cat_args An array of Categories widget options.
                 * @param array $instance Array of settings for the current widget.
                 */
                wp_list_categories( apply_filters( 'widget_categories_args', $cat_args, $instance ) );
                ?>
            </ul>
            <?php
        }

        echo $args['after_widget'];
    }

    public function update($new_instance, $old_instance)
    {
        $instance = parent::update($new_instance, $old_instance);
        $instance['target_lang'] = isset($new_instance['target_lang']) ? $new_instance['target_lang'] : '';
        return $instance;
    }
}
