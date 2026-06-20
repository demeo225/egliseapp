<?php


namespace App\Command;

use App\Repository\CulteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CleanExpiredTokensCommand extends Command
{
    protected static $defaultName = 'app:clean:expired-tokens';

    private EntityManagerInterface $entityManager;
    private CulteRepository $culteRepository;

    public function __construct(EntityManagerInterface $entityManager, CulteRepository $culteRepository)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->culteRepository = $culteRepository;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Nettoie les tokens de présence expirés')
            ->setHelp('Cette commande supprime les tokens de présence expirés pour les cultes');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $cultes = $this->culteRepository->findAll();
        $count = 0;

        foreach ($cultes as $culte) {
            if ($culte->getTokenPresence() && $culte->isTokenExpired()) {
                // Supprimer le token expiré
                $culte->setTokenPresence(null);
                $culte->setDateExpirationQr(null);
                $count++;
            }
        }

        $this->entityManager->flush();

        $io->success("$count token(s) expiré(s) supprimé(s)");

        return Command::SUCCESS;
    }
}