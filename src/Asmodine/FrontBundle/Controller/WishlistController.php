<?php

namespace Asmodine\FrontBundle\Controller;

use Asmodine\CommonBundle\Controller\Controller as AsmodineController;
use Asmodine\FrontBundle\Entity\Wishlist;
use Asmodine\FrontBundle\Form\WishlistType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class WishlistController.
 *
 *  @Route("/wishlist")
 */
class WishlistController extends AsmodineController
{
    const NB_WISHLIST_SUGGESTED = 12;

    /**
     * @Route("/add/{redirect}",name="asmodinefront_wishlist_add", defaults={"redirect" = ""} )
     * @Template
     *
     * @param Request $request
     * @param null    $redirect
     *
     * @return array
     */
    public function addAction(Request $request, $redirect = null)
    {
        $wishlist = new Wishlist();
        $form = $this->createForm(WishlistType::class, $wishlist);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //TODO
        }

        return ['form' => $form->createView(), 'redirect' => $redirect];
    }

    /**
     * @Route("/my", name="asmodinefront_whishlist_my")
     * @Template
     *
     * @param Request $request
     *
     * @return array
     */
    public function myAction(Request $request)
    {
        //TODO
        return new Response('Prochainement...');
    }
}
