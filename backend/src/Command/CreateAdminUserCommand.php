<?php

namespace App\Command;

use App\Entity\Role\Role;
use App\Entity\User\User;
use App\Repository\Role\RoleRepository;
use App\Repository\User\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:create-admin-user',
    description: 'Add a short description for your command',
)]
class CreateAdminUserCommand extends Command
{


    private \Doctrine\ORM\EntityRepository | UserRepository $userRepository;
    private \Doctrine\ORM\EntityRepository | RoleRepository $roleRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct('app:create-admin-user');

        $this->userRepository = $entityManager->getRepository(User::class);
        $this->roleRepository = $entityManager->getRepository(Role::class);
    }

    protected function configure(): void
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $roleAdmin = $this->roleRepository->findOneBy(['name' => 'ROLE_ADMIN']);

        $usernameCheck = false;
        $passwordCheck = false;
        $repasswordCheck = false;
        $emailCheck = false;

        do {
            $username = $io->ask('Enter your Username (used for login):');

            if(!$this->userRepository->findOneBy(['username' => $username])){
                $usernameCheck = true;
            }else{
                $io->error('This username already exist.');
            }

        }while(!$usernameCheck);

        do {

            $password = $io->askHidden('Enter your Password:');

            if(preg_match('/^(?=(?:.*\d))(?=.*[A-Z])(?=.*[a-z])(?=.*[.,*!?¿¡#$%&])\S{8,64}$/', $password)){
                $passwordCheck = true;
            }else{
                $io->error('The password must have at between 8 and 64 characters, one Uppercase character, one Lowercase character, a number and a special character.');
            }

        }while(!$passwordCheck);

        do {

            $repassword = $io->askHidden('Repeat your Password:');

            if($password == $repassword){
                $repasswordCheck = true;
            }else{
                $io->error("The passwords doesn't match.");
            }

        }while(!$repasswordCheck);

        do {

            $email = $io->ask('Enter your Email:');

            if(filter_var($email, FILTER_VALIDATE_EMAIL)){
                $emailCheck = true;
            }else{
                $io->error('The password must have at between 8 and 64 characters, one Uppercase character, one Lowercase character, a number and a special character.');
            }

        }while(!$emailCheck);

        $this->userRepository->createUser(
            $username,
            $password,
            $email,
            [$roleAdmin]
        );

        $io->success('The user has been successfully registered.');

        return Command::SUCCESS;
    }
}
