<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Photo;

#[AsCommand(name: 'app:photo-visible-false', description: 'Disable visibility of all photos for private access')]
class DisablePhotosVisibilityCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('user', InputArgument::REQUIRED, 'User ID');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $userId = $input->getArgument('user');
        $photoRepository = $this->entityManager->getRepository(Photo::class);
        $photosSetToPrivate = $photoRepository->findBy(['is_public' => true, 'user' => $userId]);

        foreach ($photosSetToPrivate as $publicPhoto) {
            $publicPhoto->setIsPublic(false);
            $this->entityManager->persist($publicPhoto);
        }

        $this->entityManager->flush();

        return Command::SUCCESS;
    }
}
