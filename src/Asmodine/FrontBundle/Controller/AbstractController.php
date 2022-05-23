<?php

namespace Asmodine\FrontBundle\Controller;

use Asmodine\CommonBundle\Controller\Controller as AsmodineController;
use Asmodine\CommonBundle\Service\ElasticsearchService;
use Asmodine\FrontBundle\Entity\Category;
use Asmodine\FrontBundle\Entity\PhysicalProfile;

/**
 * Class AbstractController.
 */
abstract class AbstractController extends AsmodineController
{
    /**
     * @return ElasticsearchService
     */
    protected function getElasticsearchService(): ElasticsearchService
    {
        return $this->get('asmodine.common.elasticsearch');
    }

    /**
     * @param Category $category
     *
     * @return array
     */
    protected function getBreadcrumbCategory(Category $category): array
    {
        $breadcrumb = [];
        $depth = $category->getDepth();
        for ($i = $depth; $i >= 0; --$i) {
            array_unshift(
                $breadcrumb,
                [
                    'name' => $category->getName(),
                    'url' => $this->generateUrl('asmodinefront_model_category', ['path' => $category->getPath()]),
                ]
            );

            if ($depth > 0) {
                $category = $this->getDoctrine()
                    ->getRepository('AsmodineFrontBundle:Category')
                    ->findOneBy(['backId' => $category->getBackParentId()]);
            }
        }
        array_unshift($breadcrumb, $this->getBreadcrumbHome());

        return $breadcrumb;
    }

    /**
     * Home Breadcrumb.
     *
     * @return array
     */
    protected function getBreadcrumbHome(): array
    {
        return [
            'url' => $this->generateUrl('asmodinefront_main_home'),
            'name' => $this->getTranslator()->trans('breadcrumb.title.home'),
        ];
    }

    /**
     * @return PhysicalProfile|null
     */
    protected function getCurrentPhysicalProfile(): ?PhysicalProfile
    {
        $user = $this->getUser();
        if (is_null($user)) {
            return null;
        }

        /** @var PhysicalProfile $physicalProfile */
        $physicalProfile = $this->getDoctrine()
            ->getRepository('AsmodineFrontBundle:PhysicalProfile')
            ->findOneBy(['user' => $user, 'current' => true]);
        if (is_null($physicalProfile)) {
            return PhysicalProfile::create($user);
        }

        return $physicalProfile;
    }
}
