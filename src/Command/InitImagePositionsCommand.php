<?php

namespace App\Command;

use App\Repository\GalleryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:init-image-positions',
    description: 'Initialise les positions des images existantes',
)]
class InitImagePositionsCommand extends Command
{
    public function __construct(
        private readonly GalleryRepository $galleryRepository,
        private readonly EntityManagerInterface $entityManager
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $galleries = $this->galleryRepository->findAll();
        $totalUpdated = 0;

        foreach ($galleries as $gallery) {
            $position = 1;
            foreach ($gallery->getImages() as $image) {
                if ($image->getPosition() === null) {
                    $image->setPosition($position);
                    $totalUpdated++;
                }
                $position++;
            }
        }

        $this->entityManager->flush();

        $io->success(sprintf('Positions initialisées pour %d images.', $totalUpdated));

        return Command::SUCCESS;
    }
}