<?php

namespace Asmodine\FrontBundle\Controller;

use Asmodine\CommonBundle\Controller\Controller as AsmodineController;
use Asmodine\FrontBundle\Entity\Brand;
use Asmodine\FrontBundle\Repository\CategoryRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class PageController.
 *
 * @Route("/page")
 */
class PageController extends AsmodineController
{
    /**
     * @Route("/le-concept",name="asmodinefront_page_concept")
     * @Cache(expires="tomorrow", public=true)
     * @Template
     *
     * @return array
     */
    public function conceptAction()
    {
        return ['slug' => 'concept'];
    }

    /**
     * @Route("/marques",name="asmodinefront_page_brands")
     * @Cache(expires="tomorrow", public=true)
     * @Template
     *
     * @return array
     */
    public function brandsAction()
    {
        $brands = $this->getDoctrine()->getRepository('AsmodineFrontBundle:Brand')->findBy(['enabled' => true], ['name' => 'ASC']);

        $getFirst = function (Brand $brand) {
            $name = trim($brand->getName());

            return substr(ucfirst($name), 0, 1);
        };
        $firsts = array_map($getFirst, $brands);

        return ['brands' => $brands, 'first_letters' => array_unique($firsts), 'slug' => 'brands'];
    }

    /**
     * @Route("/categories",name="asmodinefront_page_categories")
     * @Cache(expires="tomorrow", public=true)
     * @Template
     *
     * @return array
     */
    public function categoriesAction()
    {
        /** @var CategoryRepository $categoryRepo */
        $categoryRepo = $this->getDoctrine()->getRepository('AsmodineFrontBundle:Category');

        $level0 = $categoryRepo->findBy(['depth' => 0, 'enabled' => true]);
        $level1 = $categoryRepo->findBy(['depth' => 1, 'enabled' => true]);
        $level2 = $categoryRepo->findBy(['depth' => 2, 'enabled' => true]);

        return ['cat_0' => $level0, 'cat_1' => $level1, 'cat_2' => $level2, 'slug' => 'categories'];
    }

    /**
     * @Route("/contact",name="asmodinefront_page_contact")
     * @Template
     *
     * @param Request $request
     *
     * @return array
     */
    public function contactAction(Request $request)
    {
        $sendEmail = false;
        $formBuilder = $this->createFormBuilder()
            ->add('firstname', TextType::class, ['required' => true])
            ->add('lastname', TextType::class, ['required' => true])
            ->add('email', EmailType::class, ['required' => true])
            ->add('subject', TextType::class, ['required' => true])
            ->add('message', TextareaType::class, ['required' => true])
            ->add('submit', SubmitType::class, ['required' => true]);

        $privateForm = $formBuilder->getForm();
        $privateForm->handleRequest($request);

        $professionalForm = $formBuilder
            ->add('company', TextType::class, ['required' => true])
            ->getForm();

        if ($privateForm->isSubmitted() && $privateForm->isValid()) {
            // data is an array with "name", "email", and "message" keys
            $data = $privateForm->getData();
        }

        return [
            'slug' => 'contact',
            'private' => $privateForm->createView(),
            'professional' => $professionalForm->createView(),
            'send_email' => $sendEmail,
        ];
    }

    /**
     * @Route("/cgu",name="asmodinefront_page_cgu")
     * @Template
     *
     * @param Request $request
     *
     * @return array
     */
    public function cguAction(Request $request)
    {
        return [
            'slug' => 'cgu',
        ];
    }

    /**
     * @Route("/partenaires",name="asmodinefront_page_partners")
     * @Template
     *
     * @param Request $request
     *
     * @return array
     */
    public function partnersAction(Request $request)
    {
        return [
            'slug' => 'partners',
        ];
    }

    /**
     * @Route("/cookies",name="asmodinefront_page_cookies")
     * @Template
     *
     * @param Request $request
     *
     * @return array
     */
    public function cookiesAction(Request $request)
    {
        return [
            'slug' => 'cookie',
        ];
    }

    /**
     * @Route("/redirection/marque/{id}/{redirect}/{from}",name="asmodinefront_page_redirection_brand", defaults={"from" = "asmotaille"})
     * @ParamConverter("id", class="AsmodineFrontBundle:Brand")
     * @Template
     *
     * @param Brand $brand
     * @param $redirect
     * @param $from
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectbrandAction(Brand $brand, $redirect, $from)
    {
        /** @var Session $session */
        $session = $this->get('session');
        $session->set('asmotaille_from', urldecode($from));

        return ['brand_name' => $brand->getName(), 'brand_logo' => $brand->getLogo(), 'redirect_url' => urldecode($redirect)];
    }

    /**
     * @Route("/newsletter",name="asmodinefront_page_newsletter")
     * @Template
     *
     * @return array
     */
    public function newsletterAction()
    {
        return ['slug' => 'newsletter'];
    }
}
