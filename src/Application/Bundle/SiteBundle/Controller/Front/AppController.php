<?php

namespace Application\Bundle\SiteBundle\Controller\Front;

use Application\Bundle\SiteBundle\Document\Url;
use Application\Bundle\SiteBundle\Document\UrlDirection;
use Application\Bundle\SiteBundle\Manager\UrlDirectionManager;
use Application\Bundle\SiteBundle\Manager\UrlManager;
use Application\Bundle\SiteBundle\Repository\SiteRepositoryInterface;
use Application\Component\Link\ParserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Zend\Http\Client\Exception\ExceptionInterface as ZendHttpClientException;
use Zend\Uri\Uri;

/**
 * AppController
 *
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class AppController extends Controller
{
    /**
     * Display homepage.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $strDate = '2015-05-12 23:00:00';
        $date = new \DateTime($strDate);

        $response = new Response();
        $response->setLastModified($date);
        $response->setEtag(md5($strDate));
        $response->setPublic();

        if ($response->isNotModified($request)) {
            return $response;
        }

        $response->setExpires($date->add(new \DateInterval('P1D')));

        return $this->render('@ApplicationSite/layout.html.twig', [
            'userAgent' => $this->container->getParameter('user-agent-full')
        ], $response);
    }

    /**
     * Search and found an url.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function searchAction(Request $request)
    {
        $uri = new Uri($request->query->get('url'));

        if (!$this->getUrlManager()->isValid($uri)) {
            return new JsonResponse(null, Response::HTTP_BAD_REQUEST);
        }

        $url = $this->getUrlManager()->findOneByUri($uri);

        if (null !== $url && $this->getUrlManager()->isUpToDate($url)) {
            $url->addOutUrls($this->getUrlDirectionManager()->findByFrom($url));

            return $this->createJsonResponse($request, $url);
        }

        try {
            $report = $this->getParser()->parse($uri, 3);
        } catch (ZendHttpClientException $e) {
            return new JsonResponse(null, Response::HTTP_GATEWAY_TIMEOUT);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(null, Response::HTTP_BAD_REQUEST);
        }

        if (null !== $report->getSite()) {
            $this->getSiteRepository()->save($report->getSite());
        }

        /** @var Url $url */
        $url = $report->getUrl();

        $this->sendParserAsync($url);
        $this->getUrlManager()->save($url);

        if ($url->hasProvider('page')) {
            $this->sendWebArchiveAsync($url);
        }

        $outLinks = [$url->getHash()];

        /** @var Url $out */
        foreach ($url->getOut() as $out) {
            if (in_array($out->getHash(), $outLinks)) {
                continue;
            }

            $outLinks[] = $out->getHash();

            $this->sendParserAsync($url);
            $this->getUrlManager()->save($out);

            if (!$this->getUrlDirectionManager()->exists($url, $out)) {
                $direction = new UrlDirection();
                $direction->setFrom($url);
                $direction->setTo($out);

                $this->getUrlDirectionManager()->save($direction);
            }
        }

        return $this->createJsonResponse($request, $url);
    }

    /**
     * Create new response with JSON content-type.
     *
     * @param Request $request
     * @param Url     $url
     *
     * @return Response
     */
    protected function createJsonResponse(Request $request, Url $url)
    {
        $json = json_encode($url);

        if (JSON_ERROR_NONE !== json_last_error()) {
            $this->container->get('logger')->critical(json_last_error_msg());

            return new JsonResponse(null, Response::HTTP_BAD_GATEWAY);
        }

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setLastModified($url->getUpdatedAt());
        $response->setEtag(md5($json . $url->getHash()));
        $response->setPublic();

        if ($response->isNotModified($request)) {
            return $response;
        }

        $response->setContent($json);

        return $response;
    }

    /**
     * Returns the url parser.
     *
     * @return ParserInterface
     */
    private function getParser()
    {
        return $this->container->get('site.link.parser');
    }

    /**
     * Returns the url manager.
     *
     * @return UrlManager
     */
    private function getUrlManager()
    {
        return $this->container->get('site.link.url_manager');
    }

    /**
     * Returns the site repository.
     *
     * @return SiteRepositoryInterface
     */
    private function getSiteRepository()
    {
        return $this->container->get('site.link.site_repository');
    }

    /**
     * Returns the url direction manager.
     *
     * @return UrlDirectionManager
     */
    private function getUrlDirectionManager()
    {
        return $this->container->get('site.link.url_direction_manager');
    }

    /**
     * Execute an asynchrone "parser".
     *
     * @param Url $url
     */
    private function sendParserAsync(Url $url)
    {
        $url->setStatus($url::STATUS_WAITING);

        $this->container->get('site.link.parser_async_producer')->send(['url' => $url]);
    }

    /**
     * Execute an asynchrone "web archive".
     *
     * @param Url $url
     */
    private function sendWebArchiveAsync(Url $url)
    {
        $this->container->get('site.link.parser_async_producer')->send(['url' => $url]);
    }
}
