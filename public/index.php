<?php

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use TVListings\Domain\Service\DoctrineEntityManager;
use TVListings\Domain\Repository\ChannelRepository;
use TVListings\Domain\Repository\ListingRepository;
use TVListings\Domain\Entity\Channel;
use TVListings\Domain\Entity\Listing;

if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $file = __DIR__ . $_SERVER['REQUEST_URI'];
    if (is_file($file)) {
        return false;
    }
}

require __DIR__ . '/../vendor/autoload.php';

session_start();

// Instantiate the app
$configuration = array(
    'settings' => array(
        'displayErrorDetails' => true,
        'doctrine' => array(
            'mapping_path' => __DIR__ . '/../src/Domain/Entity',
            'driver' => 'pdo_mysql',
            'dbname' => 'mn_tv_listings',
            'user' => 'root',
            'password' => '$ecret',
            'host' => 'localhost',
        ),
        'view' => array(
            'template_path' => __DIR__ . '/../app/templates',
            'twig' => array(
                'cache' => __DIR__ . '/../app/cache/twig',
                'debug' => true,
                'auto_reload' => true,
            ),
        ),
    )
);

$app = new \Slim\App($configuration);

$container = $app->getContainer();
$container['hello_service'] = function ($container) {
    return new TVListings\Domain\Service\HelloService();
};

// Register component on container
$container['view'] = function ($c) {
    $settings = $c->get('settings');
    $view = new \Slim\Views\Twig($settings['view']['template_path'], $settings['view']['twig']);
    $view->addExtension(new \Slim\Views\TwigExtension(
        $c->get('router'),
        $c->get('request')->getUri()
    ));
    $view->addExtension(new Twig_Extension_Debug());

    return $view;
};

$container['doctrine.orm.entity_manager'] = function ($container) {
    $settings = $container->get('settings');
    $isDevMode = true;
    $config = Setup::createAnnotationMetadataConfiguration(
        array(
            $settings['doctrine']['mapping_path'],
            $isDevMode,
        )
    );

    $config->setCustomDatetimeFunctions(array(
        'DATE'  => 'DoctrineExtensions\Query\Mysql\Date',
    ));

    $conn = array(
        'driver' => $settings['doctrine']['driver'],
        'host' => $settings['doctrine']['host'],
        'dbname' => $settings['doctrine']['dbname'],
        'user' => $settings['doctrine']['user'],
        'password' => $settings['doctrine']['password'],
    );

    return EntityManager::create($conn, $config);
};

$container['tvlistings.channel.repository'] = function ($container) {
    $em = new DoctrineEntityManager($container->get('doctrine.orm.entity_manager'));

    return new ChannelRepository($em);
};
$container['tvlistings.listing.repository'] = function ($container) {
    $em = new DoctrineEntityManager($container->get('doctrine.orm.entity_manager'));

    return new ListingRepository($em);
};

$app->get('/', function ($request, $response, $args) {
    $container = $this->getContainer();

    $channel = $container->get('tvlistings.channel.repository')->findOneBySlug('mnb');
    $listings = $container->get('tvlistings.channel.repository')->getTodayListings($channel);

    $this->view->render(
        $response,
        'base.html.twig',
        array(
            'channel' => $channel,
            'listings' => $listings,
        )
    );

    return $response->write($body);
})->setName('homepage');

$app->get('/{slug}', function ($request, $response, $args) {
    $container = $this->getContainer();

    $channel = $container->get('tvlistings.channel.repository')->findOneBySlug($args['slug']);
    if (null === $channel) {
        $uri = $this->router->pathFor(
            'homepage',
            array()
        );

        return $response->withRedirect((string)$uri, 301);
    }

    $listings = $container->get('tvlistings.channel.repository')->getTodayListings($channel);

    $this->view->render(
        $response,
        'base.html.twig',
        array(
            'channel' => $channel,
            'listings' => $listings,
        )
    );

    return $response->write($body);
});

