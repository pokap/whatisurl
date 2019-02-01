<?php

namespace Application\Bundle\SiteBundle\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class SearchController extends Controller
{
    /**
     * Returns list of urls given by host & provider type.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function typeAction(Request $request)
    {
        $request->query->get('host');
        $request->query->get('type');
    }
}
