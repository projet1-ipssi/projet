<?php

namespace App\Command;

use App\Manager\UsersManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CountEventCommand extends Command
{
    protected static $defaultName = 'app:user-count-video';
    private $userManager;

    public function __construct(UsersManager $userManager)
    {
        $this->userManager = $userManager;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('email', InputArgument::REQUIRED, 'email description');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $nomberVideo = $this->userManager->getNumberVideo($email);

        if ($email) {
            $io->note(sprintf('Searche video for user : %s', $email));
            $io->success(sprintf('Found: %s video', $nomberVideo));
        } else {
            $io->error(sprintf('Erreur ! Email %s do not existe', $email));
        }
    }
}
