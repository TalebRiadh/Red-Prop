<?php
namespace App\Controller;

use App\Repository\CategoryRepository;
use Twig\Environment;

use App\Entity\Product;
use App\Entity\Category;
use App\Form\ProductType;
use App\Form\CategoryType;
use App\Repository\ProductRepository;
use \Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class ProductController extends AbstractController
{


    /**
     * @var ProductRepository
     */
    private $repository;


    /**
     * @var CategoryRepository
     */
    private $category_repository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(CategoryRepository $category_repository,ProductRepository $repository,EntityManagerInterface $em)
    {
        $this->category_repository = $category_repository;
        $this->repository = $repository;
        $this->em = $em;
    }


     /**
    * @Route("/admin/showProducts",name="showProducts")
    */
    public function showProducts()
    {         
                $products = $this->repository->findAll();
                return $this->render('admin/showProducts.html.twig',[
                    'products' => $products,
                    'current' => 2
                ]);
    }

    /**
    * @Route("/admin/createProduct",name="createProduct")
    * @param Request $request
    */
    public function createProduct(Request $request)
    {           
                $product = new Product();
                $category = new Category();
                $form_category = $this->createForm(CategoryType::class, $category);
                $form = $this->createForm(ProductType::class, $product);
                 $form->handleRequest($request);
                if ($form->isSubmitted() && $form->isValid()) {
                    $product->onPrePersist();
                    $product->setCode(rand());
                    $this->em->persist($product);
                    $this->em->flush();
                    $this->addFlash('success',$product->getCode().' bien créé avec succés');
                    return $this->redirectToRoute('showProducts');
            }
                return $this->render('admin/NewProduct.html.twig',[
                'form' => $form->createView(),
                'form_category' => $form_category->createView(),          
                'current' => 2

                ]);
    }


    /**
     * @Route("/admin/product/{id}", name="product.edit",methods="GET|POST")
     * @param Product $product
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(Product $product, Request $request)
    {
      $form = $this->createForm(ProductType::class, $product);
        $lastThumb = $product->getThumb();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if($form['imageFile']->getData() == null){
                $product->setThumb($lastThumb);
            }else{
                $product->deleteFile($lastThumb);
            }
            $product->onPreUpdate();
            $this->em->flush();
            $this->addFlash('success',$product->getCode().'  modifié avec succés');
            return $this->redirectToRoute('showProducts');
        }

        return $this->render('admin/editProduct.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
            'current' => 2
        ]);


    }

    /**
     * @Route("admin/product/{id}", name="product.delete",methods="DELETE")
     * @param Product $product
     * @return \Symfony\Component\HttpFoundation\Response
     */

    public function remove(Product $product, Request $request)
    {
        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->get('_token'))) {
            $this->em->remove($product);
            $product->deleteFile($product->getThumb());
            $this->em->flush();
            $this->addFlash('success',$product->getCode().' bien supprimé avec succés');

        }
        return $this->redirectToRoute('admin/showProducts');

    }

     /**
    * @Route("/admin/stateProducts",name="stateProducts")
    * @param Request $request
    */
    public function stateProducts()
    {         
                $products = $this->repository->findAllDisableProduct(0);
                return $this->render('admin/stateProducts.html.twig',[
                    'products' => $products, 
                    'current' => 4
                ]);
    }

    /**
     * @Route("/changeState",name="changeState",options={"expose"=true})
     * @return \Symfony\Component\HttpFoundation\Response
     * @return Response
     */
    public function changeState(Request $request)
{
        $name = $request->request->get('name');
        $product = $this->repository->findOneBySomeField($name);
        $product->setState(true);
        $product->onPreUpdate();
        $this->em->flush();
        $response = new Response(json_encode(array(
            'message'=>'state changed'
    )));
    $response->headers->set('Content-Type', 'application/json');

    return $response;
}


 /**
     * @Route("/createCategory",name="createCategory",options={"expose"=true})
     * @return \Symfony\Component\HttpFoundation\Response
     * @return Response
     */
    public function createCategory(Request $request)
{   
        $new_caterogy = new Category();
        $new_caterogy->setName($request->request->get('category_name'));
        $this->em->persist($new_caterogy);
        $this->em->flush();
        $response = new Response(json_encode(array(
            'message'=>'category created'
    )));
    $response->headers->set('Content-Type', 'application/json');

    return $response;
}


    /**
     * @Route("/admin/categorySetting",name="categorySetting")
     */
    public function categorySetting(){
        $categories = $this->category_repository->findAll();
        return $this->render('admin/categorySetting.html.twig',[
            'categories' => $categories,
            'current' => 5
        ]);
    }


}