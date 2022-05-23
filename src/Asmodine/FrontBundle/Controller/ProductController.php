<?php

namespace Asmodine\FrontBundle\Controller;

use Asmodine\CommonBundle\Service\ElasticsearchService;
use Asmodine\FrontBundle\Entity\Brand;
use Asmodine\FrontBundle\Entity\Category;
use Asmodine\FrontBundle\Entity\PhysicalProfile;
use Asmodine\FrontBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class ProductsController.
 *
 * @Route("/les-produits")
 */
class ProductController extends AbstractController
{
    /** @var ElasticsearchService */
    private $elasticService;

    /** @var User */
    private $user;

    /** @var PhysicalProfile */
    private $physicalProfile;

    /** @var Brand */
    private $brand;

    /** @var array */
    private $model;

    /** @var string */
    private $productId;

    /** @var array */
    private $notes;

    /** @var string */
    private $currentSize;

    /** @var array */
    private $products;

    /** @var bool */
    private $help;

    /**
     * @Route("/{id}/{productid}/{best}", name="asmodinefront_products_model", defaults={"productid" = "", "best"= "0"})
     * @Template
     *
     * @param Request $request
     * @param string  $id
     * @param string  $productid
     *
     * @return array|Response
     *
     * @throws \Asmodine\CommonBundle\Exception\EnumParameterException
     * @throws \Asmodine\CommonBundle\Exception\NullException
     */
    public function indexAction(Request $request, string $id, string $productid)
    {
        $init = $this->init($request, $id, $productid);
        $fromPage = !is_null($request->query->get('from_page', null))
            ? $request->query->get('from_page', null)
            : $this->generateUrl('asmodinefront_products_model', ['id' => $id, 'productid' => $productid, 'best' => $request->get('best', '0')], UrlGeneratorInterface::ABSOLUTE_URL);
        $fromPage = urlencode($fromPage);
        if (true !== $init) {
            return $this->redirectToRoute('asmodinefront_page_redirection_brand', ['id' => $this->brand->getId(), 'redirect' => urlencode($init), 'from' => $fromPage]);
        }
        $this->initHelp($request);
        $parameters = $this->initParameters();
        $sizes = $this->checkSizes();

        // Redirect if get best
        $best = '1' == $request->get('best', false);
        if ($best && $sizes['recommanded_size'] != $sizes['current_size'] && isset($sizes['sizes'][$sizes['recommanded_size']])) {
            return $this->redirect($sizes['sizes'][$sizes['recommanded_size']]);
        }

        return array_merge($parameters, $sizes, ['from_page' => $fromPage]);
    }

    /**
     * @param $modelId
     * @param $productId
     *
     * @return array|bool
     *
     * @throws \Asmodine\CommonBundle\Exception\EnumParameterException
     */
    private function init(Request $request, $modelId, $productId)
    {
        $this->elasticService = $this->getElasticsearchService();

        $session=$request->getSession();
        $temp=$session->get('temporary');

        if ($temp) {
            $this->user = $this->getManager()->getRepository('AsmodineFrontBundle:User')->findOneBy(['id' => $request->getSession()->get('tempUser')]);
        } else {
            $this->user = $this->getUser();
        }

        $this->productId = $productId;

        $model = $this->elasticService->get(ElasticsearchService::MODEL, $modelId);
        $this->model = $model['_source'];
        $this->brand = $this->getDoctrine()->getRepository('AsmodineFrontBundle:Brand')->findOneBy(['backId' => $this->model['brand_id']]);
        if (is_null($this->user) && 'voir' == $this->productId) {
            $this->products = $this->model['products'];
            $this->notes = null;
            $this->physicalProfile = null;

            return true;
        }

        if (is_null($this->user) || 0 == strlen($this->productId) || 'voir' == $this->productId) {
            if ($this->brand->getIframe()) {
                return $this->generateUrl('asmodinefront_asmotaille_index', ['idModel' => $this->model['id'], 'idProduct' => '0', 'idRecommand' => '0']);
            }

            return $this->model['products'][0]['url'];
        }

        $this->products = $this->model['products'];
        $this->notes = $this->elasticService->get(ElasticsearchService::ADVICE, 'u'.$this->user->getId().'p'.$this->productId);

        if ($temp) {
            $this->physicalProfile = $this->getManager()->getRepository('AsmodineFrontBundle:PhysicalProfile')->findOneBy(['user' => $this->user, 'current'=> true]);
        } else {
            $this->physicalProfile = $this->getCurrentPhysicalProfile();
        }


        return true;
    }

