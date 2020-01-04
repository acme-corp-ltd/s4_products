<?php


namespace App\Controller;


use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractFOSRestController implements ClassResourceInterface
{
    protected $productRepository;
    protected $entityManager;

    public function __construct(ProductRepository $productRepository, EntityManagerInterface $entityManager)
    {
        $this->productRepository = $productRepository;
        $this->entityManager = $entityManager;
    }

    public function cgetAction()
    {
        $entities = $this->productRepository->findAll();
        return $this->view($entities, 200)
            ->setTemplate('datatable.html.twig')
            ->setTemplateVar('datalist');
    }

    /**
     * @Rest\Route("/products", methods={"put"})
     * @ParamConverter("input", class="array<App\Entity\Product>", converter="fos_rest.request_body")
     * @param array $input
     * @return Response
     */
    public function replaceAction(array $input) {
        $current = $this->productRepository->findAll();
        foreach ($current as $item) {
            $this->entityManager->remove($item);
        }
        foreach ($input as $item) {
            $this->entityManager->persist($item);
        }
        $this->entityManager->flush();

        return new Response(200);
    }

}