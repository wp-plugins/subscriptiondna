<?php

class SubscriptionDNA_menu extends WP_Widget
{

    function SubscriptionDNA_menu()
    {

        $widget_ops = array('classname' => 'SubscriptionDNA_menu', 'description' => 'Displays SubscriptionDNA Members Menu.');
        $control_ops = array('width' => 350, 'height' => 300);
        $this->WP_Widget('SubscriptionDNA_menu', 'SubscriptionDNA - Menu', $widget_ops, $control_ops);
    }

    function widget($args, $instance)
    {
        extract($args);
	if($_SESSION["user_session_id"]=="" && $instance["hide"]=="1")
            return(true);
        $title = apply_filters('widget_title', $instance['title']);
        echo $before_widget;
        if ($title)
        {
            echo $before_title . $title . $after_title;
        }
        //print_r($GLOBALS['SubscriptionDNA']['Settings']);
	?>
        <ul >
        <li><a href="<?php echo(get_permalink($GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]['members'])); ?>"> <?php echo get_the_title($GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]['members']); ?> </a></li>
        <?php
        if($_SESSION['is_groupowner']=="1")
        {
        ?>
        <li><a href="<?php echo(get_permalink($GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]['groups'])); ?>"><?php echo get_the_title($GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]['groups']); ?> </a></li>
        <?php
        }
        ?>
        <li><a href="<?php echo(get_permalink($GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]['my-profile'])); ?>"><?php echo get_the_title($GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]['my-profile']); ?></a></li>
        <li><a href="<?php echo(get_permalink($GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]['change-password'])); ?>"><?php echo get_the_title($GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]['change-password']); ?></a></li>
        <?php
        if($_SESSION['is_groupmember']!="1" || $_SESSION['paid_by_owner']!="1")
        {
        ?>
        <li><a href="<?php echo(get_permalink($GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]['manage-subscriptions'])); ?>"><?php echo get_the_title($GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]['manage-subscriptions']); ?></a></li>
        <li><a href="<?php echo(get_permalink($GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]['payment-methods'])); ?>"><?php echo get_the_title($GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]['payment-methods']); ?></a></li>
        <li><a href="<?php echo(get_permalink($GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]['transactions'])); ?>"><?php echo get_the_title($GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]['transactions']); ?></a></li>
        <?php
        }
        if($_SESSION["user_session_id"]!="")
        {
        ?>
        <li><a href="<?php echo(get_permalink($GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]['login'])); ?>?action=logout">Logout</a></li>
        <?php
        }
        else
        {
        ?>
        <li><a href="<?php echo(get_permalink($GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]['login'])); ?>">Login</a></li>
        <?php
        }
        ?>
        </ul>        
        <?php
        echo $after_widget;
    }

    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['hide'] = $new_instance['hide'];
        return $instance;
    }

    function form($instance)
    {
        //Defaults
        $instance = wp_parse_args((array) $instance, array('title' => '','hide' => ''));

        $title = esc_attr($instance['title']);
        $hide = $instance['hide'];
        ?>
        <p>
          <label for="<?php echo $this->get_field_id('title'); ?>">
            <?php _e( 'Widget Title:' ); ?>
          </label>
          <input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
        <p>
          <label for="<?php echo $this->get_field_id('hide'); ?>">
            <?php _e('Hide When Not Logged In:'); ?>
          </label>
          <input id="<?php echo $this->get_field_id('hide'); ?>" name="<?php echo $this->get_field_name('hide'); ?>" type="checkbox" value="1" <?php if($hide=="1")echo("checked"); ?> />
        </p>
        <?php
    }

}

class SubscriptionDNA_login extends WP_Widget
{

    function SubscriptionDNA_login()
    {

        $widget_ops = array('classname' => 'SubscriptionDNA_login', 'description' => 'Displays SubscriptionDNA Login Form.');
        $control_ops = array('width' => 350, 'height' => 300);
        $this->WP_Widget('SubscriptionDNA_login', 'SubscriptionDNA - Login', $widget_ops, $control_ops);
    }

    function widget($args, $instance)
    {
        extract($args);
	if($_SESSION["user_session_id"]!="")
	return(true);
        $title = apply_filters('widget_title', $instance['title']);
        echo $before_widget;
        if ($title)
        {
            echo $before_title . $title . $after_title;
        }
        $base_path=dirname(__FILE__);
        if(file_exists($base_path."/custom/template/login.php"))
             include($base_path."/custom/template/login.php");
        else
             include($base_path."/template/login.php");
        echo $after_widget;
    }

    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        return $instance;
    }

    function form($instance)
    {
        //Defaults
        $instance = wp_parse_args((array) $instance, array('title' => ''));

        $title = esc_attr($instance['title']);
        ?>
        <p>
          <label for="<?php echo $this->get_field_id('title'); ?>">
            <?php _e( 'Widget Title:' ); ?>
          </label>
          
          <input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
        <?php
    }

}

add_action('widgets_init', create_function('', 'return register_widget("SubscriptionDNA_login");'));
add_action('widgets_init', create_function('', 'return register_widget("SubscriptionDNA_menu");'));
?>
