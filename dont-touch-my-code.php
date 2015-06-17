<?php
/*
  Plugin Name: Don't touch my [code]
	Description: Adds [code] shortcode that saves your code from the WordPress WYSIWYG editor
	Author: Honza Novak
	Author URI: http://honzanovak.com
	Version: 0.2
*/

add_shortcode('code', function($atts, $content = null) {
	$atts = shortcode_atts(['visual' => false], $atts);
	if ($atts['visual']) {
		$content = html_entity_decode($content);
	}
	return $content;
});

/**
 * Surround [code] with <pre> so the wpautop function doesn't touch it.
 * If you want to use <pre> in the [code], please write the <PRE> tags with uppercase.
 */
function wpautop_with_code($content) {
	$content = preg_replace(
		['{\[code(\s+[^\]])?\]}', '{\[/code\]}'],
		['<pre>$0', '$0</pre>'],
		$content
	);
	$content = wpautop($content);
	return preg_replace(
		['{\<pre\>(\[code(\s+[^\]])?\])}', '{(\[/code\])\</pre\>}'],
		['$1', '$1'],
		$content
	);
}


// add more buttons to the html editor
function dont_touch_add_quicktags() {
    if (wp_script_is('quicktags')){
?>
    <script type="text/javascript">
    QTags.addButton('dont-touch-code', '[code]', '[code]', '[/code]', '[code]', "Don't touch my [code]", 512);
    </script>
<?php
    }
}
add_action('admin_print_footer_scripts', 'dont_touch_add_quicktags');

remove_filter('the_content', 'wpautop');
remove_filter('the_excerpt', 'wpautop');
add_filter('the_content', 'wpautop_with_code');
add_filter('the_excerpt', 'wpautop_with_code');
