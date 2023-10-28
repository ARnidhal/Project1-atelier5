<?php




namespace App\Controller;
use App\Entity\Author;
use App\Entity\Book;
use App\Form\AuthorType;
use App\Form\BookType;
use App\Form\SearchType;
use App\Repository\AuthorRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\VarDumper\Dumper\DataDumperInterface;







class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }


    #[Route('/addformbook', name: 'addformbook')]
    public function addformbook( ManagerRegistry $managerRegistry, Request $req): Response
    {
        $x=$managerRegistry->getManager();
        $book=new Book();
        $form=$this->createForm(BookType::class,$book);
        $form->handleRequest($req);
        if($form->isSubmitted() and $form->isValid())
        {
         $author = $book->getAuthors();
         
         $x->persist($author);
        $x->persist($book);
        $x->flush();
       
        return $this->redirectToRoute('showdbbook');
    }

    return $this->renderForm('book/addformbook.html.twig', [
        'f'=>$form
    ]);
    }

    #[Route('/showdbbook', name: 'showdbbook')]
    public function publishedBooks(BookRepository $bookRepository,Request $req): Response
    {
       
        // Récupérez la liste des livres publiés
        $nbr=$bookRepository->NbBookCategory();
        $bkk =$bookRepository->ShowBookOrderByAuthor();
        $book = $bookRepository->findBy(['published' => true]);
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($req);
        if($form->isSubmitted()){
            $ref = $form->get('ref')->getData();
   
            $book = $bookRepository->showbyiidauthor($ref);
        }
      


         if ($book === null) {
            throw $this->createNotFoundException('Aucun Livre.');
        }

        $publishedCount = $bookRepository->count(['published' => true]);
        $unpublishedCount = $bookRepository->count(['published' => false]);

        return $this->renderForm('book/showdbbook.html.twig', [
            'book' => $book,
            'f'=> $form,
            'publishedCount' => $publishedCount,
            'unpublishedCount' => $unpublishedCount,
            'nbr' => $nbr,
            'bkk'=>$bkk,
        ]);
    }

    #[Route('/byauthor',name:'byauthor')]
     function Mail(BookRepository $repo,Request $request){

        $form=$this->createForm(SearchType::class,);     
   $form->handleRequest($request);
   $nbr=$repo->NbBookCategory();
   if ($form->isSubmitted()){
   $ref = $form->get('ref')->getData();
   
   
   $book = $repo->findBookByRef($ref);
   }

   $publishedCount = $repo->count(['published' => true]);
        $unpublishedCount = $repo->count(['published' => false]);
    
         $book=$repo->ShowBookOrderByAuthor();
         return $this->renderForm('book/showdbbook.html.twig', [
             'book' => $book,
             'f' =>$form,
             'publishedCount' => $publishedCount,
            'unpublishedCount' => $unpublishedCount,
            'nbr' => $nbr


         ]);
        }
    #[Route('/editbook/{id}', name: 'editbook')]
    public function editbook($id,BookRepository $bookRepository,ManagerRegistry $managerRegistry,Request $req): Response
    {
        //var_dump($id).die();
        $em=$managerRegistry->getManager();
        $dataid=$bookRepository->find($id);
        //var_dump($dataid).die();
        $form=$this->createForm(BookType::class,$dataid);
        $form->handleRequest($req);
        if ($form->isSubmitted() and $form->isValid()){
            $author = $dataid->getAuthors();
            $em->persist($dataid);
            $em->flush();
            return $this->redirectToRoute('showdbbook');

        }
        return $this->renderForm('book/editbook.html.twig', [
            'f' => $form
        ]);
    }

    #[Route('/deletebook/{id}', name: 'deletebook')]
    public function deletebook($id,BookRepository $authorRepository,ManagerRegistry $managerRegistry): Response
    {
        //var_dump($id).die();
        $em=$managerRegistry->getManager();
        $dataid=$authorRepository->find($id);
        $author = $dataid->getAuthors();
        $author->setNbrBooks($author->getNbrBooks() - 1);
        //var_dump($dataid).die();
        $em->remove($dataid);
        $em->flush();
        return $this->redirectToRoute('showdbbook');
    }
    #[Route('/showiidauthor/{ref}', name: 'showiidauthor')]
    public function showiidauthor( $ref,BookRepository $bookRepository): Response
    {
        $book= $bookRepository->showbyiidauthor($ref);
        return $this->render('book/showiidauthor.html.twig', [
            'books' => $book,
        ]);
    }

    #[Route('/book35', name: 'book35')]
    public function BooksBefore2023(BookRepository $bookRepository): Response
    {
        $books = $bookRepository->listBooksBefore2023();

        return $this->render('book/book35.html.twig', [
            'books' => $books,
        ]);
    }
    #[Route('/updateCategory', name: 'updateCategory')]
    public function updateCategory(BookRepository $bookRepository, ManagerRegistry $managerRegistry)
    {
        $entityManager =$managerRegistry->getManager();

        $williamShakespeareBooks = $bookRepository->updateCategory();

        foreach ($williamShakespeareBooks as $book) {
            $book->setCategory('Romance');
            $entityManager->persist($book);
        }

        $entityManager->flush();

        return $this->redirectToRoute('showdbbook'); 
    }

    #[Route('/showpubbetdates')]
    function showTitleBook(BookRepository $repo){
        $titles=$repo->findBookByPublicationDate();
        return $this->render('book/showpubbetdates.html.twig', [
            'book' => $titles,
        ]);
    }


    
    
}    
    
