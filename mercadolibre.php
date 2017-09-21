<?php

/**
 * ML connection Class
 */

class Mercadolibre
{

  private $meli; //ML SDK allows manage connection with api
  private $appId;
  private $secretKey;
  private $redirectURI;
  private $_SESSION; // Save session info

  function __construct()
  {
    include('/path/your/domain/meli.php');
    include('/path/your/domain/mercadolibre/configApp.php');
    $this->meli = new Meli($appId, $secretKey); // Start meli connection
    // Save private API's info
    $this->appId = $appId;
    $this->secretKey = $secretKey;
    $this->redirectURI = $redirectURI;
    self::connect();
  }

  public function createItem($items)
  {
    // Receive array of items and prepare JSON for any one
    foreach($items as $item)
      {
        // Construct the item to POST
        $item = array(
              "title"=>$item['name'],
              "category_id"=>"MLA378456",
              "price"=>$item['price'],
              "currency_id"=>"USD",
              "available_quantity"=>1,
              "buying_mode"=>"buy_it_now",
              "listing_type_id"=>"gold_special",
              "condition"=>"new",
              "description"=> $item['shortdesc'],
              "video_id"=> "YOUTUBE_ID_HERE",
              "warranty"=> $item['garant'],
              "pictures" => array(
                  array(
                      "source" => 'http://www.domain.com/'.$item['image']
                ))
          );

        // We call the post request to list a item
        echo '<pre> ------------------- <br>';
        print_r($this->meli->post('/items', $item, array('access_token' => $this->_SESSION['access_token'])));
        echo '</pre> ------------------ <br>';
    	}
  }

  // Public function to get categories
  public function getCategories($parentCat)
  {
    if($parentCat){ return self::getCategoriesByParent($parentCat);  } //If not receive $parentCat continue with common cats else get childrens
    $curl = curl_init('https://api.mercadolibre.com/sites/MLA/categories'); // connect with mercadolibre to obtains categories
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // Setting curl to print info into a variable.
    $categories = curl_exec($curl); // Execute connection
    curl_close(); //Close curl
    $categories = json_decode($categories, true);
    return $categories;
  }

  // Private function to get categories children of a given category
  private function getCategoriesByParent($parentCat)
  {
    $curl = curl_init('https://api.mercadolibre.com/categories/'.$parentCat); // connect with mercadolibre to obtains categories
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // Setting curl to print info into a variable.
    $categories = curl_exec($curl); // Execute connection
    curl_close($curl); //Close curl
    $categories = json_decode($categories, true);
    return $categories;
  }

  // Private function connect with ML for a few minutes
  private function connect()
  {
    if($_GET['code']) {
      // If the code was in get parameter we authorize
      $user = $this->meli->authorize($_GET['code'], $this->redirectURI);
      // Now we create the sessions with the authenticated user
      $this->_SESSION['access_token'] = $user['body']->access_token;
      $this->_SESSION['expires_in'] = $user['body']->expires_in;
      $this->_SESSION['refresh_token'] = $user['body']->refresh_token;

      // We can check if the access token in invalid checking the time
      if($this->_SESSION['expires_in'] + time() + 1 < time()) {
        try {
          print_r($this->meli->refreshAccessToken());
        } catch (Exception $e) {
          echo "Exception: ",  $e->getMessage(), "\n";
        }
      }
    } else {
      //If cant connect, print a button requiring necesarry code
      echo '<a href="' . $this->meli->getAuthUrl($this->redirectURI, Meli::$AUTH_URL['MLA']) . '">Click here to Login using MercadoLibre oAuth 2.0</a>';
    }
  }
} // End Class

?>
