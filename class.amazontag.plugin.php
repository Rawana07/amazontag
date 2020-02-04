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
                $url2 = $link->getAttribute('href');
                $checkdomain = 'amazon.fr'; //your amazon associates website to modify if you're not french!
                if (!empty($url) && strpos($url, 'https://amzn.to') !== false){ //if link is an amazon https shortcut url, we find the final's amazon url (ex https://amzn.to/slfdol -> https://www.amazon.com/product/tag=example21)
                    $url = $this->get_redirect_target($url); 
                }
                if (!empty($url) && strpos($url, 'http://amzn.to') !== false){ //if link is an amazon http shortcut url, we find the final's amazon url (http://amzn.to-> https://amzn.to-> https://www.amazon.com/product/tag=example21)
                    $url = $this->get_redirect_final_target($url); 
                }
                if (!empty($url) && strpos($url, $checkdomain) !== false){
                    $afftag = 'YOUR AFFILIATE TAG'; //your affiliate ID to modify
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
                    if ((strpos($args['Mixed'], '&amp;') !== false)&&(!strpos($url2, '&amp;') !== false)) {
                        $url2 = str_replace('&','&amp;',$url2);
                    }
                    $args['Mixed'] = str_replace('href="'.$url2.'"','href="'.$url.'"',$args['Mixed']);//replace old url by the tagged one in the $args
                    $args['Mixed'] = str_replace('rel="nofollow"','rel="nofollow sponsored"',$args['Mixed']);//replace rel="nofollow" by rel="nofollow sponsored" for google 

                }
            }
        }
    }
    ///
    /// https://gist.github.com/davejamesmiller/dbefa0ff167cc5c08d6d code to get HTTP redirect destination for a URL in PHP 
    ///
    public function get_redirect_target($url) //function to find redirected url
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $headers = curl_exec($ch);
        curl_close($ch);
        // Check if there's a Location: header (redirect)
        if (preg_match('/^Location: (.+)$/im', $headers, $matches))
            return trim($matches[1]);
        // If not, there was no redirect so return the original URL
        // (Alternatively change this to return false)
        return $url;
    }
    public function get_redirect_final_target($url) //function to find final redirected url
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // follow redirects
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1); // set referer on redirect
        curl_exec($ch);
        $target = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);
        if ($target)
            return $target;
        return false;
    }
}