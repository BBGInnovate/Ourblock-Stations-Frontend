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

}
