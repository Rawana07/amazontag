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
 * This plugin add/replace amazon tag in every user's post
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
                //Extract the "href" attribute.

                $url =  $link->getAttribute('href');
                $url2 = $link->getAttribute('href');
                $anchor = $link->nodeValue;

                //check the url's domain you can replace by amazon.com or other amazon's country site
                 $checkdomain = 'amazon.fr';


                if (!empty($url) && strpos($url, $checkdomain) !== false){
                    $afftag = 'YOUR AFFILIATE TAG'; //our affiliate ID
                    $affstring = 'tag='; // url parameter for affiliate ID

                    if (parse_url($url, PHP_URL_QUERY)){ //check if link has query string
                        if (strpos($url, $affstring) !== false) { //check if link already has affiliate ID
                        $url = preg_replace("/(".$affstring.").*?(\z|&)/", "$1".$afftag."$2", $url); //Replace affiliate id by yours in the url

                        }else{
                            $url = $url.'&'.$affstring.$afftag; //add affiliate tag to the url
                        }
                    }
                    else{
                        $url = $url.'?'.$affstring.$afftag; // add affiliate tag with ? operator in the url
                    }
                    //$url2 = str_replace('&','&amp;',$url2);
                    //var_dump($args['Mixed']);
                    if($anchor === $url2){ //check if anchor is like url
                    $args['Mixed'] = str_replace('<a href="'.$url2.'" rel="nofollow">','<a href="'.$url.'" target="_blank" rel="nofollow">',$args['Mixed']);

                    }
                    else
                    {
                        $args['Mixed'] = str_replace('<a rel="nofollow" href="'.$url2.'">','<a href="'.$url.'" target="_blank" rel="nofollow">',$args['Mixed']);
                    }


                }



        }

}


        }
    }

