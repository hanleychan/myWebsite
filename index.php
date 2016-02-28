<?php

define("GMAIL_USERNAME", "username");
define("GMAIL_PASSWORD", "password");

require 'vendor/autoload.php';

session_start();

// Create app
$app = new \Slim\App();

// Get container
$container = $app->getContainer();

// Register component on container
$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig('templates', [
        'cache' => false 
    ]);
    $view->addExtension(new \Slim\Views\TwigExtension(
        $container['router'],
        $container['request']->getUri()
    ));

    return $view;
};

$container['flash'] = function() {
    return new \Slim\Flash\Messages();
};

$app->get('/', function($request , $response, $args) {
    $page = "portfolio";

    return $this->view->render($response, 'index.twig', compact("page"));
})->setName('portfolio');

$app->get('/about', function($request, $response, $args) {
    $page = "about";

    return $this->view->render($response, 'about.twig', compact("page"));
})->setName('about');

$app->get('/contact', function($request, $response, $args) {
    $messages = $this->flash->getMessages();
    $page = "contact";

    return $this->view->render($response, 'contact.twig', compact("messages", "page"));
})->setName('contact');

$app->post('/contact', function($request, $response, $args) {
    $name = $request->getParam('name');
    $email = $request->getParam('email');
    $subject = $request->getParam('subject'); 
    $msg = $request->getParam('message');
    $testName = $request->getParam('test_name');

    // if user filled in the hidden honeypot field
    if(!empty($testName)) {
        $this->flash->addMessage('fail', 'Error with form input');
        $messages = $this->flash->getMessages();
        return $this->view->render($response, 'contact.twig', compact("name", "email", "subject", "msg", "messages"));
    }

    // validate form data
    if(!empty($name) && !empty($email) && !empty($subject) && !empty($msg)) {
        $cleanName = filter_var($name, FILTER_SANITIZE_STRING);
        $cleanSubject = filter_var($subject, FILTER_SANITIZE_STRING);
        $cleanEmail = filter_var($email, FILTER_SANITIZE_EMAIL);
        $cleanMsg = filter_var($msg, FILTER_SANITIZE_STRING);
    }
    else {
        $this->flash->addMessage('fail', 'Error: Missing form data'); 
        
        return $this->view->render($response, 'contact.twig', array_merge(
            compact("name", "email", "subject", "msg"),
            ["messages" => $this->flash->getMessages(), "page"=>"contact"]
        ));
    }

    // Build the email message and send it using Swiftmailer
    $cleanMsg = "FROM: $cleanName <$cleanEmail>\n\n\n$cleanMsg";

    $message = Swift_Message::newInstance()
        ->setSubject("Email from hanleyc.com: " . $cleanSubject)
        ->setFrom(array($cleanEmail => $cleanName))
        ->setTo(array('hanleychan@gmail.com'=>'Hanley Chan'))
        ->setBody($cleanMsg)
    ;

    $transport = Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, 'ssl')
        ->setUsername(GMAIL_USERNAME)
        ->setPassword(GMAIL_PASSWORD)
    ;

    $mailer = Swift_Mailer::newInstance($transport);

    if($result = $mailer->send($message)) {
        $this->flash->addMessage('success', 'Message has been sent successfully');
    }
    else {
        $this->flash->addMessage('fail', 'Error: There was a problem sending your message');
    }

    $router = $this->router;
    return $response->withRedirect($router->pathFor('contact'));

})->setName('processContact');

$app->run();

