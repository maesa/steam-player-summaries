<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class SteamConverter extends CI_Controller {

	const API_KEY = "SOME_KEY_HERE";

	public function index()	{
		$this->load->view('header');

		$this->load->view('steam_converter');

		$this->load->view('footer');
	}

	public function getPlayerSummary() {

		if($this->input->post('ajax') == '1') {
			$this->form_validation->set_rules('txtSteamUrl', 'Steam URL', 'required|callback_url_validation');
			$this->form_validation->set_message('url_validation', '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' . 'invalid Steam URL');
			$this->form_validation->set_error_delimiters('<div class="alert alert-danger alert-dismissable">', '</div>');

			if($this->form_validation->run() == FALSE) {
				echo validation_errors();
			} else {
				$steamids = $this->getSteamID64($this->input->post('txtSteamUrl'));

				if($steamids) {
					$url ="http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=" . $this::API_KEY . "&steamids=" . $steamids;
					$json_source = $this->getWebPage($url);

					//dirty trick to check if $json_source['content'] contain our need, by counting its length
					if ((200 === $json_source['http_code']) && (100 < strlen($json_source['content']))) {
						$decoded_json = json_decode($json_source['content']);

				        $output = '<img src="' . $decoded_json->response->players[0]->avatarfull . '" class="img-thumbnail"><br/>';
				        $username = htmlentities($decoded_json->response->players[0]->personaname, ENT_QUOTES);
				        $output .= '<blockquote><p>';
				        if (1 == $decoded_json->response->players[0]->communityvisibilitystate) {//1-private 3-public
							$output .= "Unable to fetch player online status because the profile of " .$username. " is private.<br/>";			        
				        } else {
				        	
				        	switch ($decoded_json->response->players[0]->personastate) {
				        		case 0: //0 - Offline
					                $output .= $username . " is offline.<br/>";
					                break;
				                case 1: //1 - Online
					                $output .= $username . " is online.<br/>";
					                break;
				                case 2: //2 - Busy
					                $output .= $username . " is offline.<br/>";
					                break;
				                case 3: //3 - Away
					                $output .= $username . " is away.<br/>";
					                break;
				                case 4: //4 - Snooze
					                $output .= $username . " is snooze.<br/>";
					                break;
				                case 5: //5 - Looking to trade
					                $output .= $username . " is looking to trade.<br/>";
					                break;
				                case 6: //6 - Looking to play
					                $output .= $username . " is looking to play.<br/>";
					                break;
				                default: 
					                $output .= $username . " is unknown.<br/>";
					                break;
				            } //switch
				        }

				        $output .= '</p></blockquote>';
				        $steam_url = $decoded_json->response->players[0]->profileurl;
				        $output .= '<div class="alert alert-info"><a href="' . $steam_url .'" target="_blank">' . $steam_url . '</a></div>';

				        echo $output;
			    	} else {
			    		echo '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Unable to reach Steam server or invalid Steam URL</div>';
			    	}
			    } else { //$steamids
			    	echo '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Unable to reach Steam server or invalid Steam URL</div>';
			    } //$steamids
			}
		}

	}

	private function getSteamID64($url) {

		$id_pattern = "/(https?:\/\/)?(steamcommunity\.com\/id\/)([a-zA-Z0-9._-]+)\/?/i";
		if (preg_match($id_pattern, $url, $id_matches)) {
			preg_match("/([a-zA-Z0-9._-]+)\/?/i", $id_matches[0], $matches);

			//dirty trick
			$yang_mau_diganti = array("http","https",":","//","steamcommunity.com","/","id");
			$diganti_dengan = array("","","","","","","");
			$steam_id = str_replace($yang_mau_diganti, $diganti_dengan, $id_matches[0]);

			//get steam id
			$vanity_url = 'http://api.steampowered.com/ISteamUser/ResolveVanityURL/v0001/?key=' . $this::API_KEY . '&vanityurl=' .  $steam_id;
			$json_source = $this->getWebPage($vanity_url);
			$decoded_json = json_decode($json_source['content']);

			if ((200 === $json_source['http_code']) && (1 === $decoded_json->response->success)) {
				return $decoded_json->response->steamid;
			} else {
				return FALSE;
			}
		} else {
			preg_match("/(\d+)/", $url, $profile_matches);
			return $profile_matches[1];
		}
	}

    private function getWebPage($url) {
    	$this->load->library('curl'); 
    	// Start session (also wipes existing/previous sessions)
		$this->curl->create('$url');

		$user_agent='Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)';

        $options = array(
        	CURLOPT_URL	=> $url,
            CURLOPT_CUSTOMREQUEST  =>"GET",        //set request type post or get
            CURLOPT_POST           =>false,        //set to GET
            CURLOPT_USERAGENT      => $user_agent, //set user agent
            //CURLOPT_COOKIEFILE     =>"cookie.txt", //set cookie file
            //CURLOPT_COOKIEJAR      =>"cookie.txt", //set cookie jar
            CURLOPT_RETURNTRANSFER => true,     // return web page
            CURLOPT_HEADER         => false,    // don't return headers
            //CURLOPT_FOLLOWLOCATION => true,     // follow redirects
            CURLOPT_ENCODING       => "",       // handle all encodings
            //CURLOPT_AUTOREFERER    => true,     // set referer on redirect
            CURLOPT_AUTOREFERER    => "http://www.google.com/bot.html",
            CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
            CURLOPT_TIMEOUT        => 120,      // timeout on response
            CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
        );

		$this->curl->options($options);
		// Execute - returns responce
		$content = $this->curl->execute();
		$err = $this->curl->error_code; // int
		$errmsg = $this->curl->error_string;
		$header = $this->curl->info;

		$header['errno']   = $err;
        $header['errmsg']  = $errmsg;
        $header['content'] = $content;
        return $header;
    }

    public function url_validation($input) {
		$url_pattern = "/(https?:\/\/)?(steamcommunity\.com\/((id\/[a-zA-Z0-9._-]+)|(profiles\/\d+)))\/?/";
		if (!preg_match($url_pattern, $input, $matches)) {
			return FALSE;
		}
		return TRUE;
	}
}