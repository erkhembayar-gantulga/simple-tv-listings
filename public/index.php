<?php

use Symfony\Component\Yaml\Parser;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use TVListings\Domain\Service\DoctrineEntityManager;
use TVListings\Domain\Service\VideoProxyService;
use TVListings\Domain\Repository\ChannelRepository;
use TVListings\Domain\Repository\ListingRepository;
use TVListings\Domain\Repository\VideoProxyRepository;
use TVListings\Domain\Entity\Channel;
use TVListings\Domain\Entity\Listing;
use TVListings\Infrastructure\TwigExtension\DayOfWeekExtension;

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

$yaml = new Parser();
try {
    $configuration = $yaml->parse(file_get_contents(__DIR__ . '/../app/config/config.yml'));
} catch (ParseException $e) {
    printf("Unable to parse the YAML string: %s", $e->getMessage());
}

$app = new \Slim\App($configuration);

$container = $app->getContainer();

// Register component on container
$container['view'] = function ($c) {
    $settings = $c->get('settings');
    $twigOptions = $settings['view']['twig'];
    $twigOptions['cache'] = __DIR__ . $settings['view']['twig']['cache'];
    $view = new \Slim\Views\Twig(__DIR__ . $settings['view']['template_path'], $twigOptions);
    $view->addExtension(new \Slim\Views\TwigExtension(
        $c->get('router'),
        $c->get('request')->getUri()
    ));
    $view->addExtension(new Twig_Extension_Debug());
    $view->addExtension(new DayOfWeekExtension());

    return $view;
};

$container['doctrine.orm.entity_manager'] = function ($container) {
    $settings = $container->get('settings');
    $isDevMode = true;
    $config = Setup::createAnnotationMetadataConfiguration(
        array(
            __DIR__ . $settings['doctrine']['mapping_path'],
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

$container['tvlistings.video_proxy.service'] = function ($container) {
    $em = new DoctrineEntityManager($container->get('doctrine.orm.entity_manager'));

    return new VideoProxyService($em);
};

$container['tvlistings.video_proxy.repository'] = function ($container) {
    $em = new DoctrineEntityManager($container->get('doctrine.orm.entity_manager'));

    return new VideoProxyRepository($em);
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
            'specifiedDate' => new \DateTimeImmutable(),
        )
    );

    return $response;
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

    $specifiedDate = new \DateTimeImmutable($request->getParam('on'));
    $listings = $container->get('tvlistings.channel.repository')->getListingsOf($channel, $specifiedDate);

    $this->view->render(
        $response,
        'base.html.twig',
        array(
            'channel' => $channel,
            'listings' => $listings,
            'specifiedDate' => $specifiedDate,
        )
    );

    return $response;
})->setName('channel_by_date');

$app->get('/listings/{id}', function ($request, $response, $args) {
    $container = $this->getContainer();

    $listing = $container->get('tvlistings.listing.repository')->find($args['id']);

    $this->view->render(
        $response,
        'listing_detail.html.twig',
        array(
            'listing' => $listing,
        )
    );

    return $response;
})->setName('listing_detail');

$app->get('/videos/{uuid}', function ($request, $response, $args) {
    $container = $this->getContainer();

    $videoProxy = $container->get('tvlistings.video_proxy.repository')->find($args['uuid']);

    if (null === $videoProxy) {
        $uri = $this->router->pathFor(
            'homepage',
            array()
        );

        return $response->withRedirect((string)$uri, 301);
    }

    $this->view->render(
        $response,
        'show_video.html.twig',
        array(
            'videoProxy' => $videoProxy,
        )
    );

    return $response;
})->setName('show_video');

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

    return $response;
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

    return $response;
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

        return $response;
    })->setName('admin_channel_show');

    $this->map(['GET', 'POST'], '/listings/new', function ($request, $response, $args) {

        $channel = $this->getContainer()->get('tvlistings.channel.repository')->findOneBySlug($args['slug']);
        if ($request->isPost()) {
            $parsedBody = $request->getParsedBody();
            $listingRepository = $this->getContainer()->get('tvlistings.listing.repository');
            $listing = new Listing($channel, $parsedBody['title'], new \DateTime($parsedBody['programDate']));
            $listing->programAt($parsedBody['programAt']);
            $listing->setDescription($parsedBody['description']);
            $listingRepository->persist($listing);

            $videoProxyService = $this->getContainer()->get('tvlistings.video_proxy.service');
            $videoProxyUuid = $videoProxyService->convertFromSource($parsedBody['video_source']);
            $listing->changeResourceLink($videoProxyUuid);
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

        return $response;
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

        return $response;
    })->setName('admin_channel_edit');
});

$app->post('/admin/listings/{id}', function ($request, $response, $args) {
    $listingRepository = $this->getContainer()->get('tvlistings.listing.repository');
    $listing = $listingRepository->find($args['id']);
    if (null === $listing) {
        $uri = $this->router->pathFor(
            'admin_homepage',
            array()
        );

        return $response->withRedirect((string)$uri, 301);
    }

    $slug = $listing->getChannel()->getSlug();
    $listingRepository->delete($listing);

    $uri = $this->router->pathFor(
        'admin_channel_show',
        array(
           'slug' => $slug,
        )
    );

    return $response->withRedirect((string)$uri, 301);
})->setName('admin_listing_delete');

$app->group('/admin/listings/{id}', function () {
    $this->map(['GET', 'POST'], '/edit', function ($request, $response, $args) {

        $listingRepository = $this->getContainer()->get('tvlistings.listing.repository');
        $listing = $listingRepository->find($args['id']);
        if ($request->isPost()) {
            $parsedBody = $request->getParsedBody();
            $listing->changeTitle($parsedBody['title']);
            $listing->changeProgramDate(new \DateTime($parsedBody['programDate']));
            $listing->programAt($parsedBody['programAt']);
            $listing->changeResourceLink($parsedBody['resourceLink']);
            $listing->setDescription($parsedBody['description']);
            $listingRepository->persist($listing);

            $videoProxyService = $this->getContainer()->get('tvlistings.video_proxy.service');
            $videoProxyUuid = $videoProxyService->convertFromSource($parsedBody['video_source']);
            $listing->changeResourceLink($videoProxyUuid);
            $listingRepository->persist($listing);

            $uri = $this->router->pathFor(
                'admin_channel_show',
                array(
                    'slug' => $listing->getChannel()->getSlug(),
                )
            );

            return $response->withRedirect((string)$uri, 301);
        }

        $this->view->render(
            $response,
            'admin/Listing/edit.html.twig',
            array(
                'listing' => $listing,
            )
        );

        return $response;
    })->setName('admin_listing_edit');
});

// Run!
$app->run();
