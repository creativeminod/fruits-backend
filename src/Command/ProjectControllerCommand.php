<?php

namespace App\Command;
use App\Entity\Fruits;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Dotenv\Dotenv;


class ProjectControllerCommand extends Command
{
     

    public function __construct(ManagerRegistry $doctrine,private EntityManagerInterface $entityManager, private MailerInterface $mailer)
    {
        parent::__construct();
    }

    

    protected function configure(): void
    {
        // Use in-build functions to set name, description and help

        $this->setName('fruits:fetch')
        ->setDescription('This command runs your custom task')
        ->setHelp('Run this command to execute your custom tasks in the execute function.')
        ->addArgument('param', InputArgument::OPTIONAL, 'Pass the parameter.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        
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

           $this->entityManager->persist($fruits);

        }

        $this->entityManager->flush();

         $email = (new Email())
            ->from($_ENV['MAIL_FROM'])
            ->to($_ENV['MAIL_TO'])
            ->subject($_ENV['MAIL_SUBJECT'])
            ->html('<p>'.$_ENV['MAIL_TEXT'].'</p>');

        $this->mailer->send($email);

        return Command::SUCCESS;
    }
}