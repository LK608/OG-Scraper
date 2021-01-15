<?php
/*
Plugin Name: Open Graph Meta Scraper
Plugin URI: https://gist.github.com/baconbrad/6f6ea5bafbae869d3a2ce586dd2e482a
Description: Get Open Graph information for your shortened links
Version: 1.1
Author: Luke Steinfurth
Author URI: https://www.steinfurth.co/
*/

define( 'DELAY', '0' ); // delay to redirect
yourls_add_action( 'pre_redirect', 'og_scraper' );
    


function og_scraper( $args ) {

        $url =  $args[0];
        $parsed_url = parse_url($url);
        
        require 'user/plugins/vendor/autoload.php';

        
            
        $web = new \spekulatius\phpscraper();

        $web->go($url);

        $data = $web->openGraph;
        $description = $web->openGraph['og:description'];
        if(empty($description)) 
        {
            $meta = get_meta_tags($url);
            $description = $meta['description'];
        }

        $description = $web->openGraph['og:title'];
        if(empty($title)) 
        {
            $page = file_get_contents($url);
            $title = preg_match('/<title[^>]*>(.*?)<\/title>/ims', $page, $match) ? $match[1] : null;    
        }

        $image = $web->openGraph['og:image'];
        if(substr($image, 0, 4) != "http" || substr($web->openGraph['og:image'], 0, 5) != "https") $image = $parsed_url['scheme'] . "://" . $parsed_url['host'] . $web->openGraph['og:image'];
	?>
	<html>
		<head>
			<?php
				echo '<meta property="og:title" content="' . $title . '" />
				<meta property="og:type" content="' . $web->openGraph['og:type'] . '" />
				<meta property="og:url" content="' . $web->openGraph['og:location'] . '" />
				<meta property="og:image" content="' .  $image . '" />
				<meta property="og:site_name" content="' . $web->openGraph['og:site_name'] . '" />
				<meta property="og:description" content="' . $description . '" />';
			?>
			<meta http-equiv="refresh" content="<?php echo DELAY; ?>; url=<?php echo $url; ?>">
		</head>
	</html>
	<?php
	die();
	
}
