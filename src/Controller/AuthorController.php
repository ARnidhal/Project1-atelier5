<?php

namespace App\Controller;
use App\Entity\Author;
use App\Form\AuthorType;
use App\Form\MinmaxType;
use App\Repository\AuthorRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\VarDumper\Dumper\DataDumperInterface;

class AuthorController extends AbstractController
{
    public $authors = array(
        array('id' => 1, 'picture' => '/images/Victor-Hugo.jpg','username' => 'Victor Hugo', 'email' => 'victor.hugo@gmail.com ', 'nb_books' => 100),
        array('id' => 2, 'picture' => '/images/william-shakespeare.jpg','username' => ' William Shakespeare', 'email' =>  ' william.shakespeare@gmail.com', 'nb_books' => 200 ),
        array('id' => 3, 'picture' => '/images/Taha_Hussein.jpg','username' => 'Taha Hussein', 'email' => 'taha.hussein@gmail.com', 'nb_books' => 300),
        );

   
    #[Route('/showauthor/{name}', name: 'app_showauthor')]
    public function show($name): Response
    {
        return $this->render('author/show.html.twig', [
            'name' => $name
        ]);
    }
    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }

    #[Route('/showidauthor/{id}', name: 'showidauthor')]
    public function showidauthor( $id,AuthorRepository $authorRepository): Response
    {
        $author= $authorRepository->showbyidauthor($id);
        return $this->render('author/showidauthor.html.twig', [
            'authors' => $author,
        ]);
    }

    #[Route('/showtableauthor', name: 'app_showtableauthor')]
  public function showtableauthor($id): Response
    {

        
        return $this->render('author/showtableauthor.html.twig', [
            'authors' => $this->authors
        ]);
    }

    #[Route('/showbyid/{id}', name: 'showbyid')]
    public function showbyid($id): Response
    {   
        #var_dump($id).die();
        
        $author=null;
        foreach($this->authors as $authorD)
        {
         if($authorD['id']==$id)
         $author=$authorD;
        }
        #var_dump($author).die();
        return $this->render('author/showbyid.html.twig', [
            'author' => $author
        ]);
    }


    #[Route('/showdbauthor', name: 'showdbauthor')] //affichage
    public function showdbauthor(AuthorRepository $authorRepository): Response
    {

       // $author=$authorRepository->findAll();
      $author=$authorRepository->orderbyusername();//tri ASC
      // $author=$authorRepository-> seachwithalph();//recherche
        return $this->render('author/showdbauthor.html.twig', [
            'author'=>$author

        ]);
    }

    #[Route('/addauthor', name: 'addauthor')] //ajout
    public function addauthor(ManagerRegistry $managerRegistry): Response
    {
        $x=$managerRegistry->getManager();
        $author=new Author(); //ken fama hedhy instance raw ajout ken famech ray update
        $author->setUsername("3a54new");
        $author->setEmail("3a54new@esprit.tn");
        $x->persist($author);
        $x->flush(); //3ibara botton exuction
        return new Response("great add");
    }
    #[Route('/addformauthor', name: 'addformauthor')]
    public function addformauthor(ManagerRegistry $managerRegistry,Request $req): Response
    {
        $x=$managerRegistry->getManager();//ya3mel ay update w ayy delete ay ajout
        $author=new Author();
        $form=$this->createForm(AuthorType::class,$author);
        $form->handleRequest($req);//mima post bech thot fil base 
        if($form->isSubmitted() and $form->isValid())
        {
        $x->persist($author);
        $x->flush();
        return $this->redirectToRoute('showdbauthor');//bech ihezni lil page show kima href

        
        }



        return $this->renderForm('author/addformauthor.html.twig', [
            'f'=>$form,
        ]);
    }


    #[Route('/editauthor/{id}', name: 'editauthor')]
    public function editauthor($id, authorRepository $authorRepository, ManagerRegistry $managerRegistry,Request $req): Response
    {
       
       
        $x = $managerRegistry->getManager();
        $dataid=$authorRepository->find($id); //fi blaset istance
        
        $form=$this->createForm(AuthorType::class,$dataid);
        $form->handleRequest($req);
        if($form->isSubmitted() and $form->isValid()){
        $x->persist($dataid);
        $x->flush();
           return $this->redirectToRoute('showdbauthor');

        }
        return $this->renderForm('author/editauthor.html.twig', [
            'x' => $form 
        ]);
       
    }

    
    #[Route('/deleteauthor/{id}', name: 'deleteauthor')]
    public function deleteauthor($id, ManagerRegistry $managerRegistry, AuthorRepository $authorRepository): Response
    {
        $em = $managerRegistry->getManager();
        $dataid = $authorRepository->find($id);
        $em->remove($dataid);
        $em->flush();
        return $this->redirectToRoute('showdbauthor');
    }

    #[Route('/listminmax', name: 'listminmax')]
    #[Route('/minmax', name: 'minmax')]
    public function listBooksByAuthorBookCountRange(Request $request, AuthorRepository $authorRepository): Response
    {
        $form = $this->createForm(MinmaxType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $min = $data['Min'];
            $max = $data['Max'];

            $authors = $authorRepository->SearchAuthorminmax($min, $max);
            return $this->render('author/listminmax.html.twig', [
                'tab' => $authors,
            ]);
        }

        return $this->renderForm('author/minmax.html.twig', [
            'f' => $form,
        ]);
    }
    #[Route('/DeleteDQL', name:'DD')]
        function DeleteDQL(AuthorRepository $repo){
            $repo->DeleteAuthorwith0books();
            return $this->redirectToRoute('showdbauthor');
        }

}
