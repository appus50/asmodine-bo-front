<?php

namespace Asmodine\FrontBundle\Controller;

use Asmodine\CommonBundle\Controller\Controller as AsmodineController;
use Asmodine\CommonBundle\Model\Morphoprofile\Gender;
use Asmodine\FrontBundle\Entity\Category;
use Asmodine\FrontBundle\Repository\BrandRepository;
use Asmodine\FrontBundle\Repository\CategoryRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\EmailValidator;

/**
 * Class FragmentController.
 *
 * @Route("/_fragment")
 */
class FragmentController extends AsmodineController
{
    /**
     * @Route("/header/categories", name="asmodinefront_fragment_headercategories")
     * @Cache(expires="tomorrow", public=true)
     * @Template("@AsmodineFrontBundle/Fragment/header_categories.html.twig")
     */
    public function headerCategoriesAction()
    {
        $rootCategory = $this->getDoctrine()
            ->getRepository('AsmodineFrontBundle:Category')
            ->findBy(['depth' => 0, 'enabled' => true], ['position' => 'ASC']);
        $getLinks = function (Category $category) {
            return [
                'id' => $category->getBackId(),
                'name' => $category->getName(),
                'url' => $this->generateUrl('asmodinefront_model_category', ['path' => $category->getPath()]),
            ];
        };
        $links = array_map($getLinks, $rootCategory);

        $links[] = [
            'name' => $this->getTranslator()->trans('category.brand'),
            'url' => $this->generateUrl('asmodinefront_page_brands'),
        ];

        $links[] = [
            'name' => $this->getTranslator()->trans('category.concept'),
            'url' => $this->generateUrl('asmodinefront_page_concept'),
        ];

        return ['links' => $links];
    }

    /**
     * @Route("/header/sub_categories/{mode}", name="asmodinefront_fragment_headersubcategories")
     * @Cache(expires="tomorrow", public=true)
     * @Template("@AsmodineFrontBundle/Fragment/header_sub_categories.html.twig")
     *
     * @param string $mode
     *
     * @return array
     */
    public function headerSubCategoriesAction(string $mode)
    {
        /** @var CategoryRepository $categoryRepo */
        $categoryRepo = $this->getDoctrine()->getRepository('AsmodineFrontBundle:Category');

        $level0 = $categoryRepo->findBy(['depth' => 0, 'enabled' => true]);
        $level1 = $categoryRepo->findBy(['depth' => 1, 'enabled' => true]);
        $level2 = $categoryRepo->findBy(['depth' => 2, 'enabled' => true]);
        $more = [];
        $more[] = [
            'name' => $this->getTranslator()->trans('category.brand'),
            'url' => $this->generateUrl('asmodinefront_page_brands'),
        ];
        $more[] = [
            'name' => $this->getTranslator()->trans('category.concept'),
            'url' => $this->generateUrl('asmodinefront_page_concept'),
        ];

        return ['mode' => $mode, 'categories_0' => $level0, 'categories_1' => $level1, 'categories_2' => $level2, 'more' => $more];
    }

    /**
     * @Route("/header/user/{mode}", name="asmodinefront_fragment_headeruser")
     * @Template("@AsmodineFrontBundle/Fragment/header_user.html.twig")
     */
    public function headerUserAction(string $mode)
    {
        //FIXME
        return ['mode' => $mode];
    }

    /**
     * @Route("/header/cart", name="asmodinefront_fragment_cart")
     * @Template("@AsmodineFrontBundle/Fragment/header_cart.html.twig")
     */
    public function cartAction()
    {
        //FIXME
        return ['products' => []];
    }

    /**
     * @Route("/footer", name="asmodinefront_fragment_footer")
     * @Cache(expires="tomorrow", public=true)
     * @Template
     */
    public function footerAction()
    {
        $max = 8;
        /** @var CategoryRepository $categoryRepo */
        $categoryRepo = $this->getDoctrine()->getRepository('AsmodineFrontBundle:Category');
        /** @var BrandRepository $brandRepo */
        $brandRepo = $this->getDoctrine()->getRepository('AsmodineFrontBundle:Brand');

        $womanCat = $categoryRepo->findBy(['depth' => 2, 'enabled' => true, 'gender' => Gender::FEMALE]);
        $manCat = $categoryRepo->findBy(['depth' => 2, 'enabled' => true, 'gender' => Gender::MALE]);
        shuffle($womanCat);
        shuffle($manCat);

        $categories = [
            'woman' => array_slice($womanCat, 0, $max),
            'man' => array_slice($manCat, 0, $max),
        ];

        $brands = $brandRepo->findBy(['enabled' => true]);
        shuffle($brands);
        $brands = array_slice($brands, 0, $max);

        return ['gender_categories' => $categories, 'brands' => $brands];
    }

    /**
     * @Route("/social", name="asmodinefront_fragment_social")
     * @Cache(expires="tomorrow", public=true)
     * @Template
     */
    public function socialAction()
    {
        return [];
    }

    /**
     * @Route("/blog", name="asmodinefront_fragment_blog")
     * @Cache(expires="+2 hours", public=true)
     * @Template
     */
    public function blogAction()
    {
        $url = $this->getTranslator()->trans('social.blog.url').'/feed/';
        $xml = @file_get_contents($url);
        $document = new \DOMDocument();
        $document_image = new \DOMDocument();

        $document->loadXml($xml);
        $items_articles = $document->getElementsByTagName('item');

        $articles = [];
        for ($i = 0; $i < 4; ++$i) {
            $title = $items_articles->item($i)->getElementsByTagName('title')->item(0)->nodeValue;
            $content = $items_articles->item($i)->getElementsByTagName('encoded')->item(0)->nodeValue;
            $document_image->loadHTML($content);
            $image = utf8_decode($document_image->getElementsByTagName('img')->item(0)->attributes->getNamedItem('src')->nodeValue);
            $link = $items_articles->item($i)->getElementsByTagName('link')->item(0)->nodeValue;

            $articles[] = [
                'title' => $title,
                'image' => $image,
                'link' => $link,
            ];
        }

        return ['articles' => $articles];
    }

    /**
     * @Route("/newsletter", name="asmodinefront_fragment_newsletter")
     * @Template
     */
    public function newsletterAction(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add(
                'email',
                EmailType::class,
                [
                    'constraints' => new EmailValidator(),
                    'label' => false,
                    'attr' => ['placeholder' => 'newsletter.placeholder', 'class' => 'form-control'],
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'newsletter.register',
                    'attr' => ['class' => 'waves-button-input'],
                ]
            )
            ->setAction($this->generateUrl('asmodinefront_page_newsletter'))
            ->setMethod('post')
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $datas = $form->getData();
            $email = $datas['email'];
            $return_code = $this->get('manager.newsletter')->register($email);

            /*   return new RedirectResponse(
                   $this->generateUrl(
                       'newsletter_validation', [
                           'return_code' => $return_code,
                       ]
                   )
               );*/
        }

        return ['form_newsletter' => $form->createView()];
    }
}
