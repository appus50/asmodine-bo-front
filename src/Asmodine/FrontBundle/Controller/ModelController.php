<?php

namespace Asmodine\FrontBundle\Controller;

use Asmodine\CommonBundle\Service\ElasticsearchService;
use Asmodine\CommonBundle\Model\Elasticsearch\Response as ESResponse;
use Asmodine\FrontBundle\Entity\Brand;
use Asmodine\FrontBundle\Entity\Category;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class ModelsController.
 *
 * @Route("/catalogue")
 */
class ModelController extends AbstractController
{
    /**
     * @Route("/marque/{slug}", name="asmodinefront_model_brand", requirements={"slug"=".+"})
     *
     * @param Request $request
     * @param Brand   $brand
     *
     * @return Response
     *
     * @throws \Asmodine\CommonBundle\Exception\EnumParameterException
     */
    public function brandAction(Request $request, Brand $brand)
    {
        $breadcrumb = [
            [
                'name' => $this->getTranslator()->trans('breadcrumb.title.brands'),
                'url' => $this->generateUrl('asmodinefront_page_brands'),
            ],
        ];
        $parameters = [
            'title' => $brand->getName(),
            'description' => $brand->getDescription(),
            'title_image' => $brand->getLogo(),
            'breadcrumb' => $breadcrumb,
        ];
        $match = ['brand_id' => $brand->getBackId()];

        return $this->showModels($request, $parameters, $match, null, true, false, false);
    }

    /**
     * @Route("/categorie/{path}", name="asmodinefront_model_category", requirements={"path"="[^?]+"})
     *
     * @param Request  $request
     * @param Category $category
     *
     * @return Response
     *
     * @throws \Asmodine\CommonBundle\Exception\EnumParameterException
     */
    public function categoryAction(Request $request, Category $category)
    {
        $cat0 = null;
        $depth = $category->getDepth();
        $description = $category->getDescription();

        if (is_null($description)) {
            $name = $category->getName();
            if (0 == $depth) {
                $name = $this->getTranslator()->trans('home.category.clothes');
            }
            $cat0 = $this->getDoctrine()
                ->getRepository('AsmodineFrontBundle:Category')
                ->findOneBy(['gender' => $category->getGender(), 'depth' => 0]);

            $description = $this->getTranslator()->trans(
                'model.description.category',
                ['%name%' => $name, '%gender%' => $cat0->getName()]
            );
        }

        $parameters = [
            'title' => $category->getName(),
            'description' => $description,
            'title_image' => null,
            'breadcrumb' => $this->getBreadcrumbCategory($category),
        ];
        $match = ['category_depth'.$depth.'_id' => $category->getBackId()];

        return $this->showModels($request, $parameters, $match, null, false, true, 0 == $depth);
    }

    /**
     * @Route("/conseil", name="asmodinefront_model_advice")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function adviceAction(Request $request)
    {
        /** @var Session $session */
        $session = $this->get('session');
        $session->set('advice', !$session->get('advice', true));
        $session->save();

