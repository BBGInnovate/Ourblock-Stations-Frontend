<?php


class DefaultController
{
	private $_loader;
	private $_twig;
    protected $nav = array(
                array(
                    'name'     => 'Admin',
                    'alias'     => '/admin',
                ),
                array(
                    'name'     => 'Register',
                    'alias'     => '/register',
                ),
              );


	public function __construct()
	{
		$this->_loader = new Twig_Loader_Filesystem('/var/www/stations/app/views/');
		$this->_twig = new Twig_Environment($this->_loader);
	}

    public function indexAction()
    {
        //output template
        echo $this->_twig->render('index.html.twig', 
            array(
                'stationname'           => 'Erics Radio Station',
                'stationdescription'    => 'A test station with test streams by Eric',
                'nav'                   => $this->nav,
            ));

    }


    public function adminAction($params, $route)
    {
        //output template
        echo $this->_twig->render('admin.html.twig', 
            array(
                'name' => 'Erich',
            ));
    }

    public function registerAction($params, $route)
    {
    	//output template
		echo $this->_twig->render('admin.html.twig');

    }

    public function twitterAction($params, $route)
    {
        //Get Twitter search and output json
        //"Single User OAuth" (Using the OAuth key generated and assigned by the Twitter App)
        $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, OAUTH_TOKEN, OAUTH_TOKEN_SECRET);
        //get querystring params as ASSOC array
        $url = parse_url($_SERVER['REQUEST_URI']);
        $queryParts = explode('&', $url['query']); 
        $queryParams = array(); 
        foreach ($queryParts as $part) { 
            $item = explode('=', $part); 
            $queryParams[$item[0]] = $item[1]; 
        } 
        $data = json_encode($connection->get("search/tweets", $queryParams));
        header('Content-Type: application/json');
        echo $data;
    }

}
