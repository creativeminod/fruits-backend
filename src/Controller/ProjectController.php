<?php
 
namespace App\Controller;
 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Project;
// use Symfony\Component\Mime\Email;
use App\Entity\Fruits;
 
/**
 * @Route("/api", name="api_")
 */
 
class ProjectController extends AbstractController
{
 

    /**
    * @Route("/all", name="project_index", methods={"GET"})
    */
    public function index(ManagerRegistry $doctrine,Request $request): Response
    {
       
        $this->limit=10;

        $this->totalCount=0;

        $totalCount = $doctrine
            ->getRepository(Fruits::class)
            ->findAll();

        $this->offset=$request->query->get('page') * $this->limit;


        $products = $doctrine
            ->getRepository(Fruits::class)
            ->findBy(
                ($request->query->get('title')?['name'=>$request->query->get('title')]:[]), [], $this->limit, $this->offset
            );

        if($request->query->get('title'))
        {
            // $this->arystr=['name'=>$request->query->get('title')];

            $this->totalCount=count($products);
        }
        else
        {
            // $this->arystr=[];
            $this->totalCount=count($totalCount);
        }

        $data = [];

        foreach ($products as $product) {
           $data[] = [
               'id' => $product->getId(),
               'name' => $product->getName(),
               'fruit_id' => $product->getFruitId(),
               'family' => $product->getFamily(),
               'fruit_order' => $product->getFruitOrder(),
               'genus' => $product->getGenus(),
               'nutritions' => json_decode($product->getNutritions()),
               'favorite_status' => $product->isFavoriteStatus(),
           ];
        }

        return $this->json(array('totalItems'=>$this->totalCount,'items'=>$data));
    }
 
  
    /**
     * @Route("/add", name="project_new", methods={"GET"})
     */
    public function new(EntityManagerInterface $entityManager): Response
    {   

        die("successfully added function");
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://fruityvice.com/api/fruit/all');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'authority: fruityvice.com'
        ]);

        $response = curl_exec($ch);

        curl_close($ch);

        $obj = json_decode($response, TRUE);

        foreach ($obj as $value)
        {
           $fruits = new Fruits();

           $fruits->setName($value['name']);

           $fruits->setFruitId($value['id']);

           $fruits->setFamily($value['family']);

           $fruits->setFruitOrder($value['order']);

           $fruits->setGenus($value['genus']);

           $NutritionsJson=json_encode($value['nutritions']);

           $fruits->setNutritions($NutritionsJson);

           $fruits->setFavoriteStatus(false);

           $entityManager->persist($fruits);

        }

        $entityManager->flush();

        return $this->json('Created new project successfully with id');
    }
  
    /**
     * @Route("/total_sum", name="project_show", methods={"GET"})
     */
    public function show(ManagerRegistry $doctrine): Response
    {
        $fruits = $doctrine
            ->getRepository(Fruits::class)
            ->findAll();

        $CaloriesSum = 0;

        $FatSum = 0;

        $SugarSum = 0;

        $CarbohydratesSum = 0;

        $ProteinSum = 0;
        
        foreach ($fruits as $value)
        {
            $NutritionsAry=json_decode($value->getNutritions());

            $CaloriesSum+= $NutritionsAry->calories;

            $FatSum+= $NutritionsAry->fat;

            $SugarSum+= $NutritionsAry->sugar;

            $CarbohydratesSum+= $NutritionsAry->carbohydrates;

            $ProteinSum+= $NutritionsAry->protein;
        }
        
        $response = [
            'calories' => round($CaloriesSum),

            'fat' => round($FatSum),

            'sugar' => round($SugarSum),

            'carbohydrates' => round($CarbohydratesSum),

            'protein' => round($ProteinSum)
        ];

        return $this->json($response);
    }

    /**
     * @Route("/addfav", name="addToFavd", methods={"POST"})
    */

     public function addfav(ManagerRegistry $doctrine, Request $request): Response
    {
        $totalCount = $doctrine->getRepository(Fruits::class)
                    ->findBy(
                        ['favorite_status'=>true]                        

                    );
        if(count($totalCount)==10)
        {
            die('You can add onlyl 10 favorite fruit');
        }
        else
        {
            $data = json_decode($request->getContent(), true);

            $entityManager = $doctrine->getManager();

            $fruit = $entityManager->getRepository(Fruits::class)
                    ->findBy(
                        array('fruit_id' => $data['fruit_id'])
                    );

            $fruit[0]->setFavoriteStatus(true);

            $entityManager->flush();

            die('done');
        }
    }

    /**
     * @Route("/removefav", name="removeToFav", methods={"POST"})
    */

     public function removefav(ManagerRegistry $doctrine, Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $entityManager = $doctrine->getManager();

        $fruit = $entityManager->getRepository(Fruits::class)
                ->findBy(
                    array('fruit_id' => $data['fruit_id'])
                );

        $fruit[0]->setFavoriteStatus(false);

        $entityManager->flush();

        die('done');
    }

    /**
    * @Route("/showfav", name="addToFav", methods={"GET"})
    */

     public function showfav(ManagerRegistry $doctrine,Request $request): Response
    {
        // favorite_status
        // die('df');

        $this->limit=10;

        $this->totalCount=0;

        $this->calorie=0;

        $this->fat=0;

        $this->sugar=0;

        $this->carbohydrates=0;

        $this->protein=0;

        $totalCount = $doctrine->getRepository(Fruits::class)
                    ->findBy(
                        ($request->query->get('title')?['favorite_status'=>true,'name'=>$request->query->get('title')]:['favorite_status'=>true,])                        

                    );

        $this->offset=$request->query->get('page') * $this->limit;

        $fruits = $doctrine->getRepository(Fruits::class)
                    ->findBy(
                        $request->query->get('title')?['favorite_status'=>true,'name'=>$request->query->get('title')]:['favorite_status'=>true,],
                        [],
                        $this->limit, $this->offset
                    );

        $data = [];

        foreach ($fruits as $fruit) {

            $nutritions=json_decode($fruit->getNutritions());

            $this->calorie+= $nutritions->calories;

            $this->fat+= $nutritions->fat;

            $this->sugar+= $nutritions->sugar;

            $this->carbohydrates+= $nutritions->carbohydrates;

            $this->protein+= $nutritions->protein;
            
           $data[] = [
               'id' => $fruit->getId(),
               'name' => $fruit->getName(),
               'fruit_id' => $fruit->getFruitId(),
               'family' => $fruit->getFamily(),
               'fruit_order' => $fruit->getFruitOrder(),
               'genus' => $fruit->getGenus(),
               'nutritions' => json_decode($fruit->getNutritions()),
               'favorite_status' => $fruit->isFavoriteStatus(),
           ];
        }

        $nutritionsAry = [
            'calories' => round($this->calorie),

            'fat' => round($this->fat),

            'sugar' => round($this->sugar),

            'carbohydrates' => round($this->carbohydrates),

            'protein' => round($this->protein)
        ];
        array_push($data,
            array(
                'id' => '',
                'name' => '',
                'fruit_id' => '',
                'family' => '',
                'fruit_order' => '',
                'genus' => '',
                'nutritions' => $nutritionsAry,
                'favorite_status' => 'end'
            )
        );

        return $this->json(array('totalItems'=>count($totalCount),'items'=>$data));

    }    

}