        return $this->redirect($request->server->get('HTTP_REFERER'));
    }

    /**
     * @Route("/filters", name="asmodinefront_model_filters")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function filtersAction(Request $request)
    {
        //FIXME Reuse search
        $route = $request->get('route');
        if (false !== strpos($route, '?')) {
            $route = substr($route, 0, strpos($route, '?'));
        }
        if ('delete' === $request->get('action')) {
            return new Response($route);
        }
        $filters = json_decode($request->get('filters', []));

        $f = [];
        for ($i = 0; $i < count($filters); ++$i) {
            foreach ($filters[$i] as $k => $v) {
                $f[$k][] = $v;
            }
        }

        return new Response($route.'?filters='.urlencode(json_encode($f)));
    }

    /**
     * @Route("/rechercher/{search}", name="asmodinefront_model_search")
     *
     * @param Request $request
     * @param string  $search
     *
     * @return Response
     *
     * @throws \Asmodine\CommonBundle\Exception\EnumParameterException
     */
    public function searchAction(Request $request, string $search)
    {
        $breadcrumb = [];
        $breadcrumb[] = $this->getBreadcrumbHome();
        $parameters = [
            'title' => $this->getTranslator()->trans('search.title'),
            'description' => $this->getTranslator()->trans('search.description'),
            'title_image' => null,
            'breadcrumb' => $breadcrumb,
        ];

        return $this->showModels($request, $parameters, [], urldecode($search), false, false, false);
    }

    /**
     * Construct Elasticsearch Query and show models.
     *
     * @param Request     $request
     * @param array       $parameters
     * @param array       $match
     * @param null|string $search
     * @param bool        $fromBrand
     * @param bool        $fromCategory
     * @param bool        $fromGender
     *
     * @return Response
     *
     * @throws \Asmodine\CommonBundle\Exception\EnumParameterException
     */
    private function showModels(Request $request, array $parameters, array $match, ?string $search, bool $fromBrand, bool $fromCategory, bool $fromGender): Response
    {
        /** @var ElasticsearchService $esService */
        $esService = $this->get('asmodine.common.elasticsearch');
        $advice = $this->get('session')->get('advice', true);
        $page = $request->get('page', 1);
        $sort = $this->getSort($request);
        $aggs = $this->getAggregations();
        $search = is_null($search) ? $request->get('search') : $search;

        // Filters
        $filters = $request->get('filters');
        if (!is_null($filters)) {
            $filters = json_decode(urldecode($filters), true);
            if (isset($filters['gender']) && isset($filters['category'])) {
                unset($filters['gender']);
            }
        }

        // Help
        $sessionHelp = $request->getSession()->get('sg_help', false);
        $urlHelp = $request->get('help', null);
        if (!is_null($urlHelp)) {
            $sessionHelp = in_array(strtolower($urlHelp), ['oui', '1', 'true', 'help', 'valid']);
        }
        $request->getSession()->set('sg_help', $sessionHelp);

        $filtersUser = $filters;
        // Override categorie level 2 on gender
        if (isset($match['category_depth0_id']) && isset($filters['category'][0])) {
            unset($match['category_depth0_id']);
            $match['category_depth2_id'] = $filters['category'][0];
            unset($filters['category']);
        }

        $query = $this->getInitQuery($match, $search, $filters);

        $esResponse = $esService->search(ElasticsearchService::MODEL, $query, 9, 1, [], $aggs, null, []);
        $filtersContent = $esResponse->getAggregations();

        $sizesOrdred = ['12XL' => 0, '11XL' => 0, '10XL' => 0, '9XL' => 0, '8XL' => 0, '7XL' => 0, '6XL' => 0, '5XL' => 0, '4XL' => 0, '3XL' => 0, '2XL' => 0, '3XS' => 0, 'XXXXXXXXXXXXL' => 0, 'XXXXXXXXXXXL' => 0, 'XXXXXXXXXXL' => 0, 'XXXXXXXXL' => 0, 'XXXXXXXL' => 0, 'XXXXXXL' => 0, 'XXXXXL' => 0, 'XXXXL' => 0, 'XXXL' => 0, 'XXL' => 0, 'XL/XXL' => 0, 'XL' => 0, 'L/XL' => 0, 'L' => 0, 'M/L' => 0, 'M' => 0, 'S/M' => 0, 'S' => 0, 'XS/S' => 0, 'XS' => 0, 'XXS/XS' => 0, 'XXS' => 0, 'XXXS' => 0, 'XXXXXXXXXXXXLL' => 0, 'XXXXXXXXXXXLL' => 0, 'XXXXXXXXXXLL' => 0, 'XXXXXXXXLL' => 0, 'XXXXXXXLL' => 0, 'XXXXXXLL' => 0, 'XXXXXLL' => 0, 'XXXXLL' => 0, 'XXXLL' => 0, 'XXLL' => 0, 'XLL' => 0, 'LL' => 0, 'ML' => 0, 'SL' => 0, 'XSL' => 0, 'XXSL' => 0, 'XXXSL' => 0, ':60' => 0, ':59' => 0, ':58' => 0, ':57' => 0, ':56' => 0, ':55' => 0, ':54' => 0, ':53' => 0, ':52' => 0, ':51' => 0, ':50' => 0, ':49' => 0, ':48' => 0, ':47' => 0, ':46' => 0, ':45' => 0, ':44' => 0, ':43' => 0, ':42' => 0, ':41' => 0, ':40' => 0, ':39' => 0, ':38' => 0, ':37' => 0, ':36' => 0, ':35' => 0, ':34' => 0, ':33' => 0, ':32' => 0, ':31' => 0, ':30' => 0, ':29' => 0, ':28' => 0, ':27' => 0, ':26' => 0, ':25' => 0, ':24' => 0, ':23' => 0, ':22' => 0, ':21' => 0, ':20' => 0, ':19' => 0, ':18' => 0, ':17' => 0, ':16' => 0, ':15' => 0, ':14' => 0, ':13' => 0, ':12' => 0, ':11' => 0, ':10' => 0, ':9' => 0, ':8' => 0, ':7' => 0, ':6' => 0, ':5' => 0, ':4' => 0, ':3' => 0, ':2' => 0, ':1' => 0, ':0' => 0, '70' => 0, '69' => 0, '68' => 0, '67' => 0, '66' => 0, '65' => 0, '64' => 0, '63' => 0, '62' => 0, '61' => 0, '60' => 0, '59' => 0, '58' => 0, '57' => 0, '56' => 0, '55' => 0, '54' => 0, '52' => 0, '51' => 0, '50.5' => 0, '50' => 0, '49.5' => 0, '49' => 0, '48.5' => 0, '48' => 0, '47.5' => 0, '47' => 0, '46.5' => 0, '46' => 0, '45.5' => 0, '45' => 0, '44.5' => 0, '44' => 0, '43' => 0, '42.5' => 0, '42' => 0, '41.5' => 0, '41' => 0, '40.5' => 0, '40' => 0, '39.5' => 0, '39' => 0, '38' => 0, '37.5' => 0, '37' => 0, '36.5' => 0, '36' => 0, '35' => 0, '34' => 0, '33' => 0, '32' => 0, '31' => 0, '30' => 0, '29' => 0, '28' => 0, '27' => 0, '26' => 0, '25' => 0, '24' => 0, '23' => 0, '22' => 0, '21' => 0, '20' => 0, '19' => 0, '18' => 0, '17' => 0, '16' => 0, '15' => 0, '14' => 0, '13' => 0, '12' => 0, '11' => 0, '10' => 0, '9' => 0, '8' => 0, '7' => 0, '6' => 0, '5' => 0, '4' => 0, '3' => 0, '2' => 0, '1' => 0, '0' => 0, '150/200' => 0, '140/200' => 0, '128/134' => 0, '116/122' => 0, '104/110' => 0, '58/60' => 0, '54/56' => 0, '50/52' => 0, '46/44' => 0, '46/42' => 0, '46/40' => 0, '46/38' => 0, '46/36' => 0, '46/34' => 0, '46/33' => 0, '46/32' => 0, '46/31' => 0, '46/30' => 0, '44/44' => 0, '44/42' => 0, '44/40' => 0, '44/38' => 0, '44/36' => 0, '44/34' => 0, '44/33' => 0, '44/32' => 0, '44/31' => 0, '44/30' => 0, '42/44' => 0, '42/42' => 0, '42/40' => 0, '42/38' => 0, '42/36' => 0, '42/34' => 0, '42/33' => 0, '42/32' => 0, '42/31' => 0, '42/30' => 0, '40/44' => 0, '40/42' => 0, '40/40' => 0, '40/38' => 0, '40/36' => 0, '40/34' => 0, '40/33' => 0, '40/32' => 0, '40/30' => 0, '39/44' => 0, '39/42' => 0, '39/40' => 0, '39/36' => 0, '39/34' => 0, '39/32' => 0, '39/31' => 0, '39/30' => 0, '38/44' => 0, '38/42' => 0, '38/40' => 0, '38/38' => 0, '38/36' => 0, '38/34' => 0, '38/33' => 0, '38/32' => 0, '38/31' => 0, '38/30' => 0, '37/44' => 0, '37/42' => 0, '37/40' => 0, '37/38' => 0, '37/36' => 0, '37/34' => 0, '37/33' => 0, '37/32' => 0, '37/31' => 0, '37/30' => 0, '36/44' => 0, '36/42' => 0, '36/40' => 0, '36/38' => 0, '36/36' => 0, '36/34' => 0, '36/33' => 0, '36/32' => 0, '36/31' => 0, '36/30' => 0, '35/44' => 0, '35/42' => 0, '35/40' => 0, '35/38' => 0, '35/36' => 0, '35/34' => 0, '35/33' => 0, '35/32' => 0, '35/31' => 0, '35/30' => 0, '34/44' => 0, '34/42' => 0, '34/40' => 0, '34/38' => 0, '34/36' => 0, '34/34' => 0, '34/33' => 0, '34/32' => 0, '34/31' => 0, '34/30' => 0, '33/44' => 0, '33/42' => 0, '33/40' => 0, '33/38' => 0, '33/36' => 0, '33/34' => 0, '33/33' => 0, '33/32' => 0, '33/31' => 0, '33/30' => 0, '32/44' => 0, '32/42' => 0, '32/40' => 0, '32/38' => 0, '32/36' => 0, '32/34' => 0, '32/32' => 0, '32/31' => 0, '32/30' => 0, '31/44' => 0, '31/42' => 0, '31/40' => 0, '31/38' => 0, '31/36' => 0, '31/34' => 0, '31/33' => 0, '31/32' => 0, '31/31' => 0, '31/30' => 0, '30/44' => 0, '30/42' => 0, '30/40' => 0, '30/38' => 0, '30/36' => 0, '30/34' => 0, '30/33' => 0, '30/32' => 0, '30/30' => 0, '29/44' => 0, '29/42' => 0, '29/40' => 0, '29/38' => 0, '29/36' => 0, '29/34' => 0, '29/33' => 0, '29/32' => 0, '29/31' => 0, '29/30' => 0, '28/40' => 0, '28/42' => 0, '28/38' => 0, '28/36' => 0, '28/34' => 0, '28/33' => 0, '28/32' => 0, '28/31' => 0, '28/30' => 0, '27/44' => 0, '27/42' => 0, '27/40' => 0, '27/38' => 0, '27/36' => 0, '27/34' => 0, '27/33' => 0, '27/32' => 0, '27/31' => 0, '27/30' => 0, '26/44' => 0, '26/42' => 0, '26/40' => 0, '26/38' => 0, '26/36' => 0, '26/34' => 0, '26/33' => 0, '26/32' => 0, '26/31' => 0, '26/30' => 0, '25/44' => 0, '25/42' => 0, '25/40' => 0, '25/38' => 0, '25/36' => 0, '25/34' => 0, '25/33' => 0, '25/32' => 0, '25/31' => 0, '25/30' => 0, '24/44' => 0, '24/42' => 0, '24/40' => 0, '24/38' => 0, '24/36' => 0, '24/34' => 0, '24/33' => 0, '24/32' => 0, '24/31' => 0, '54extra short' => 0, '54 short' => 0, '54 regular' => 0, '54 long' => 0, '54 extra long ' => 0, '54 extra extra long' => 0, '52 extra short' => 0, '52 short' => 0, '52 regular' => 0, '52 long' => 0, '52 extra long ' => 0, '52 extra extra long' => 0, '50 extra short' => 0, '50 short' => 0, '50 regular' => 0, '50 long' => 0, '50 extra long ' => 0, '50 extra extra long' => 0, '48 extra short' => 0, '48 short' => 0, '48 regular' => 0, '48 long' => 0, '48 extra long ' => 0, '48 extra extra long' => 0, '46 extra short' => 0, '46 short' => 0, '46 regular' => 0, '46 long' => 0, '46 extra long ' => 0, '46 extra extra long' => 0, '44 extra short' => 0, '44 short' => 0, '44 regular' => 0, '44 long' => 0, '44 extra long ' => 0, '44 extra extra long' => 0, '42 extra short' => 0, '42 short' => 0, '42 regular' => 0, '42 long' => 0, '42 extra long ' => 0, '42 extra extra long' => 0, '40 extra short' => 0, '40 short' => 0, '40 regular' => 0, '40 long' => 0, '40 extra long ' => 0, '40 extra extra long' => 0, '38 extra short' => 0, '38 short' => 0, '38 regular' => 0, '38 long' => 0, '38 extra long ' => 0, '38 extra extra long' => 0, '36 extra short' => 0, '36 short' => 0, '36 regular' => 0, '36 long' => 0, '36 extra long ' => 0, '36 extra extra long' => 0, '34 extra short' => 0, '34 short' => 0, '34 regular' => 0, '34 long' => 0, '34 extra long ' => 0, '34 extra extra long' => 0, '32 regular' => 0, '32 long' => 0, '32 extra long ' => 0, '32 extra extra long' => 0, '30 extra short' => 0, '30 short' => 0, '30 regular' => 0, '30 long' => 0, '30 extra long ' => 0, '30 extra extra long' => 0, '28 extra short' => 0, '28 short' => 0, '28 regular' => 0, '28 long' => 0, '28 extra long ' => 0, '28 extra extra long' => 0, '27 extra short' => 0, '27 short' => 0, '27 regular' => 0, '27 long' => 0, '27 extra long ' => 0, '27 extra extra long' => 0, '48 36' => 0, '48 34' => 0, '48 32' => 0, '48 30' => 0, '48 28' => 0, '46 36' => 0, '46 34' => 0, '46 32' => 0, '46 30' => 0, '46 28' => 0, '44 36' => 0, '44 34' => 0, '44 32' => 0, '44 30' => 0, '44 28' => 0, '42 36' => 0, '42 34' => 0, '42 32' => 0, '42 30' => 0, '42 28' => 0, '40 36' => 0, '40 34' => 0, '40 32' => 0, '40 30' => 0, '40 28' => 0, '38 36' => 0, '38 34' => 0, '38 32' => 0, '38 30' => 0, '38 28' => 0, '36 36' => 0, '36 34' => 0, '36 32' => 0, '36 30' => 0, '36 28' => 0, '34 36' => 0, '34 34' => 0, '34 32' => 0, '34 30' => 0, '34 28' => 0, '32 34' => 0, '32 32' => 0, '32 30' => 0, '32 28' => 0, '30 34' => 0, '30 32' => 0, '30 30' => 0, '30 28' => 0, '28 34' => 0, '28 32' => 0, '28 30' => 0, '28 28' => 0, '26 34' => 0, '26 32' => 0, '26 30' => 0, '26 28' => 0, 'EU 48' => 0, 'EU 47' => 0, 'EU 46' => 0, 'EU 45' => 0, 'EU 44' => 0, 'EU 44.5' => 0, 'EU 43' => 0, 'EU 42.5' => 0, 'EU 42' => 0, 'EU 41' => 0, 'EU 40.5' => 0, 'EU 40' => 0, 'EU 39' => 0, 'EU 38' => 0, 'EU 37' => 0, 'EU 36' => 0, 'EU 35.5' => 0, 'EU 35' => 0, 'UK 48 S FR 58S' => 0, 'UK 48 R FR 58R' => 0, 'UK 48 L FR 58L' => 0, 'UK 46 S FR 56S' => 0, 'UK 46 R FR 56R' => 0, 'UK 46 L FR 56L' => 0, 'UK 44 S FR 54S' => 0, 'UK 44 R FR 54R' => 0, 'UK 44 L FR 54L' => 0, 'UK 42 S FR 52S' => 0, 'UK 42 R FR 52R' => 0, 'UK 42 L FR 52L' => 0, 'UK 40 S FR 50S' => 0, 'UK 40 R FR 50R' => 0, 'UK 40 L FR 50L' => 0, 'UK 38 S FR 48S' => 0, 'UK 38 R FR 48R' => 0, 'UK 38 L FR 48L' => 0, 'UK 36 S FR 46S' => 0, 'UK 36 R FR 46R' => 0, 'UK 36 L FR 46L' => 0, 'UK 34 R FR 44R' => 0, 'UK 34 S FR 44S' => 0, 'UK 34 L FR 44L' => 0, 'UK 32 R FR 42R' => 0, 'UK 32 S FR 42S' => 0, 'UK 32 L FR 42L' => 0, 'UK 30 R FR 40R' => 0, 'UK 30 L FR 40L' => 0, 'UK 30 S FR 40S' => 0, 'UK 28 R FR 38R' => 0, 'UK 28 S FR 38S' => 0, 'UK 28 L FR 38L' => 0, 'UK 26 R FR 36R' => 0, 'UK 26 S FR 36S' => 0, 'UK 26 L FR 36L' => 0, 'W46' => 0, 'W44' => 0, 'W42' => 0, 'W40' => 0, 'W38' => 0, 'W36' => 0, 'W34' => 0, 'W32' => 0, 'W30' => 0, 'W28' => 0, 'W26' => 0, 'W48 L34' => 0, 'W48 L32' => 0, 'W48 L30' => 0, 'W46 L34' => 0, 'W46 L32' => 0, 'W46 L30' => 0, 'W44 L40' => 0, 'W44 L38' => 0, 'W44 L36' => 0, 'W44 L34' => 0, 'W44 L32' => 0, 'W44 L30' => 0, 'W42 L40' => 0, 'W42 L38' => 0, 'W42 L36' => 0, 'W42 L34' => 0, 'W42 L32' => 0, 'W42 L30' => 0, 'W40 L40' => 0, 'W40 L38' => 0, 'W40 L36' => 0, 'W40 L34' => 0, 'W40 L32' => 0, 'W40 L30' => 0, 'W38 L40' => 0, 'W38 L38' => 0, 'W38 L36' => 0, 'W38 L34' => 0, 'W38 L32' => 0, 'W38 L30' => 0, 'W36 L40' => 0, 'W36 L38' => 0, 'W36 L36' => 0, 'W36 L34' => 0, 'W36 L32' => 0, 'W36 L30' => 0, 'W35 L34' => 0, 'W35 L30' => 0, 'W34 L40' => 0, 'W34 L38' => 0, 'W34 L36' => 0, 'W34 L34' => 0, 'W34 L32' => 0, 'W34 L30' => 0, 'W34 L29' => 0, 'W34 L28' => 0, 'W33 L34' => 0, 'W33 L32' => 0, 'W33 L30' => 0, 'W32 L36' => 0, 'W32 L34' => 0, 'W32 L32' => 0, 'W32 L30' => 0, 'W32 L29' => 0, 'W32 L28' => 0, 'W32 L26' => 0, 'W31 L34' => 0, 'W31 L32' => 0, 'W31 L30' => 0, 'W30 L38' => 0, 'W30 L36' => 0, 'W30 L34' => 0, 'W30 L32' => 0, 'W30 L30' => 0, 'W30 L28' => 0, 'W29 L34' => 0, 'W29 L32' => 0, 'W29 L30' => 0, 'W28 L38' => 0, 'W28 L36' => 0, 'W28 L34' => 0, 'W28 L32' => 0, 'W28 L30' => 0, 'W28 L28' => 0, 'W28 L26' => 0, 'W26 L38' => 0, 'W26 L36' => 0, 'W26 L34' => 0, 'W26 L32' => 0, 'W26 L30' => 0, 'W26 L28' => 0, 'W26 L26' => 0, '48R' => 0, '46R' => 0, '44R' => 0, '44S' => 0, '42R' => 0, '42S' => 0, '40R' => 0, '40S' => 0, '38L' => 0, '38R' => 0, '38S' => 0, '36L' => 0, '36R' => 0, '36S' => 0, '34L' => 0, '34R' => 0, '34S' => 0, '32L' => 0, '32R' => 0, '32S' => 0, '16L' => 0, '16R' => 0, '16S' => 0, '14L' => 0, '14R' => 0, '14S' => 0, '12L' => 0, '12R' => 0, '12S' => 0, '10L' => 0, '10R' => 0, '10S' => 0, '8L' => 0, '8R' => 0, '8S' => 0, '6L' => 0, '6R' => 0, '6S' => 0, '4L' => 0, '4R' => 0, '4S' => 0, '120G' => 0, '120F' => 0, '120E' => 0, '120D' => 0, '120C' => 0, '115H ' => 0, '115G' => 0, '115F' => 0, '115E' => 0, '115D' => 0, '115C ' => 0, '115B' => 0, '115AA' => 0, '115A' => 0, '110H' => 0, '110G' => 0, '110F' => 0, '110E' => 0, '110D' => 0, '110C' => 0, '110B' => 0, '110AA' => 0, '110A' => 0, '105H' => 0, '105G' => 0, '105F' => 0, '105E' => 0, '105D' => 0, '105C' => 0, '105B' => 0, '105AA' => 0, '105A' => 0, '100H' => 0, '100G' => 0, '100F' => 0, '100E' => 0, '100D' => 0, '100C' => 0, '100B ' => 0, '100AA' => 0, '100A' => 0, '95H' => 0, '95G' => 0, '95F' => 0, '95E' => 0, '95D' => 0, '95C' => 0, '95B' => 0, '95AA' => 0, '95A' => 0, '90H' => 0, '90G' => 0, '90F' => 0, ' 90E' => 0, '90D' => 0, '90C' => 0, '90B' => 0, '90AA' => 0, '90A' => 0, '85H' => 0, '85G' => 0, '85F' => 0, '85E' => 0, '85D' => 0, '85C' => 0, '85B' => 0, '85AA' => 0, '85A' => 0, '80H' => 0, '80G' => 0, '80F' => 0, '80D' => 0, '80C' => 0, '80B' => 0, '80AA' => 0, '80A' => 0, '75H' => 0, '75G' => 0, '75F' => 0, '75E' => 0, '75D' => 0, '75C' => 0, '75B' => 0, '75AA' => 0, '75A' => 0, '70H' => 0, '70G' => 0, '70F' => 0, '70E' => 0, '70D' => 0, '70C' => 0, '70B' => 0, '70AA' => 0, '70A' => 0, '48 G' => 0, '48 F' => 0, '48 E' => 0, '48 D' => 0, '46 G' => 0, '44 G' => 0, '42 G' => 0, '40 G' => 0, '38 G' => 0, '38 F' => 0, '44 A' => 0, '42 A' => 0, '40 A' => 0, '46 F' => 0, '46 E' => 0, '46 D' => 0, '46 C' => 0, '46 B' => 0, '44 F' => 0, '44 E' => 0, '44 D' => 0, '44 C' => 0, '44 B' => 0, '42 F' => 0, '42 E' => 0, '42 D' => 0, '40 F' => 0, '40 E' => 0, '40 D' => 0, '40 C' => 0, '40 B' => 0, '38 E' => 0, '38 C' => 0, '38 D' => 0, '38 B' => 0, '38 A' => 0, '50 7in' => 0, '50 9in' => 0, '48 7in' => 0, '48 9in' => 0, '46 7in' => 0, '46 9in' => 0, '44 7in' => 0, '44 9in' => 0, '42 7in' => 0, '42 9in' => 0, '40 7in' => 0, '40 9in' => 0, '38 7in' => 0, '38 9in' => 0, '36 7in' => 0, '36 9in' => 0, '34 7in' => 0, '34 9in' => 0, '32 7in' => 0, '32 9in' => 0, '30 7in' => 0, '30 9in' => 0, '10 (4XL)' => 0, '9 (3XL)' => 0, '8 (XXL)' => 0, '7 (XL)' => 0, '6 (L)' => 0, '5 (M)' => 0, '4 (S)' => 0, '3 (M)' => 0, '2 (S)' => 0, '1 (XS)' => 0, '49/50 (4XL)' => 0, '62/64 (4XL)' => 0, '58/60 (3XL)' => 0, '47/48(3XL)' => 0, '54/56 (XXL)' => 0, '45/46(XXL)' => 0, '43/44(XL)' => 0, '56/58 (XL)' => 0, '50/52 (XL)' => 0, '52/54 (L)' => 0, '46/48 (L)' => 0, '41/42(L)' => 0, '42/44 (M)' => 0, '48/50 (M)' => 0, '38/40 (S)' => 0];

        $sizes = isset($filtersContent['size']) ? $filtersContent['size'] : [];
        $newSizes = [];
        foreach ($sizes as $key => $count) {
            if (isset($sizesOrdred[strval($key)])) {
                $sizesOrdred[strval($key)] = $count;
            } else {
                $newSizes[strval($key)] = $count;
            }
        }
        $sizesOrdred = array_filter($sizesOrdred, function ($var) {
            return $var > 0;
        });
        $filtersContent['size'] = $sizesOrdred + $newSizes;

        // View Parameters
        $parameters = array_merge(
            $parameters,
            [
                'advice' => $advice, // => If size is enabled
                'aggs' => $filtersContent, // => For displaying filters
                'filters' => $filtersUser, // => Applied Filters
                'from_brand' => $fromBrand, // => If brands does not appear in the filters
                'from_search' => !is_null($search),
                'from_category' => $fromCategory,
                'from_gender' => $fromGender,
                'help' => $sessionHelp,
            ]
        );

        $cats0 = $this->getDoctrine()->getRepository('AsmodineFrontBundle:Category')->findBy(['depth' => 0, 'enabled' => true]);
        $cats1 = $this->getDoctrine()->getRepository('AsmodineFrontBundle:Category')->findBy(['depth' => 1, 'enabled' => true]);
        $cats2 = $this->getDoctrine()->getRepository('AsmodineFrontBundle:Category')->findBy(['depth' => 2, 'enabled' => true]);
        $catsGender = [];
        $catsDepth1 = [];
        $catsDepth2 = [];
        $catsDisableAdvice = [];

        /** @var Category $catGender */
        foreach ($cats0 as $catGender) {
            $catsGender[$catGender->getBackId()] = $catGender->getName();
        }
        /** @var Category $cat1 */
        foreach ($cats1 as $cat1) {
            if ('vetements' == $cat1->getSlug()) {
                $catsDepth1[$cat1->getBackId()] = $catsGender[$cat1->getBackParentId()];
            } else {
                $catsDisableAdvice[] = $cat1->getBackId();
            }
        }

        $genderFilterName = null;
        if (isset($filters['gender'][0]) && isset($catsGender[$filters['gender'][0]])) {
            $genderFilterName = $catsGender[$filters['gender'][0]];
        }

        /** @var Category $cat2 */
        foreach ($cats2 as $cat2) {
            if (isset($catsDepth1[$cat2->getBackParentId()])) {
                // Remove other gender
                if (is_null($genderFilterName) || $catsDepth1[$cat2->getBackParentId()] == $genderFilterName) {
                    $catsDepth2[$cat2->getBackId()] = $cat2->getName().' '.$catsDepth1[$cat2->getBackParentId()];
                }
            } else {
                $catsDisableAdvice[] = $cat2->getBackId();
            }
        }

        $brandsTarget = [];
        $brands = $this->getDoctrine()->getRepository('AsmodineFrontBundle:Brand')->findAll();
        foreach ($brands as $b) {
            $brandsTarget[$b->getBackId()] = $b->getIframe();
        }

        $parameters = array_merge(
            $parameters,
            [
                'brands_iframe' => $brandsTarget,
                'categories_gender' => $catsGender,
                'categories_depth2' => $catsDepth2,
            ]
        );

        $disableAdvice = false;
        if ($fromCategory) {
            if (isset($match['category_depth1_id']) && in_array($match['category_depth1_id'], $catsDisableAdvice)) {
                $disableAdvice = true;
            }
            if (isset($match['category_depth2_id']) && in_array($match['category_depth2_id'], $catsDisableAdvice)) {
                $disableAdvice = true;
            }
        }

        if ($this->getUser() && $advice && !$disableAdvice) {
            $adviceParams = $this->getModelsWithAdvice($esService, $query, $sort, $aggs, $page);

            return $this->render('AsmodineFrontBundle:Model:index.html.twig', array_merge($parameters, $adviceParams));
        }

        /** @var ESResponse $esResponse */
        $esResponse = $esService->search(ElasticsearchService::MODEL, $query, 9, $page, $sort, $aggs, null);

        return $this->render(
            'AsmodineFrontBundle:Model:index.html.twig',
            array_merge(
                $parameters,
                [
                    'models' => $esResponse->getObjects(),
                    'total' => $esResponse->getTotal(),
                    'pagination' => ['current' => $page, 'max' => intval($esResponse->getTotal() / 9) + 1],
                ]
            )
        );
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    private function getSort(Request $request): array
    {
        $order = $request->get('order', null);
        $sort = [];
        if ('price_asc' == $order) {
            $sort = ['unit_price:asc'];
        }
        if ('price_desc' == $order) {
            $sort = ['unit_price:desc'];
        }

        return $sort;
    }

    /**
     * Return filters aggregations.
     *
     * @return array
     */
    private function getAggregations(): array
    {
        return [
            'brands' => ['terms' => ['field' => 'brand_name', 'size' => 100]],
            'cat_gender' => ['terms' => ['field' => 'category_depth0_id', 'size' => 5]],
            'cat_level2' => ['terms' => ['field' => 'category_depth2_id', 'size' => 25]],
            'size' => [
                'nested' => ['path' => 'products'],
                'aggs' => ['distinct_size' => ['terms' => ['field' => 'products.size', 'size' => 2000]]],
            ],
            'color' => [
                'nested' => ['path' => 'products'],
                'aggs' => ['distinct_color' => ['terms' => ['field' => 'products.color_filter', 'size' => 25]]],
            ],
        ];
    }

    /**
     * @param array       $match
     * @param null|string $search
     * @param array|null  $filters
     *
     * @return array
     */
    private function getInitQuery(array $match, ?string $search, ?array $filters): array
    {
        // Query construct
        $query = [];

        if (count($match) > 0) {
            $query['bool']['must'] = [];
        }
        foreach ($match as $k => $v) {
            $query['bool']['must'][] = ['match' => [$k => $v]];
        }

        if (!is_null($search)) {
            $query['bool']['should'] = [];
            $query['bool']['should'][] = [
                ['match' => ['name' =>  ['query' => $search,"fuzziness"=> 1, 'boost' => 10]]],
                ['match' => ['products.color' =>  ['query' => $search,"fuzziness"=> 1, 'boost' => 3]]],
                ['match' => ['description' =>  ['query' => $search,"fuzziness"=> 1, 'boost' => 1]]]
            ];
            //$query['bool']['should'][] = ['fuzzy' => ['name' => ['value' => $search, 'fuzziness' => 'AUTO', 'boost' => 3]]];
            //$query['bool']['should'][] = ['fuzzy' => ['description' => ['value' => $search, 'fuzziness' => 'AUTO', 'boost' => 1]]];
            //$query['bool']['minimum_should_match'] = 1;
            //$query['bool']['boost'] = 1.0;
        }

        if (is_null($filters)) {
            if (0 == count($query)) {
                $query = ['match_all' => new \stdClass()];
            }

            return $query;
        }

        // Apply Filters

        // Gender
        if (isset($filters['gender'])) {
            if (1 == count($filters['gender'])) {
                $query['bool']['filter']['term']['category_depth0_id'] = $filters['gender'][0];
            } elseif (count($filters['gender']) > 1) {
                // TODO
            }
        }

        // Category
        if (isset($filters['category'])) {
            if (1 == count($filters['category'])) {
                $query['bool']['filter']['term']['category_depth2_id'] = $filters['category'][0];
            } elseif (count($filters['category']) > 1) {
                // TODO
            }
        }

        // Brands
        if (isset($filters['shop'])) {
            if (1 == count($filters['shop'])) {
                $query['bool']['filter']['term']['brand_name'] = $filters['shop'][0];
            } elseif (count($filters['shop']) > 1) {
                // TODO
            }
        }

        // Price
        if (isset($filters['price'])) {
            if (1 == count($filters['price'])) {
                $price = explode('_', $filters['price'][0]);
                $query['bool']['must'][] = ['range' => ['unit_price' => ['gte' => $price[0], 'lte' => $price[1]]]];
            } elseif (count($filters['price']) > 1) {
                //TODO
            }
        }

        if (isset($filters['color']) || isset($filters['size'])) {
            $nested = ['nested' => ['path' => 'products', 'score_mode' => 'max', 'query' => ['bool' => ['must' => []]]]];

            if (isset($filters['color'])) {
                if (1 == count($filters['color'])) {
                    $nested['nested']['query']['bool']['must'][] = ['match' => ['products.color_filter' => $filters['color'][0]]];
                } elseif (count($filters['color']) > 1) {
                    // TODO
                }
            }

            if (isset($filters['size'])) {
                if (1 == count($filters['size'])) {
                    $nested['nested']['query']['bool']['must'][] = ['match' => ['products.size' => $filters['size'][0]]];
                } elseif (count($filters['size']) > 1) {
                    // TODO
                    $nested['nested']['query']['bool']['must'][] = ['match' => ['products.size' => $filters['size'][0]]];
                }
            }
            $query['bool']['must'][] = $nested;
        }

        return $query;
    }

    /**
     * @param ElasticsearchService $esService
     * @param array                $query
     * @param array                $sort
     * @param array                $aggs
     * @param int                  $page
     *
     * @return array
     *
     * @throws \Asmodine\CommonBundle\Exception\EnumParameterException
     */
    private function getModelsWithAdvice(ElasticsearchService $esService, array $query, array $sort, array $aggs, int $page)
    {
        $initQuery = $query;
        // Get All Models Id of query
        $idsAllModels = [];
        for ($i = 1, $total = -1; count($idsAllModels) < $total || $total == -1; ++$i) {
            $esResponseAll = $esService->search(ElasticsearchService::MODEL, $query, 50000, $i, null, $aggs, ['id']);
            $modelsId = $esResponseAll->getObjects();
            $total = $esResponseAll->getTotal();
            for ($j = 0; $j < count($modelsId); ++$j) {
                $idsAllModels[$modelsId[$j]['id']] = true;
            }
        }


        //If temporary profil
        $session = $this->get('session');
        $temp=$session->get('temporary');

        // Get All order Advice of model and user;
        $must = [];
        $must[] = ['terms' => ['model_id' => array_keys($idsAllModels)]];
        if ($temp) {
            $must[] = ['term' => ['user_id' => $session->get('tempUser')]];
        } else {
            $must[] = ['term' => ['user_id' => $this->getUser()->getId()]];
        }
        $must[] = ['range' => ['note_advice' => ['gte' => 1.87]]];
        $query = ['constant_score' => ['filter' => ['bool' => ['must' => $must]]]];
        $notes = $esService->search(ElasticsearchService::ADVICE, $query, 50000, 1, ['note_ranking:desc'], null, null);
        $notesObject = $notes->getObjects();
        $noteUniqueId = [];
        for ($j = 0; $j < count($notesObject); ++$j) {
            if (!isset($noteUniqueId[$notesObject[$j]['model_id']])) {
                $noteUniqueId[$notesObject[$j]['model_id']] = $notesObject[$j];
            }
        }

        $models = [];
        $total = count($noteUniqueId);
        if ($sort == []) {
            $noteModel = array_slice($noteUniqueId, 9 * ($page - 1), 9, true);
            $noteModelId = [];
            foreach ($noteModel as $a => $d) {
                $noteModelId[] = isset($d['model_id']) ? $d['model_id'] : $d;
            }
            $query = ['constant_score' => ['filter' => ['terms' => ['id' => $noteModelId]]]];
            /** @var ESResponse $esResponse */
            $esResponse = $esService->search(ElasticsearchService::MODEL, $query, 75000, 1, null, null, null);
            $mdisplay = $esResponse->getObjects();
            for ($k = 0; $k < count($noteModelId); ++$k) {
                for ($l = 0; $l < count($mdisplay); ++$l) {
                    if ($mdisplay[$l]['id'] == $noteModelId[$k]) {
                        if (isset($noteUniqueId[$mdisplay[$l]['id']])) {
                            $models[] = array_merge($mdisplay[$l], $noteUniqueId[$mdisplay[$l]['id']]);
                        } else {
                            $models[] = $mdisplay[$l];
                        }
                    }
                }
            }
        } else {
            $esResponse = $esService->search(ElasticsearchService::MODEL, $initQuery, 9, $page, $sort, $aggs, null, null);
            $mdisplay = $esResponse->getObjects();
            for ($k = 0; $k < count($mdisplay); ++$k) {
                if (isset($noteUniqueId[$mdisplay[$k]['id']])) {
                    $models[] = array_merge($mdisplay[$k], $noteUniqueId[$mdisplay[$k]['id']]);
                } else {
                    $models[] = $mdisplay[$k];
                }
            }
        }

        return [
            'models' => $models,
            'total' => $total,
            'pagination' => ['current' => $page, 'max' => intval($total / 9) + 1],
        ];
    }
}