    /**
     * @param Request $request
     */
    private function initHelp(Request $request)
    {
        $sessionHelp = $request->getSession()->get('sg_help', false);
        $urlHelp = $request->get('help', null);
        if (!is_null($urlHelp)) {
            $sessionHelp = in_array(strtolower($urlHelp), ['oui', '1', 'true', 'help', 'valid']);
        }
        $request->getSession()->set('sg_help', $sessionHelp);
        $this->help = $sessionHelp;
    }

    /**
     * @return array
     *
     * @throws \Asmodine\CommonBundle\Exception\EnumParameterException
     * @throws \Asmodine\CommonBundle\Exception\NullException
     */
    private function checkSizes(): array
    {
        $sizesInfos = [];
        $sizesGuide = [];
        $sizesColor = [];
        $currentProduct = null;
        $bestSize = false;
        $bestSizeId = '0';
        $bestDiff = null;
        foreach ($this->products as $product) {
            if ($product['id'] == $this->productId || ('voir' == $this->productId && is_null($currentProduct))) {
                $currentProduct = $product;
            }
            $sizesInfos[$product['size']] = $this->generateUrl('asmodinefront_products_model', ['id' => $this->model['id'], 'productid' => $product['id']]);
            $sizeGuide = $this->elasticService->search(ElasticsearchService::SIZE_GUIDE, ['term' => ['product_id' => $product['id']]], 100, 1, [], null, null)->getObjects();

            $sizesColor[$product['size']] = 'none';

            // Find Best
            for ($i = 0; $i < count($sizeGuide) && !is_null($this->physicalProfile); ++$i) {
                $sg = $sizeGuide[$i];
                $user = 10 * $this->physicalProfile->get($sg['body_part']);
                $min = $sg['min'];
                $max = $sg['max'];

                $diff = '';
                $class = '';
                if (is_null($user)) {
                    $diff = '';
                    $class = '';
                } elseif ($user >= $min && $user <= $max) {
                    $diff = 0;
                    $class = 'success';
                    $sizesColor[$product['size']] = $this->checkColor($sizesColor[$product['size']], 'green');
                } elseif ($user < $min) {
                    $diff = $min - $user;
                    if ($diff > 70) {
                        $class = 'danger';
                        $sizesColor[$product['size']] = $this->checkColor($sizesColor[$product['size']], 'red');
                    } elseif ($diff > 30) {
                        $class = 'warning';
                        $sizesColor[$product['size']] = $this->checkColor($sizesColor[$product['size']], 'orange');
                    } else {
                        $class = 'info';
                        $sizesColor[$product['size']] = $this->checkColor($sizesColor[$product['size']], 'yellow');
                    }
                } elseif ($user > $max) {
                    $diff = $max - $user;
                    if ($diff < -30) {
                        $class = 'danger';
                        $sizesColor[$product['size']] = $this->checkColor($sizesColor[$product['size']], 'red');
                    } else {
                        $class = 'info';
                        $sizesColor[$product['size']] = $this->checkColor($sizesColor[$product['size']], 'yellow');
                    }
                }
                $sizeGuide[$i]['user'] = $user;
                $sizeGuide[$i]['diff'] = $diff;
                $sizeGuide[$i]['class'] = $class;
            }
            $sizesGuide[$product['size']] = $sizeGuide;
            $nullOrError = false;
            $diff = 0;
            for ($i = 0; $i < count($sizeGuide) && !is_null($this->physicalProfile); ++$i) {
                $diff += abs($sizeGuide[$i]['diff']);
                $nullOrError = $nullOrError || in_array($sizeGuide[$i]['class'], ['', 'danger']);
            }
            if (0 == count($sizeGuide)) {
                $nullOrError = true;
            }
            if (!$nullOrError && (is_null($bestDiff) || $bestDiff > $diff)) {
                $bestDiff = $diff;
                $bestSize = $product['size'];
                $bestSizeId = $product['id'];
            }
        }

        if (!is_null($this->physicalProfile)) {
            $sizeAdvices = $this->getCurrentSizeAdvice($sizesGuide[$currentProduct['size']]);
        } else {
            $sizeAdvices = null;
        }

        $sizesInfos = $this->orderedSize($sizesInfos);

        return
            [
                'sizes' => $sizesInfos,
                'current_size' => $currentProduct['size'],
                'product' => $currentProduct,
                'advices_size' => $sizeAdvices,
                'help_sg' => $sizesGuide,
                'has_size_guide' => !is_null($this->physicalProfile) ? (count($sizesGuide[$currentProduct['size']]) > 0) : false,
                'recommanded_size' => $bestSize,
                'recommanded_size_id' => $bestSizeId,
                'seo_view' => 'voir' == $this->productId,
                'products_size_color' => $sizesColor,
            ];
    }

