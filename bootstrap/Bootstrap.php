<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        // Chargement et activation des plugins
        Zend_Controller_Front::getInstance()->registerPlugin(new Plugin_Security);
        Zend_Controller_Front::getInstance()->registerPlugin(new Plugin_ACL);
        Zend_Controller_Front::getInstance()->registerPlugin(new Plugin_View);
        Zend_Controller_Front::getInstance()->registerPlugin(new Plugin_XmlHttpRequest);

        return parent::run();
    }

    /**
     * Initialisation du cache APC
     */
    protected function _initCache()
    {
        $backend_name = 'Core';
        $backend_options = array(
            'lifetime' => getenv('PREVARISC_CACHE_LIFETIME') ? getenv('PREVARISC_CACHE_LIFETIME') : 3600,
            'cache_id_prefix' => 'prevarisc'
        );

        $frontend_name = 'APC';
        $frontend_options = array();

        return Zend_Cache::factory($backend_name, $frontend_name, $backend_options, $frontend_options);
    }

    /**
     * Initialisation de la lib de gestion des fichiers
     */
    protected function _initFileSystem()
    {
        // Choix de l'adapter à utiliser
        if(getenv('PREVARISC_FILESYSTEM_LOCAL_ENABLED') == 1) {
            $adapter = new League\Flysystem\Adapter\Local(getenv('PREVARISC_FILESYSTEM_LOCAL_PATH'));
        }
        elseif(getenv('PREVARISC_FILESYSTEM_FTP_ENABLED') == 1) {
            $adapter = new League\Flysystem\Adapter\Ftp(array(
                'host' => getenv('PREVARISC_FILESYSTEM_FTP_HOST'),
                'username' => getenv('PREVARISC_FILESYSTEM_FTP_USERNAME'),
                'password' => getenv('PREVARISC_FILESYSTEM_FTP_PASSWORD'),
                'root' => getenv('PREVARISC_FILESYSTEM_FTP_ROOT'),
                'passive' => true,
                'ssl' => getenv('PREVARISC_FILESYSTEM_FTP_SSL'),
                'timeout' => getenv('PREVARISC_FILESYSTEM_FTP_TIMEOUT') ? getenv('PREVARISC_FILESYSTEM_FTP_TIMEOUT') : 30,
            ));
        }
        elseif(getenv('PREVARISC_FILESYSTEM_SFTP_ENABLED') == 1) {
            $adapter = new League\Flysystem\Adapter\Sftp(array(
                'host' => getenv('PREVARISC_FILESYSTEM_SFTP_HOST'),
                'port' => getenv('PREVARISC_FILESYSTEM_SFTP_PORT') ? getenv('PREVARISC_FILESYSTEM_SFTP_PORT') : 22,
                'username' => getenv('PREVARISC_FILESYSTEM_SFTP_USERNAME'),
                'password' => getenv('PREVARISC_FILESYSTEM_SFTP_PASSWORD'),
                'privateKey' => getenv('PREVARISC_FILESYSTEM_SFTP_PRIVATEKEY') ? getenv('PREVARISC_FILESYSTEM_SFTP_PRIVATEKEY') : null,
                'root' => getenv('PREVARISC_FILESYSTEM_SFTP_ROOT') ? getenv('PREVARISC_FILESYSTEM_SFTP_ROOT') : null,
                'timeout' => getenv('PREVARISC_FILESYSTEM_SFTP_TIMEOUT') ? getenv('PREVARISC_FILESYSTEM_SFTP_TIMEOUT') : 10
            ));
        }
        else {
            die("Aucun adaptateur d'accès aux fichiers n'est paramétré.");
        }

      return new League\Flysystem\Filesystem($adapter);
    }

    /**
     * Initialisation de l'auto-loader
     */
    protected function _initAutoLoader()
    {
        $autoloader = Zend_Loader_Autoloader::getInstance();

        $autoloader_application = new Zend_Application_Module_Autoloader(array('basePath' => APPLICATION_PATH, 'namespace'  => null));

        $autoloader->pushAutoloader($autoloader_application);

        return $autoloader;
    }

    /**
     * Initialisation de la vue
     */
    protected function _initView()
    {
        $view = new Zend_View();

        $view->headMeta()
            ->appendHttpEquiv('X-UA-Compatible', 'IE=edge,chrome=1')
            ->appendName('description', 'Logiciel de gestion du service Prévention')
            ->appendName('author', 'SDIS62 - Service Recherche et Développement');

        return $view;
    }

    /**
     * Initialisation du layout
     */
    protected function _initLayout()
    {
        return Zend_Layout::startMvc(array('layoutPath' => APPLICATION_PATH . DS . 'views' . DS . 'layouts'));
    }

    /**
     * Initialisation du router
     */
    protected function _initRouter()
    {
        $router = Zend_Controller_Front::getInstance()->getRouter();

        $router->removeDefaultRoutes();

        require APPLICATION_PATH . DS . 'routes.php';

        $router->addRoutes($routes);

        return $router;
    }

    /**
     * Logger pour tracer l'utilisation du logiciel
     */
    protected function _initLogger()
    {
        $logger = new Monolog\Logger('prevarisc_log');

        if(getenv('PREVARISC_DEBUG_ENABLED')) {
            $logger->pushHandler(new Monolog\Handler\FirePHPHandler());
        }

        return $logger;
    }
}
