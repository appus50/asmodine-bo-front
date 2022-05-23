<?php

namespace Asmodine\FrontBundle\Controller;

use Asmodine\CommonBundle\Controller\Controller as AsmodineController;
use Asmodine\CommonBundle\Service\ElasticsearchService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class AsmotailleController.
 *
 * @Route("/asmotaille")
 */
class AsmotailleController extends AsmodineController
{
    /**
     * @Route("/page/{idModel}/{idProduct}/{idRecommand}", name="asmodinefront_asmotaille_index")
     * @Method({"GET"})
     * @Template
     *
     * @param string $idModel
     * @param string $idProduct
     * @param string $idRecommand
     *
     * @return array
     *
     * @throws \Asmodine\CommonBundle\Exception\EnumParameterException
     */
    public function indexAction(string $idModel, string $idProduct = '0', string $idRecommand = '0')
    {
        /** @var ElasticsearchService $esService */
        $esService = $this->get('asmodine.common.elasticsearch');

        $hit = $esService->get(ElasticsearchService::MODEL, $idModel);
        $products = $hit['_source']['products'];
        $sizeReco = '0' == $idRecommand ? $idProduct : $idRecommand;
        for ($i = 0; $i < count($products); ++$i) {
            if ($products[$i]['id'] == $sizeReco) {
                $size = $this->getTranslator()->trans('asmotaille.recommend').' '.$products[$i]['size'];

                return ['iframe_src' => $products[$i]['url'], 'size' => $size, 'brand' => $hit['_source']['brand_name']];
            }
        }

        return [
            'iframe_src' => $products[0]['url'],
            'size' => $this->getTranslator()->trans('asmotaille.no_size'),
            'brand' => $hit['_source']['brand_name'],
        ];
    }

    /**
     * @Route("/revenir", name="asmodinefront_asmotaille_back")
     */
    public function redirectAction()
    {
        /** @var Session $session */
        $session = $this->get('session');
        $from = $session->get('asmotaille_from', null);
        if (!is_null($from)) {
            return $this->redirect($from);
        }

        return $this->redirectToRoute('asmodinefront_main_home');
    }
}
