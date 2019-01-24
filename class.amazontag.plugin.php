<?php
/**
 * Amazontag plugin.
 *
 * @copyright 2018 Julien DARRIBAU
 * @license GNU GPLv2
 */

/**
 * Class AmazontagPlugin
 *
 * This plugin add/replace amazon tag in every amazon's user post
 *
 *
 * @see https://github.com/pioc92/amazontag
 */
class AmazontagPlugin extends Gdn_Plugin {


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

                 $checkdomain = 'amazon.fr'; //your amazon associates website

                if (!empty($url) && strpos($url, $checkdomain) !== false){
                    $afftag = 'YOUR AFFILIATE TAG'; //our affiliate ID
                    $affstring = 'tag='; // url parameter for affiliate ID

                    if (parse_url($url, PHP_URL_QUERY)){ //check if link has query string
                        if (strpos($url, $affstring) !== false) { //check if link already has affiliate ID
                        $url = preg_replace("/(".$affstring.").*?(\z|&)/", "$1".$afftag."$2", $url);

                        }else{ //no affiliate tag so we add our
                            $url = $url.'&'.$affstring.$afftag;
                        }
                    }
                    else{ //no query string so we add a query string with our tag at the end of the url

                        $url = $url.'?'.$affstring.$afftag;
                    }
                }
            }
        }
    }
}