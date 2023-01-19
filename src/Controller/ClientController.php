<?php
namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Filesystem\Exception\IOException;
use Twig\Environment;
use Doctrine\Persistence\ObjectManager;

use App\Entity\Basket;
use App\Entity\Product;
use App\Entity\Category;
use App\Entity\Orderlist;
use App\Form\ProductType;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use App\Repository\BasketRepository;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use App\Repository\OrderlistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;



class ClientController extends AbstractController
{


    /**
     * @var ProductRepository
     */
    private $repository;

    /**
     * @var UserRepository
     */
    private $userRepository;



     /**
     * @var BasketRepository
     */
    private $u;
    
     /**
     * @var CategoryRepository
     */
    private $ca;
    
    

     /**
     * @var OrderlistRepository
     */
    private $r;
    
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(CategoryRepository $ca,
    UserRepository $userRepository,
    BasketRepository $u,
    ProductRepository $repository,
    OrderlistRepository $r,
    EntityManagerInterface $em)
    {
        $this->ca = $ca;
        $this->userRepository = $userRepository;
        $this->repository = $repository;
        $this->u=$u;
        $this->r = $r;
        $this->em = $em;
    }


    public function nbrProductOnBasket(){
        $user = $this->getUser();
        if($user != null){
            $basket = $user->getBasket();
            $numberOfProductOnBasket = $basket->getNumberOfProducts();
            return $numberOfProductOnBasket;
        }else {
            return 0;
        }
       

    }


    /**
     * @Route("/searchProduct",name="searchProduct",methods="GET|POST")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function searchProduct(Request $request)
    {

            $products = $this->repository->findSearchProduct(strval($request->get('search')));
            if($products == null){
                $countProducts = 0;
            }else{
                $countProducts = 1;
            }

        return $this->render('ProductSearch.html.twig',[
            'product' => $products,
            'nbrProductOnBasket' =>$this->nbrProductOnBasket(),

            'current' => 3,
            'resultats' => $countProducts,


        ]);
    }



    /**
     * @Route("/", name="home")
     */
    public function index()
    {   
        $user = $this->getUser();
        $categories = $this->ca->findLastestCategory();
        $productsDispo = $this->repository->findAllDispoProduct();


        return $this->render(
            'HomePage.html.twig',[
                'user' => $user,
                'categories' => $categories,
                'nbrProductOnBasket' =>$this->nbrProductOnBasket(),
                'productsDispo' => $productsDispo,
                'current' => 1
            ]
            
        );
    }

    /**
     * @Route("/NewProducts",name="NewProducts",methods="GET|POST")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function NewProducts()
    {
        $products = $this->repository->findAll();
        $count = count($products);
        return $this->render('newProducts.html.twig',[
            'products' => $products,
            'nbrProductOnBasket' =>$this->nbrProductOnBasket(),

            'current' => 3,
            'resultats' =>$count,


        ]);
    }


    /**
     * @Route("/contact", name="contact")
     */
    public function contact()
    {
        $user = $this->getUser();
        $categories = $this->ca->findLastestCategory();
        $productsDispo = $this->repository->findAllDispoProduct();
        return $this->render(
            'contact.html.twig',[
                'user' => $user,
                'categories' => $categories,
                'nbrProductOnBasket' =>$this->nbrProductOnBasket(),
                'productsDispo' => $productsDispo,
                'current' => 6
            ]

        );
    }
    /**
     * @Route("/category", name="category")
     */
    public function category()
    {

        return $this->render(
            'Categorie.html.twig',[
                'nbrProductOnBasket' =>$this->nbrProductOnBasket(),
                 'categories' => $this->ca->findAll(),
                'current' => 2
            ]

        );
    }
    /**
     * @Route("/aboutUs", name="aboutUs")
     */
    public function aboutUs()
    {
        $user = $this->getUser();
        $categories = $this->ca->findLastestCategory();
        $productsDispo = $this->repository->findAllDispoProduct();
        return $this->render(
            'aboutUs.html.twig',[
                'user' => $user,
                'categories' => $categories,
                'nbrProductOnBasket' =>$this->nbrProductOnBasket(),
                'productsDispo' => $productsDispo,
                'current' => 5
            ]

        );
    }
     /**
     * @Route("/productPage/{id}", name="productPage",methods="GET|POST")
     * @param Product $product
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function productPage(Product $product)
    {   
        $altProducts = $this->repository->AltSameProduct($product->getCategory());
        
        return $this->render('productPage.html.twig', [
            'product' => $product,
            'otherProduct' =>$altProducts,
            'nbrProductOnBasket' =>$this->nbrProductOnBasket(),
            'current' => 2

        ]);

    }

    /**
    * @Route("/admin/showClients",name="showClients")
    * @param Request $request
    */
    public function showClients()
    {            
        $clients = $this->userRepository->findAll();
        return $this->render('admin/showClients.html.twig',[
            'clients' => $clients,
            'current' => 3
                            ]);
    }

