<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PromoteUserCommand extends Command
{
    protected static $defaultName = 'app:promote-user';
    protected static $defaultDescription = 'Make an User Admin';
    private UserRepository $userRepository;
    private ManagerRegistry $registry;

    /**
     * PromoteUserCommand constructor.
     */
    public function __construct(string $name = null, UserRepository $userRepository, ManagerRegistry $registry)
    {
        parent::__construct($name);
        $this->userRepository = $userRepository;
        $this->registry = $registry;
    }


    protected function configure()
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addArgument('email', InputArgument::REQUIRED, 'User Email')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');

        /**
         * @var User $user
         */
        $user = $this->userRepository->findOneBy(['email' => $email]);
        if(!$user)
        {
            $io->error(sprintf('The user %s does not exist', $email));
            return Command::FAILURE;
        }

        $user->setHasRoleAdmin(true);
        $this->registry->getManager()->persist($user);
        $this->registry->getManager()->flush();


        $io->success('You have a new admin user!');

        return Command::SUCCESS;
    }
}
