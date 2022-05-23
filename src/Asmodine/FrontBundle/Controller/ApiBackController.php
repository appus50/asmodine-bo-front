<?php

namespace Asmodine\FrontBundle\Controller;

use Asmodine\CommonBundle\Controller\Controller as AsmodineController;
use Asmodine\CommonBundle\DTO\BrandDTO;
use Asmodine\CommonBundle\DTO\CategoryDTO;
use Asmodine\FrontBundle\Entity\Brand;
use Asmodine\FrontBundle\Entity\Category;
use Asmodine\FrontBundle\Repository\BrandRepository;
use Asmodine\FrontBundle\Repository\CategoryRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ApiBackController.
 *
 * @Route("/api/back")
 */
class ApiBackController extends AsmodineController
{
    /**
     * @Route("/brands", name="asmodinefront_apiback_brand")
     * @Method({"POST"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function brandAction(Request $request)
    {
        /** @var BrandRepository $repo */
        $repo = $this->getDoctrine()->getRepository('AsmodineFrontBundle:Brand');
        $brands = $request->get('brands');
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var Connection $connection */
        $connection = $this->getDoctrine()->getConnection();
        $new = 0;

        $connection->beginTransaction();
        try {
            foreach ($brands as $datas) {
                $brandDTO = new BrandDTO($datas);

                /** @var Brand $brand */
                $brand = $repo->findOneBy(['backId' => $brandDTO->id]);
                if (is_null($brand)) {
                    ++$new;
                    $brand = new Brand();
                }
                $brand->update($brandDTO);
                $em->persist($brand);
            }

            $em->flush();
            $connection->commit();
        } catch (\Exception $exception) {
            $connection->rollBack();
            $this->get('logger')->error($exception->getMessage());

            return new JsonResponse($exception->getMessage(), Response::HTTP_NOT_ACCEPTABLE);
        }

        return new JsonResponse(
            [
                'Brands received : '.count($brands),
                'New Brands : '.$new,
            ]
        );
    }

    /**
     * @Route("/categories", name="asmodinefront_apiback_category")
     * @Method({"POST"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function categoryAction(Request $request)
    {
        /** @var CategoryRepository $repo */
        $repo = $this->getDoctrine()->getRepository('AsmodineFrontBundle:Category');
        $categories = $request->get('categories');
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var Connection $connection */
        $connection = $this->getDoctrine()->getConnection();
        $new = 0;
        $this->get('logger')->error(var_export($categories, true));
        $connection->beginTransaction();
        try {
            foreach ($categories as $category) {
                $categoryDTO = new CategoryDTO($category);
                $this->get('logger')->debug(var_export($category, true));
                /** @var Category $category */
                $category = $repo->findOneBy(['backId' => $categoryDTO->id]);
                if (is_null($category)) {
                    ++$new;
                    $category = new Category();
                }
                $category->update($categoryDTO);
                $em->persist($category);
            }

            $em->flush();
            $connection->commit();
        } catch (\Exception $exception) {
            $connection->rollBack();
            $this->get('logger')->error($exception->getMessage());

            return new JsonResponse($exception->getMessage(), Response::HTTP_NOT_ACCEPTABLE);
        }

        $repo->optimize();

        return new JsonResponse(
            [
                'Brands received : '.count($categories),
                'New Brands : '.$new,
            ]
        );
    }
}