    /**
     * Check size color.
     *
     * @param string $init
     * @param string $current
     *
     * @return string
     */
    private function checkColor(string $init, string $current): string
    {
        if ('red' == $current) {
            return 'red';
        }
        if ('orange' == $current && 'red' != $init) {
            return 'orange';
        }
        if ('yellow' == $current && !in_array($init, ['red', 'orange'])) {
            return 'yellow';
        }
        if ('green' == $current && !in_array($init, ['red', 'orange', 'yellow'])) {
            return 'green';
        }

        return $init;
    }

    /**
     * @param $sizeGuide
     *
     * @return array
     *
     * @throws \Asmodine\CommonBundle\Exception\NullException
     */
    private function getCurrentSizeAdvice($sizeGuide): array
    {
        $sizeAdvices = [];
        foreach ($sizeGuide as $sgDatas) {
            $diff = 0;
            $userSize = $this->physicalProfile->get($sgDatas['body_part']);
            if (is_null($userSize)) {
                $message = 'unknown';
            } elseif (10 * $userSize > $sgDatas['max']) {
                $message = in_array($sgDatas['body_part'], ['arm', 'inside_leg']) ? 'smaller2_than' : 'smaller_than';
                $diff = (10 * $userSize) - $sgDatas['max'];
            } elseif (10 * $userSize < $sgDatas['min']) {
                $message = in_array($sgDatas['body_part'], ['arm', 'inside_leg']) ? 'greater2_than' : 'greater_than';
                $diff = $sgDatas['min'] - (10 * $userSize);
            } else {
                $message = 'perfect';
                $diff = 0;
            }
            $bodyPart = $this->getTranslator()->trans('size.body_part.'.$sgDatas['body_part']);
            $sizeAdvices[] = $this->getTranslator()->trans('size.advice.'.$message, ['%body_part%' => $bodyPart, '%diff%' => round(1.0 * $diff / 10, 1)]);
        }

        return $sizeAdvices;
    }

    /**
     * @return array
     *
     * @throws \Asmodine\CommonBundle\Exception\EnumParameterException
     */
    private function initParameters(): array
    {
        /** @var Category $category */
        $category = $this->getDoctrine()
            ->getRepository('AsmodineFrontBundle:Category')
            ->findOneBy(['backId' => $this->model['category_depth2_id']]);
        $breadcrumb = $this->getBreadcrumbCategory($category);

        $imagesM = $this->elasticService->search(ElasticsearchService::IMAGE, ['term' => ['search_id' => 'model_'.$this->model['id']]], 100, 1, ['position:asc'], null, null)->getObjects();
        $imagesP = $this->elasticService->search(ElasticsearchService::IMAGE, ['term' => ['search_id' => 'product_'.$this->productId]], 100, 1, ['position:asc'], null, null)->getObjects();
        $images = array_merge(array_values($imagesM), array_values($imagesP));

        return [
            'model' => $this->model,
            'breadcrumb' => $breadcrumb,
            'category' => $category,
            'images' => $images,
            'brand' => $this->brand,
            'notes' => $this->notes,
            'style_note' => $this->notes['_source']['note_style'],
            'color_note' => $this->notes['_source']['note_color'],
            'grade_product' => $this->notes['_source']['note_advice'],
            'current_size' => $this->currentSize,
            'help' => $this->help,
        ];
    }

