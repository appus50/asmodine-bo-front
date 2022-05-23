<?php

namespace Asmodine\FrontBundle\Controller;

use Asmodine\CommonBundle\Controller\Controller as AsmodineController;
use Asmodine\CommonBundle\Model\Morphoprofile\Gender;
use Asmodine\FrontBundle\Entity\Brand;
use Asmodine\FrontBundle\Entity\Category;
use Asmodine\FrontBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;

/**
 * Class MainController.
 *
 * @Route("")
 */
class MainController extends AsmodineController
{
    /**
     * @Route("",name="asmodinefront_main_home")
     * @Template
     */
    public function indexAction()
    {
        if (!is_null($this->getUser())) {
            $appDatas = $this->get('session')->get(UserController::APPLICATION_DATAS, []);
            if (count($appDatas) > 0) {
                $this->addFlash('success', 'Merci de compléter votre profil pour profiter pleinement d\'Asmodine.');

                return $this->redirectToRoute('asmodinefront_user_profile');
            }
        }
        if ($this->get('session')->get('tmpR')) {
            echo "<div class='tmpR'></div>";
            $this->get('session')->set('tmpR', false);
        }
        return [];
    }

    /**
     * @Route("/recherche",name="asmodinefront_main_search")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function searchAction(Request $request)
    {
        $search = $request->request->get('search');

        /** @var Brand $brand */
        $brand = $this->getDoctrine()->getRepository('AsmodineFrontBundle:Brand')->findOneBy(['name' => $search]);
        if (!is_null($brand)) {
            return $this->redirectToRoute('asmodinefront_model_brand', ['slug' => $brand->getSlug()]);
        }

        /** @var Router $router */
        $router = $this->get('router');
        $referer = $request->headers->get('referer');
        $baseUrl = $request->getBaseUrl();
        if (strlen($baseUrl) > 0) {
            $lastPath = substr($referer, strpos($referer, $baseUrl) + strlen($baseUrl));
            if (false !== strpos($lastPath, '?')) {
                $lastPath = substr($lastPath, 0, strpos($lastPath, '?'));
            }
        } else {
            $lastPath = '/';
        }

        try {
            $params = $router->getMatcher()->match($lastPath);
        } catch (\Exception $e) {
            return  $this->redirectToRoute('asmodinefront_model_search', ['search' => urlencode($search)]);
        }

        if ('asmodinefront_model_category' == $params['_route']) {
            return $this->redirectToRoute($params['_route'], ['path' => $params['path'], 'search' => $search]);
        }

        /** @var User $user */
        $user = $this->getUser();
        if (is_null($user)) {
            return  $this->redirectToRoute('asmodinefront_model_search', ['search' => urlencode($search)]);
        }

        /** @var Category $category */
        $category = null;
        if (Gender::isMale($user->getGender())) {
            $category = $this->getDoctrine()->getRepository('AsmodineFrontBundle:Category')->findOneBy(['gender' => Gender::MALE, 'depth' => 0]);
        }
        if (Gender::isFemale($user->getGender())) {
            $category = $this->getDoctrine()->getRepository('AsmodineFrontBundle:Category')->findOneBy(['gender' => Gender::FEMALE, 'depth' => 0]);
        }
        if (!is_null($category)) {
            $this->redirectToRoute('asmodinefront_model_category', ['path' => $category->getPath(), 'search' => $search]);
        }

        return $this->redirectToRoute('asmodinefront_model_search', ['search' => urlencode($search)]);
    }
}