     /**
     * @Route("/admin/editClient/{id}", name="editClient",methods="GET|POST")
     * @param User $user
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editClient(User $user, Request $request,UserPasswordHasherInterface  $encoder)
    {   
        
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $user->onPreUpdate();
            $this->em->flush();
            $this->addFlash('success', 'bien modifié avec succés');
            return $this->redirectToRoute('showClients');
        }

        return $this->render('editClient.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'current' => 3

        ]);

    }

    /**
     * @Route("/admin/deleteClient/{id}", name="deleteClient",methods="DELETE")
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     */

    public function deleteClient(User $user, Request $request)
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->get('_token'))) {
            $this->em->remove($user);
            $this->em->flush();
            $this->addFlash('success', 'bien supprimé avec succés');

        }
        return $this->redirectToRoute('showClient');

    }


     /**
    * @Route("/order",name="Order")
    * @param Request $request
    */
    public function OrderAction()
    {            $user = $this->getUser();
                $basket = $user->getBasket();
                $products = $basket->getProducts();
                return $this->render('orderProducts.html.twig',[
                    'products' => $products,
                    'nbrProductOnBasket' =>$this->nbrProductOnBasket(),
                    'user' => $user,
                    'current' => 0,
                ]);
    }

    /**
    * @Route("/admin/showOrders",name="showOrders")
    * @param Request $request
    */
    public function showOrders()
    {            
        $orders = $this->r->findBy([], ['id' => 'DESC']);
        return $this->render('admin/showOrders.html.twig',[
            'orders' => $orders,
            'current' => 1
                            ]);
    }


    /**
    * @Route("/listProductsByCategory/{id}",name="listProductsByCategory",methods="GET|POST")
    * @param Category $category
    * @param Request $request
    * @return \Symfony\Component\HttpFoundation\Response
    */
    public function listProductsByCategory(Category $category, Request $request)
    {            
        return $this->render('listProductsByCategory.html.twig',[
                        'category' => $category,
                        'nbrProducts' => $category->getNumberOfProducts(),
                        'current' => 0,
                        'nbrProductOnBasket' =>$this->nbrProductOnBasket(),


        ]);
    }


       /**
     * @Route("/orderValided",name="orderValided",options={"expose"=true})
     * @return \Symfony\Component\HttpFoundation\Response
     * @return Response
     */
    public function orderValided(Request $request)
{
        $order = json_decode($request->request->get('order'));
        $test = new Orderlist();
        $test->setList($order);
        $test->setReference(rand());
        $test->onPrePersist();
        $test->setClient($request->request->get('email_client'));
        $test->setTotalprice($request->request->get('total_price'));
        $this->em->persist($test);
        $this->em->flush();
        $response = new Response(json_encode(array(
            'ref' => $test->getReference(),
            'redirect' => 'http://127.0.0.1:8000'
    )));
    $response->headers->set('Content-Type', 'application/json');

    return $response;
}


   /**
     * @Route("/deleteProductFromBasket",name="deleteProductFromBasket",options={"expose"=true})
     * @return \Symfony\Component\HttpFoundation\Response
     * @return Response
     */
    public function delete_product_from_basket(Request $request)
{
        $product = $this->repository->findOneByCode($request->request->get('code_product'));
        $user = $this->getUser();
        $basket = $user->getBasket();
        $basket->removeProduct($product);
        $this->em->persist($basket);
        $this->em->flush();
        $response = new Response(json_encode(array(
                    'message' => 'product deleted from basket '
    )));
    $response->headers->set('Content-Type', 'application/json');

    return $response;
}



     /**
     * @Route("/addToBasket", name="addProductToBasket",options={"expose"=true})
     * @return \Symfony\Component\HttpFoundation\Response
     * @return Response
     */
    public function addProductToBasket( Request $request)
    {
        $message = "";
        $user = $this->getUser();
        $basket = $user->getBasket();
        $id_product = $request->request->get('id_product');
        $product = $this->repository->find($id_product);
        $productsOnBasket = $basket->getProducts();
        foreach($productsOnBasket as $p){
            if($p == $product){
                $message = "le produit  dans votre Panier !";
            }
        }
        $basket->addProduct($product);
        $this->em->persist($basket);
        $this->em->flush();
        $message = "le produit est ajouté a votre panier";
        $response = new Response(json_encode(array(
                    'message' => $message
            )));
        $response->headers->set('Content-Type', 'application/json');

    return $response;
    }


}