<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SearchBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Bundle\SearchBundle\Query\SearchStringQuery;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Search landing page controller.
 *
 * @author Argyrios Gounaris <agounaris@gmail.com>
 */
class SearchController extends ResourceController
{
    /**
     * Search landing page.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $finder = $this->get('sylius_search.finder')
            ->setTargetIndex('product')
            ->setFacetGroup('search_set')
            ->find(new SearchStringQuery(
                    $request,
                    $this->container->getParameter('sylius_search.pre_search_filter.enabled')
                )
            );

        $paginator = $finder->getPaginator();

        $searchConfig = $this->container->getParameter("sylius_search.config");

        if ($paginator) {
            $paginator->setMaxPerPage($this->config->getPaginationMaxPerPage());
            $paginator->setCurrentPage($this->get('sylius_search.request_handler')->getPage());
        }

        $view = $this
            ->view()
            ->setTemplate('SyliusSearchBundle::index.html.twig')
            ->setData(array(
                'results' => $paginator,
                'facets' => $finder->getFacets(),
                'facetTags' => $searchConfig['filters']['facets'],
                'filters' => $finder->getFilters(),
                'searchTerm' => $this->get('sylius_search.request_handler')->getQuery(),
                'searchParam' => $this->get('sylius_search.request_handler')->getSearchParam(),
                'requestMethod' => $this->container->getParameter('sylius_search.request.method'),
            ));

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function formAction(Request $request)
    {
        $filters = array();

        if ($this->container->getParameter('sylius_search.pre_search_filter.enabled')) {
            $taxonomy = $this->get('sylius.repository.taxonomy')
                ->findOneBy(
                    array(
                        'name' => strtoupper($this->container->getParameter('sylius_search.pre_search_filter.taxon'))
                    )
                );

            $filters = array();
            if ($taxonomy) {
                foreach ($taxonomy->getTaxons() as $taxon) {
                    $filters[] = $taxon->getName();
                }
            }

        }

        $this->get('sylius_search.request_handler')->setRequest($request);

        $view = $this
            ->view()
            ->setTemplate($this->container->getParameter('sylius_search.search.template'))
            ->setData(array(
                'filters' => $filters,
                'searchTerm' => $this->get('sylius_search.request_handler')->getQuery(),
                'searchParam' => $this->get('sylius_search.request_handler')->getSearchParam(),
                'requestMethod' => $this->container->getParameter('sylius_search.request.method'),
            ));

        return $this->handleView($view);
    }
}