    /**
     * Desc sizes...
     *
     * @param $sizes
     *
     * @return array
     */
    private function orderedSize($sizes)
    {
        $sizesOrdred = ['12XL' => 0, '11XL' => 0, '10XL' => 0, '9XL' => 0, '8XL' => 0, '7XL' => 0, '6XL' => 0, '5XL' => 0, '4XL' => 0, '3XL' => 0, '2XL' => 0, '3XS' => 0, 'XXXXXXXXXXXXL' => 0, 'XXXXXXXXXXXL' => 0, 'XXXXXXXXXXL' => 0, 'XXXXXXXXL' => 0, 'XXXXXXXL' => 0, 'XXXXXXL' => 0, 'XXXXXL' => 0, 'XXXXL' => 0, 'XXXL' => 0, 'XXL' => 0, 'XL/XXL' => 0, 'XL' => 0, 'L/XL' => 0, 'L' => 0, 'M/L' => 0, 'M' => 0, 'S/M' => 0, 'S' => 0, 'XS/S' => 0, 'XS' => 0, 'XXS/XS' => 0, 'XXS' => 0, 'XXXS' => 0, 'XXXXXXXXXXXXLL' => 0, 'XXXXXXXXXXXLL' => 0, 'XXXXXXXXXXLL' => 0, 'XXXXXXXXLL' => 0, 'XXXXXXXLL' => 0, 'XXXXXXLL' => 0, 'XXXXXLL' => 0, 'XXXXLL' => 0, 'XXXLL' => 0, 'XXLL' => 0, 'XLL' => 0, 'LL' => 0, 'ML' => 0, 'SL' => 0, 'XSL' => 0, 'XXSL' => 0, 'XXXSL' => 0, ':60' => 0, ':59' => 0, ':58' => 0, ':57' => 0, ':56' => 0, ':55' => 0, ':54' => 0, ':53' => 0, ':52' => 0, ':51' => 0, ':50' => 0, ':49' => 0, ':48' => 0, ':47' => 0, ':46' => 0, ':45' => 0, ':44' => 0, ':43' => 0, ':42' => 0, ':41' => 0, ':40' => 0, ':39' => 0, ':38' => 0, ':37' => 0, ':36' => 0, ':35' => 0, ':34' => 0, ':33' => 0, ':32' => 0, ':31' => 0, ':30' => 0, ':29' => 0, ':28' => 0, ':27' => 0, ':26' => 0, ':25' => 0, ':24' => 0, ':23' => 0, ':22' => 0, ':21' => 0, ':20' => 0, ':19' => 0, ':18' => 0, ':17' => 0, ':16' => 0, ':15' => 0, ':14' => 0, ':13' => 0, ':12' => 0, ':11' => 0, ':10' => 0, ':9' => 0, ':8' => 0, ':7' => 0, ':6' => 0, ':5' => 0, ':4' => 0, ':3' => 0, ':2' => 0, ':1' => 0, ':0' => 0, '70' => 0, '69' => 0, '68' => 0, '67' => 0, '66' => 0, '65' => 0, '64' => 0, '63' => 0, '62' => 0, '61' => 0, '60' => 0, '59' => 0, '58' => 0, '57' => 0, '56' => 0, '55' => 0, '54' => 0, '52' => 0, '51' => 0, '50.5' => 0, '50' => 0, '49.5' => 0, '49' => 0, '48.5' => 0, '48' => 0, '47.5' => 0, '47' => 0, '46.5' => 0, '46' => 0, '45.5' => 0, '45' => 0, '44.5' => 0, '44' => 0, '43' => 0, '42.5' => 0, '42' => 0, '41.5' => 0, '41' => 0, '40.5' => 0, '40' => 0, '39.5' => 0, '39' => 0, '38' => 0, '37.5' => 0, '37' => 0, '36.5' => 0, '36' => 0, '35' => 0, '34' => 0, '33' => 0, '32' => 0, '31' => 0, '30' => 0, '29' => 0, '28' => 0, '27' => 0, '26' => 0, '25' => 0, '24' => 0, '23' => 0, '22' => 0, '21' => 0, '20' => 0, '19' => 0, '18' => 0, '17' => 0, '16' => 0, '15' => 0, '14' => 0, '13' => 0, '12' => 0, '11' => 0, '10' => 0, '9' => 0, '8' => 0, '7' => 0, '6' => 0, '5' => 0, '4' => 0, '3' => 0, '2' => 0, '1' => 0, '0' => 0, '150/200' => 0, '140/200' => 0, '128/134' => 0, '116/122' => 0, '104/110' => 0, '58/60' => 0, '54/56' => 0, '50/52' => 0, '46/44' => 0, '46/42' => 0, '46/40' => 0, '46/38' => 0, '46/36' => 0, '46/34' => 0, '46/33' => 0, '46/32' => 0, '46/31' => 0, '46/30' => 0, '44/44' => 0, '44/42' => 0, '44/40' => 0, '44/38' => 0, '44/36' => 0, '44/34' => 0, '44/33' => 0, '44/32' => 0, '44/31' => 0, '44/30' => 0, '42/44' => 0, '42/42' => 0, '42/40' => 0, '42/38' => 0, '42/36' => 0, '42/34' => 0, '42/33' => 0, '42/32' => 0, '42/31' => 0, '42/30' => 0, '40/44' => 0, '40/42' => 0, '40/40' => 0, '40/38' => 0, '40/36' => 0, '40/34' => 0, '40/33' => 0, '40/32' => 0, '40/30' => 0, '39/44' => 0, '39/42' => 0, '39/40' => 0, '39/36' => 0, '39/34' => 0, '39/32' => 0, '39/31' => 0, '39/30' => 0, '38/44' => 0, '38/42' => 0, '38/40' => 0, '38/38' => 0, '38/36' => 0, '38/34' => 0, '38/33' => 0, '38/32' => 0, '38/31' => 0, '38/30' => 0, '37/44' => 0, '37/42' => 0, '37/40' => 0, '37/38' => 0, '37/36' => 0, '37/34' => 0, '37/33' => 0, '37/32' => 0, '37/31' => 0, '37/30' => 0, '36/44' => 0, '36/42' => 0, '36/40' => 0, '36/38' => 0, '36/36' => 0, '36/34' => 0, '36/33' => 0, '36/32' => 0, '36/31' => 0, '36/30' => 0, '35/44' => 0, '35/42' => 0, '35/40' => 0, '35/38' => 0, '35/36' => 0, '35/34' => 0, '35/33' => 0, '35/32' => 0, '35/31' => 0, '35/30' => 0, '34/44' => 0, '34/42' => 0, '34/40' => 0, '34/38' => 0, '34/36' => 0, '34/34' => 0, '34/33' => 0, '34/32' => 0, '34/31' => 0, '34/30' => 0, '33/44' => 0, '33/42' => 0, '33/40' => 0, '33/38' => 0, '33/36' => 0, '33/34' => 0, '33/33' => 0, '33/32' => 0, '33/31' => 0, '33/30' => 0, '32/44' => 0, '32/42' => 0, '32/40' => 0, '32/38' => 0, '32/36' => 0, '32/34' => 0, '32/32' => 0, '32/31' => 0, '32/30' => 0, '31/44' => 0, '31/42' => 0, '31/40' => 0, '31/38' => 0, '31/36' => 0, '31/34' => 0, '31/33' => 0, '31/32' => 0, '31/31' => 0, '31/30' => 0, '30/44' => 0, '30/42' => 0, '30/40' => 0, '30/38' => 0, '30/36' => 0, '30/34' => 0, '30/33' => 0, '30/32' => 0, '30/30' => 0, '29/44' => 0, '29/42' => 0, '29/40' => 0, '29/38' => 0, '29/36' => 0, '29/34' => 0, '29/33' => 0, '29/32' => 0, '29/31' => 0, '29/30' => 0, '28/40' => 0, '28/42' => 0, '28/38' => 0, '28/36' => 0, '28/34' => 0, '28/33' => 0, '28/32' => 0, '28/31' => 0, '28/30' => 0, '27/44' => 0, '27/42' => 0, '27/40' => 0, '27/38' => 0, '27/36' => 0, '27/34' => 0, '27/33' => 0, '27/32' => 0, '27/31' => 0, '27/30' => 0, '26/44' => 0, '26/42' => 0, '26/40' => 0, '26/38' => 0, '26/36' => 0, '26/34' => 0, '26/33' => 0, '26/32' => 0, '26/31' => 0, '26/30' => 0, '25/44' => 0, '25/42' => 0, '25/40' => 0, '25/38' => 0, '25/36' => 0, '25/34' => 0, '25/33' => 0, '25/32' => 0, '25/31' => 0, '25/30' => 0, '24/44' => 0, '24/42' => 0, '24/40' => 0, '24/38' => 0, '24/36' => 0, '24/34' => 0, '24/33' => 0, '24/32' => 0, '24/31' => 0, '54extra short' => 0, '54 short' => 0, '54 regular' => 0, '54 long' => 0, '54 extra long ' => 0, '54 extra extra long' => 0, '52 extra short' => 0, '52 short' => 0, '52 regular' => 0, '52 long' => 0, '52 extra long ' => 0, '52 extra extra long' => 0, '50 extra short' => 0, '50 short' => 0, '50 regular' => 0, '50 long' => 0, '50 extra long ' => 0, '50 extra extra long' => 0, '48 extra short' => 0, '48 short' => 0, '48 regular' => 0, '48 long' => 0, '48 extra long ' => 0, '48 extra extra long' => 0, '46 extra short' => 0, '46 short' => 0, '46 regular' => 0, '46 long' => 0, '46 extra long ' => 0, '46 extra extra long' => 0, '44 extra short' => 0, '44 short' => 0, '44 regular' => 0, '44 long' => 0, '44 extra long ' => 0, '44 extra extra long' => 0, '42 extra short' => 0, '42 short' => 0, '42 regular' => 0, '42 long' => 0, '42 extra long ' => 0, '42 extra extra long' => 0, '40 extra short' => 0, '40 short' => 0, '40 regular' => 0, '40 long' => 0, '40 extra long ' => 0, '40 extra extra long' => 0, '38 extra short' => 0, '38 short' => 0, '38 regular' => 0, '38 long' => 0, '38 extra long ' => 0, '38 extra extra long' => 0, '36 extra short' => 0, '36 short' => 0, '36 regular' => 0, '36 long' => 0, '36 extra long ' => 0, '36 extra extra long' => 0, '34 extra short' => 0, '34 short' => 0, '34 regular' => 0, '34 long' => 0, '34 extra long ' => 0, '34 extra extra long' => 0, '32 regular' => 0, '32 long' => 0, '32 extra long ' => 0, '32 extra extra long' => 0, '30 extra short' => 0, '30 short' => 0, '30 regular' => 0, '30 long' => 0, '30 extra long ' => 0, '30 extra extra long' => 0, '28 extra short' => 0, '28 short' => 0, '28 regular' => 0, '28 long' => 0, '28 extra long ' => 0, '28 extra extra long' => 0, '27 extra short' => 0, '27 short' => 0, '27 regular' => 0, '27 long' => 0, '27 extra long ' => 0, '27 extra extra long' => 0, '48 36' => 0, '48 34' => 0, '48 32' => 0, '48 30' => 0, '48 28' => 0, '46 36' => 0, '46 34' => 0, '46 32' => 0, '46 30' => 0, '46 28' => 0, '44 36' => 0, '44 34' => 0, '44 32' => 0, '44 30' => 0, '44 28' => 0, '42 36' => 0, '42 34' => 0, '42 32' => 0, '42 30' => 0, '42 28' => 0, '40 36' => 0, '40 34' => 0, '40 32' => 0, '40 30' => 0, '40 28' => 0, '38 36' => 0, '38 34' => 0, '38 32' => 0, '38 30' => 0, '38 28' => 0, '36 36' => 0, '36 34' => 0, '36 32' => 0, '36 30' => 0, '36 28' => 0, '34 36' => 0, '34 34' => 0, '34 32' => 0, '34 30' => 0, '34 28' => 0, '32 34' => 0, '32 32' => 0, '32 30' => 0, '32 28' => 0, '30 34' => 0, '30 32' => 0, '30 30' => 0, '30 28' => 0, '28 34' => 0, '28 32' => 0, '28 30' => 0, '28 28' => 0, '26 34' => 0, '26 32' => 0, '26 30' => 0, '26 28' => 0, 'EU 48' => 0, 'EU 47' => 0, 'EU 46' => 0, 'EU 45' => 0, 'EU 44' => 0, 'EU 44.5' => 0, 'EU 43' => 0, 'EU 42.5' => 0, 'EU 42' => 0, 'EU 41' => 0, 'EU 40.5' => 0, 'EU 40' => 0, 'EU 39' => 0, 'EU 38' => 0, 'EU 37' => 0, 'EU 36' => 0, 'EU 35.5' => 0, 'EU 35' => 0, 'UK 48 S FR 58S' => 0, 'UK 48 R FR 58R' => 0, 'UK 48 L FR 58L' => 0, 'UK 46 S FR 56S' => 0, 'UK 46 R FR 56R' => 0, 'UK 46 L FR 56L' => 0, 'UK 44 S FR 54S' => 0, 'UK 44 R FR 54R' => 0, 'UK 44 L FR 54L' => 0, 'UK 42 S FR 52S' => 0, 'UK 42 R FR 52R' => 0, 'UK 42 L FR 52L' => 0, 'UK 40 S FR 50S' => 0, 'UK 40 R FR 50R' => 0, 'UK 40 L FR 50L' => 0, 'UK 38 S FR 48S' => 0, 'UK 38 R FR 48R' => 0, 'UK 38 L FR 48L' => 0, 'UK 36 S FR 46S' => 0, 'UK 36 R FR 46R' => 0, 'UK 36 L FR 46L' => 0, 'UK 34 R FR 44R' => 0, 'UK 34 S FR 44S' => 0, 'UK 34 L FR 44L' => 0, 'UK 32 R FR 42R' => 0, 'UK 32 S FR 42S' => 0, 'UK 32 L FR 42L' => 0, 'UK 30 R FR 40R' => 0, 'UK 30 L FR 40L' => 0, 'UK 30 S FR 40S' => 0, 'UK 28 R FR 38R' => 0, 'UK 28 S FR 38S' => 0, 'UK 28 L FR 38L' => 0, 'UK 26 R FR 36R' => 0, 'UK 26 S FR 36S' => 0, 'UK 26 L FR 36L' => 0, 'W46' => 0, 'W44' => 0, 'W42' => 0, 'W40' => 0, 'W38' => 0, 'W36' => 0, 'W34' => 0, 'W32' => 0, 'W30' => 0, 'W28' => 0, 'W26' => 0, 'W48 L34' => 0, 'W48 L32' => 0, 'W48 L30' => 0, 'W46 L34' => 0, 'W46 L32' => 0, 'W46 L30' => 0, 'W44 L40' => 0, 'W44 L38' => 0, 'W44 L36' => 0, 'W44 L34' => 0, 'W44 L32' => 0, 'W44 L30' => 0, 'W42 L40' => 0, 'W42 L38' => 0, 'W42 L36' => 0, 'W42 L34' => 0, 'W42 L32' => 0, 'W42 L30' => 0, 'W40 L40' => 0, 'W40 L38' => 0, 'W40 L36' => 0, 'W40 L34' => 0, 'W40 L32' => 0, 'W40 L30' => 0, 'W38 L40' => 0, 'W38 L38' => 0, 'W38 L36' => 0, 'W38 L34' => 0, 'W38 L32' => 0, 'W38 L30' => 0, 'W36 L40' => 0, 'W36 L38' => 0, 'W36 L36' => 0, 'W36 L34' => 0, 'W36 L32' => 0, 'W36 L30' => 0, 'W35 L34' => 0, 'W35 L30' => 0, 'W34 L40' => 0, 'W34 L38' => 0, 'W34 L36' => 0, 'W34 L34' => 0, 'W34 L32' => 0, 'W34 L30' => 0, 'W34 L29' => 0, 'W34 L28' => 0, 'W33 L34' => 0, 'W33 L32' => 0, 'W33 L30' => 0, 'W32 L36' => 0, 'W32 L34' => 0, 'W32 L32' => 0, 'W32 L30' => 0, 'W32 L29' => 0, 'W32 L28' => 0, 'W32 L26' => 0, 'W31 L34' => 0, 'W31 L32' => 0, 'W31 L30' => 0, 'W30 L38' => 0, 'W30 L36' => 0, 'W30 L34' => 0, 'W30 L32' => 0, 'W30 L30' => 0, 'W30 L28' => 0, 'W29 L34' => 0, 'W29 L32' => 0, 'W29 L30' => 0, 'W28 L38' => 0, 'W28 L36' => 0, 'W28 L34' => 0, 'W28 L32' => 0, 'W28 L30' => 0, 'W28 L28' => 0, 'W28 L26' => 0, 'W26 L38' => 0, 'W26 L36' => 0, 'W26 L34' => 0, 'W26 L32' => 0, 'W26 L30' => 0, 'W26 L28' => 0, 'W26 L26' => 0, '48R' => 0, '46R' => 0, '44R' => 0, '44S' => 0, '42R' => 0, '42S' => 0, '40R' => 0, '40S' => 0, '38L' => 0, '38R' => 0, '38S' => 0, '36L' => 0, '36R' => 0, '36S' => 0, '34L' => 0, '34R' => 0, '34S' => 0, '32L' => 0, '32R' => 0, '32S' => 0, '16L' => 0, '16R' => 0, '16S' => 0, '14L' => 0, '14R' => 0, '14S' => 0, '12L' => 0, '12R' => 0, '12S' => 0, '10L' => 0, '10R' => 0, '10S' => 0, '8L' => 0, '8R' => 0, '8S' => 0, '6L' => 0, '6R' => 0, '6S' => 0, '4L' => 0, '4R' => 0, '4S' => 0, '120G' => 0, '120F' => 0, '120E' => 0, '120D' => 0, '120C' => 0, '115H ' => 0, '115G' => 0, '115F' => 0, '115E' => 0, '115D' => 0, '115C ' => 0, '115B' => 0, '115AA' => 0, '115A' => 0, '110H' => 0, '110G' => 0, '110F' => 0, '110E' => 0, '110D' => 0, '110C' => 0, '110B' => 0, '110AA' => 0, '110A' => 0, '105H' => 0, '105G' => 0, '105F' => 0, '105E' => 0, '105D' => 0, '105C' => 0, '105B' => 0, '105AA' => 0, '105A' => 0, '100H' => 0, '100G' => 0, '100F' => 0, '100E' => 0, '100D' => 0, '100C' => 0, '100B ' => 0, '100AA' => 0, '100A' => 0, '95H' => 0, '95G' => 0, '95F' => 0, '95E' => 0, '95D' => 0, '95C' => 0, '95B' => 0, '95AA' => 0, '95A' => 0, '90H' => 0, '90G' => 0, '90F' => 0, ' 90E' => 0, '90D' => 0, '90C' => 0, '90B' => 0, '90AA' => 0, '90A' => 0, '85H' => 0, '85G' => 0, '85F' => 0, '85E' => 0, '85D' => 0, '85C' => 0, '85B' => 0, '85AA' => 0, '85A' => 0, '80H' => 0, '80G' => 0, '80F' => 0, '80D' => 0, '80C' => 0, '80B' => 0, '80AA' => 0, '80A' => 0, '75H' => 0, '75G' => 0, '75F' => 0, '75E' => 0, '75D' => 0, '75C' => 0, '75B' => 0, '75AA' => 0, '75A' => 0, '70H' => 0, '70G' => 0, '70F' => 0, '70E' => 0, '70D' => 0, '70C' => 0, '70B' => 0, '70AA' => 0, '70A' => 0, '48 G' => 0, '48 F' => 0, '48 E' => 0, '48 D' => 0, '46 G' => 0, '44 G' => 0, '42 G' => 0, '40 G' => 0, '38 G' => 0, '38 F' => 0, '44 A' => 0, '42 A' => 0, '40 A' => 0, '46 F' => 0, '46 E' => 0, '46 D' => 0, '46 C' => 0, '46 B' => 0, '44 F' => 0, '44 E' => 0, '44 D' => 0, '44 C' => 0, '44 B' => 0, '42 F' => 0, '42 E' => 0, '42 D' => 0, '40 F' => 0, '40 E' => 0, '40 D' => 0, '40 C' => 0, '40 B' => 0, '38 E' => 0, '38 C' => 0, '38 D' => 0, '38 B' => 0, '38 A' => 0, '50 7in' => 0, '50 9in' => 0, '48 7in' => 0, '48 9in' => 0, '46 7in' => 0, '46 9in' => 0, '44 7in' => 0, '44 9in' => 0, '42 7in' => 0, '42 9in' => 0, '40 7in' => 0, '40 9in' => 0, '38 7in' => 0, '38 9in' => 0, '36 7in' => 0, '36 9in' => 0, '34 7in' => 0, '34 9in' => 0, '32 7in' => 0, '32 9in' => 0, '30 7in' => 0, '30 9in' => 0, '10 (4XL)' => 0, '9 (3XL)' => 0, '8 (XXL)' => 0, '7 (XL)' => 0, '6 (L)' => 0, '5 (M)' => 0, '4 (S)' => 0, '3 (M)' => 0, '2 (S)' => 0, '1 (XS)' => 0, '49/50 (4XL)' => 0, '62/64 (4XL)' => 0, '58/60 (3XL)' => 0, '47/48(3XL)' => 0, '54/56 (XXL)' => 0, '45/46(XXL)' => 0, '43/44(XL)' => 0, '56/58 (XL)' => 0, '50/52 (XL)' => 0, '52/54 (L)' => 0, '46/48 (L)' => 0, '41/42(L)' => 0, '42/44 (M)' => 0, '48/50 (M)' => 0, '38/40 (S)' => 0];
        $results = [];

        foreach ($sizesOrdred as $s => $null) {
            if (isset($sizes[$s])) {
                array_push($results, [$s => $sizes[$s]]);
                unset($sizes[$s]);
            }
        }
        foreach ($sizes as $s => $link) {
            array_push($results, [$s => $link]);
        }

        return $results;
    }
}
