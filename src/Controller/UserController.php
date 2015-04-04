<?php

namespace LosUser\Controller;

use LosBase\Controller\AbstractCrudController;
use Zend\Crypt\Password\Bcrypt;
use Zend\View\Model\ViewModel;
use LosUser\Options\ControllerOptionsInterface;
use Zend\Form\Form;
use LosUser\Entity\Access;

class UserController extends AbstractCrudController
{
    protected $entityServiceClass = 'LosUser\Service\User';

    protected $loginForm;

    protected $options;

    public function indexAction()
    {
        if (! $this->losUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('losuser/login');
        }

        return new ViewModel();
    }

    public function loginAction()
    {
        if ($this->losUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute($this->getOptions()
                ->getLoginRedirectRoute());
        }

        $request = $this->getRequest();
        $form = $this->getLoginForm();

        if ($this->getOptions()->getUseRedirect() && $request->getQuery()->get('redirect')) {
            $redirect = $request->getQuery()->get('redirect');
        } else {
            $redirect = false;
        }

        if (! $request->isPost()) {
            return array(
                'loginForm' => $form,
                'redirect' => $redirect,
            );
        }

        $form->setData($request->getPost());

        if (! $form->isValid()) {
            $this->flashMessenger()
                ->setNamespace('losuser-login-form')
                ->addMessage($this->translate('Invalid username or password.'));

            return $this->redirect()->toUrl($this->url()
                ->fromRoute('losuser/login').($redirect ? '?redirect='.rawurlencode($redirect) : ''));
        }

        $this->losUserAuthentication()
            ->getAuthService()
            ->clearIdentity();

        return $this->forward()->dispatch('losuser', array(
            'action' => 'authenticate',
        ));
    }

    public function authenticateAction()
    {
        if ($this->losUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute($this->getOptions()
                ->getLoginRedirectRoute());
        }

        $adapter = $this->losUserAuthentication()->getAuthAdapter();
        $redirect = $this->params()->fromPost('redirect', $this->params()
            ->fromQuery('redirect', false));

        $identity = $this->getRequest()
            ->getPost()
            ->get('identity');
        $password = $this->getRequest()
            ->getPost()
            ->get('credential');
        $adapter->prepare($identity, $password);
        $result = $this->losUserAuthentication()
            ->getAuthService()
            ->authenticate($adapter);

        if (! $result->isValid()) {
            $this->flashMessenger()
                ->setNamespace('losuser-login-form')
                ->addMessage($this->translate('Invalid login/password.'));

            return $this->redirect()->toUrl($this->url()
                ->fromRoute('losuser/login').($redirect ? '?redirect='.rawurlencode($redirect) : ''));
        }

        if ($redirect) {
            return $this->redirect()->toUrl($redirect);
        }

        return $this->redirect()->toUrl($this->url()
            ->fromRoute($this->getOptions()
            ->getLoginRedirectRoute()));
    }

    public function logoutAction()
    {
        $this->losUserAuthentication()
            ->getAuthAdapter()
            ->logout();
        $this->losUserAuthentication()
            ->getAuthService()
            ->clearIdentity();

        return $this->redirect()->toUrl($this->url()
            ->fromRoute($this->getOptions()
            ->getLogoutRedirectRoute()));
    }

    private function translate($msg)
    {
        return $msg;
    }

    public function getOptions()
    {
        if (! $this->options instanceof ControllerOptionsInterface) {
            $this->options = $this->getServiceLocator()->get('losuser_module_options');
        }

        return $this->options;
    }

    public function getLoginForm()
    {
        if (! $this->loginForm) {
            $this->setLoginForm($this->getServiceLocator()
                ->get('losuser_login_form'));
        }

        return $this->loginForm;
    }

    public function setLoginForm(Form $loginForm)
    {
        $this->loginForm = $loginForm;
        $fm = $this->flashMessenger()
            ->setNamespace('losuser-login-form')
            ->getMessages();
        if (isset($fm[0])) {
            $this->loginForm->setMessages(array(
                'identity' => array(
                    $fm[0],
                ),
            ));
        }

        return $this;
    }

    public function lastseenAction()
    {
        if ($this->userUserAuthentication()->hasIdentity()) {
            $user = $this->losUserAuthentication()->getIdentity();

            $ip = \LosBase\Service\Util::getIP();
            $agent = \LosBase\Service\Util::getUserAgent();

            $acesso = new Access();
            $acesso->setUser($user);
            $acesso->setIp($ip);
            $acesso->setAgent($agent);

            $em = $this->getEntityManager();
            $em->persist($acesso);
            $em->flush();
        }

        return $this->redirect()->toRoute('dashboard');
    }

    public function trocasenhaAction()
    {
        $form = new TrocasenhaForm();

        $request = $this->getRequest();

        if ($request->isPost()) {
            $form->setData($this->getRequest()
                ->getPost());

            if ($form->isValid()) {
                $validatedData = $form->getData();
                $atual = $validatedData['atual'];
                $nova = $validatedData['nova'];
                if ($this->zfcUserAuthentication()->hasIdentity()) {
                    $usuario = $this->zfcUserAuthentication()->getIdentity();

                    $options = $this->getServiceLocator()->get('zfcuser_module_options');
                    $bcrypt = new Bcrypt();
                    $bcrypt->setCost($options->getPasswordCost());

                    if ($bcrypt->verify($atual, $usuario->getPassword())) {
                        $nova = $bcrypt->create($nova);
                        $usuario->setPassword($nova);

                        $em = $this->getEntityManager();
                        $em->persist($usuario);
                        $em->flush();

                        $this->flashMessenger()->addSuccessMessage('Nova senha salva');
                    } else {
                        $this->flashMessenger()->addErrorMessage('Senha atual inválida.');
                    }
                } else {
                    $this->flashMessenger()->addErrorMessage('Usuário não encontrado');
                }
            }
        }

        return [
            'form1' => $form,
        ];
    }
}
