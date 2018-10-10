<?php
/**
 * An example plugin.
 *
 * @copyright 2008-2014 Vanilla Forums, Inc.
 * @license GNU GPLv2
 */

/**
 * Class ExamplePlugin
 *
 * This plugin excerpt every discussion/announcement body
 * before adding it under their title in the discussions list.
 *
 * @see http://docs.vanillaforums.com/developers/plugins
 * @see http://docs.vanillaforums.com/developers/plugins/quickstart
 */
class AmazontagPlugin extends Gdn_Plugin {

    /**
     * Plugin constructor
     *
     * This fires once per page load, during execution of bootstrap.php. It is a decent place to perform
     * one-time-per-page setup of the plugin object. Be careful not to put anything too strenuous in here
     * as it runs every page load and could slow down your forum.
     */
    public function __construct() {

    }


    public function format_links_handler($sender, $args) {

        if(isset($args['Mixed'])){

            $dom = new DOMDocument;
            @$dom->loadHTML($args['Mixed']);
            $links = $dom->getElementsByTagName('a');

            foreach ($links as $link){
                //Extract and show the "href" attribute.

                $url =  $link->getAttribute('href');
                $url2 = $link->getAttribute('href');

                 $checkdomain = 'amazon.fr';


                if (!empty($url) && strpos($url, $checkdomain) !== false){
                    $afftag = 'YOUR AFFILIATE TAG'; //our affiliate ID
                    $affstring = 'tag='; // url parameter for affiliate ID

                    if (parse_url($url, PHP_URL_QUERY)){ //check if link has query string
                        if (strpos($url, $affstring) !== false) { //check if link already has affiliate ID
                        $url = preg_replace("/(".$affstring.").*?(\z|&)/", "$1".$afftag."$2", $url);

                        }else{
                            $url = $url.'&'.$affstring.$afftag;
                        }
                    }
                    else{
                        $url = $url.'?'.$affstring.$afftag;
                    }
                    $url2 = str_replace('&','&amp;',$url2);
                    //var_dump($args['Mixed']);
                    $args['Mixed'] = str_replace('<a href="'.$url2.'" rel="nofollow">','<a href="'.$url.'" target="_blank">',$args['Mixed']);


                }



        }

}


        }
    }

