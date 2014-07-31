<?php

class AuthController extends Zend_Controller_Action
{
    public function loginAction()
    {
        $this->_helper->layout->setLayout('login');

        $service_user = new Service_User();

        if ($this->_request->isPost()) {

            try {

                // Identifiants
                $username = $this->_request->prevarisc_login_username;
                $password = $this->_request->prevarisc_login_passwd;

                // Récupération de l'utilisateur
                $user = $service_user->findByUsername($username);

                // Si l'utilisateur n'est pas actif, on renvoie false
                if ($user === null || ($user !== null && !$user['ACTIF_UTILISATEUR'])) {
                    throw new Exception('L\'utilisateur n\'existe pas ou n\'est pas actif.');
                }

                // Adaptateur principal (dbtable)
                $adapters['dbtable'] = new Zend_Auth_Adapter_DbTable(null, 'utilisateur', 'USERNAME_UTILISATEUR', 'PASSWD_UTILISATEUR');
                $adapters['dbtable']->setIdentity($username)->setCredential(md5($username . getenv('PREVARISC_SECURITY_SALT') . $password));

                // Adaptateur LDAP
                if (getenv('PREVARISC_LDAP_ENABLED') == 1) {
                    $ldap = new Zend_Ldap(array('host' => getenv('PREVARISC_LDAP_HOST'), 'username' => getenv('PREVARISC_LDAP_USERNAME'), 'password' => getenv('PREVARISC_LDAP_PASSWORD'), 'baseDn' => getenv('PREVARISC_LDAP_BASEDN')));
                    try {
                        $adapters['ldap'] = new Zend_Auth_Adapter_Ldap();
                        $adapters['ldap']->setLdap($ldap);
                        $adapters['ldap']->setUsername($ldap->getCanonicalAccountName($username, Zend_Ldap::ACCTNAME_FORM_DN));
                        $adapters['ldap']->setPassword($password);
                    } catch (Exception $e) {}
                }

                // On lance le process d'identification
                foreach ($adapters as $key => $adapter) {
                    if ($adapter->authenticate()->isValid()) {
                        $storage = Zend_Auth::getInstance()->getStorage()->write($user);
                        $this->_helper->redirector->gotoRoute(array(), 'home', true);
                    }
                }

                throw new Exception('Les identifiants ne correspondent pas.');

            } catch (Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'danger', 'title' => 'Erreur d\'authentification', 'message' => $e->getMessage()));
            }
        }
    }

    public function logoutAction()
    {
        $this->_helper->viewRenderer->setNoRender();

        $service_user = new Service_User();
        $service_user->updateLastActionDate(Zend_Auth::getInstance()->getIdentity()['ID_UTILISATEUR'], null);

        Zend_Auth::getInstance()->clearIdentity();

        $this->_helper->redirector->gotoRoute(array(), 'login', true);
    }
}