$app->get('/admin/', function ($request, $response, $args) {
    $container = $this->getContainer();

    $channels = $container->get('tvlistings.channel.repository')->findAll();

    $this->view->render(
        $response,
        'admin/list.html.twig',
        array(
            'channels' => $channels,
        )
    );

    return $response->write($body);
})->setName('admin_homepage');

$app->map(['GET', 'POST'], '/admin/channels/new', function ($request, $response, $args) {

    if ($request->isPost()) {
        $container = $this->getContainer();
        $parsedBody = $request->getParsedBody();
        $channelRepository = $container->get('tvlistings.channel.repository');
        $channel = new Channel($parsedBody['name'], $parsedBody['logoPath']);
        $channelRepository->persist($channel);

        $uri = $this->router->pathFor(
            'admin_homepage',
            array()
        );

        return $response->withRedirect((string)$uri, 301);
    }

    $this->view->render(
        $response,
        'admin/new.html.twig',
        array()
    );

    return $response->write($body);
})->setName('admin_channel_new');

$app->post('/admin/channels/{slug}', function ($request, $response, $args) {
    $container = $this->getContainer();
    $channel = $container->get('tvlistings.channel.repository')->findOneBySlug($args['slug']);
    if (null === $channel) {
        $uri = $this->router->pathFor(
            'homepage',
            array()
        );

        return $response->withRedirect((string)$uri, 301);
    }

    $container->get('tvlistings.channel.repository')->delete($channel);

    $uri = $this->router->pathFor(
        'admin_homepage',
        array()
    );

    return $response->withRedirect((string)$uri, 301);
})->setName('admin_channel_delete');

$app->group('/admin/channels/{slug}', function () {
    $this->get('', function ($request, $response, $args) {
        $container = $this->getContainer();
        $parsedBody = $request->getParsedBody();
        $channel = $container->get('tvlistings.channel.repository')->findOneBySlug($args['slug']);

        $listings = $this->getContainer()->get('tvlistings.listing.repository')
            ->findBy($channel);

        $this->view->render(
            $response,
            'admin/show.html.twig',
            array(
                'channel' => $channel,
                'listings' => $listings,
            )
        );

        return $response->write($body);
    })->setName('admin_channel_show');

    $this->map(['GET', 'POST'], '/listings/new', function ($request, $response, $args) {

        $channel = $this->getContainer()->get('tvlistings.channel.repository')->findOneBySlug($args['slug']);
        if ($request->isPost()) {
            $parsedBody = $request->getParsedBody();
            $listingRepository = $this->getContainer()->get('tvlistings.listing.repository');
            $listing = new Listing($channel, $parsedBody['title'], new \DateTime($parsedBody['programDate']), $parsedBody['resourceLink']);
            $listing->programAt($parsedBody['programAt']);
            $listing->setDescription($parsedBody['description']);
            $listingRepository->persist($listing);

            $uri = $this->router->pathFor(
                'admin_channel_show',
                array(
                    'slug' => $channel->getSlug(),
                )
            );

            return $response->withRedirect((string)$uri, 301);
        }

        $this->view->render(
            $response,
            'admin/Listing/new.html.twig',
            array(
                'channel' => $channel,
            )
        );

        return $response->write($body);
    })->setName('admin_listing_new');

    $this->map(['GET', 'POST'], '/edit', function ($request, $response, $args) {

        $channelRepository = $this->getContainer()->get('tvlistings.channel.repository');
        $channel = $channelRepository->findOneBySlug($args['slug']);
        if ($request->isPost()) {
            $parsedBody = $request->getParsedBody();
            $channel->changeName($parsedBody['name']);
            $channel->changeLogoPath($parsedBody['logoPath']);
            $channelRepository->persist($channel);

            $uri = $this->router->pathFor(
                'admin_homepage',
                array()
            );

            return $response->withRedirect((string)$uri, 301);
        }

        $this->view->render(
            $response,
            'admin/edit.html.twig',
            array(
                'channel' => $channel,
            )
        );

        return $response->write($body);
    })->setName('admin_channel_edit');
});

// Run!
$app->run();
