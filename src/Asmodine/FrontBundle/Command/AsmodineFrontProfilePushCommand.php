<?php

namespace Asmodine\FrontBundle\Command;

use Asmodine\FrontBundle\Entity\User;
use Asmodine\FrontBundle\Repository\UserRepository;
use Asmodine\FrontBundle\Service\BackApiService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class AsmodineFrontProfilePushCommand.
 */
class AsmodineFrontProfilePushCommand extends ContainerAwareCommand
{
    /**
     * Configure.
     */
    protected function configure()
    {
        $this
            ->setName('asmodine:front:profile:push')
            ->setDescription('Pousse les profils physiques vers le back (saisir id OU email OU rien pour tous)')
            ->addOption('id', null, InputOption::VALUE_OPTIONAL, 'Identifiant de l\'utilisateur à pousser.')
            ->addOption('email', null, InputOption::VALUE_OPTIONAL, 'email de l\'utilisateur à pousser.')
            ->addOption('wait', null, InputOption::VALUE_OPTIONAL, 'Temps en secondes entre chaque "push".');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var UserRepository $userRepo */
        $userRepo = $this->getContainer()->get('doctrine')->getRepository('AsmodineFrontBundle:User');
        $physicalProfileRepo = $this->getContainer()->get('doctrine')->getRepository('AsmodineFrontBundle:PhysicalProfile');

        $id = $input->getOption('id');
        $email = $input->getOption('email');
        $wait = $input->getOption('wait');
        $sleep = 60;
        if ($wait && intval($wait) == $wait) {
            $sleep = intval($wait);
        }
        if ($id || $email) {
            $sleep = 0;
            if ($id) {
                $ids = [$id];
            } else {
                $user = $userRepo->findOneBy(['email' => $email]);
                if (is_null($user)) {
                    return;
                }
                $ids = [$user->getId()];
            }
        } else {
            $users = $userRepo->findAll();
            $ids = [];
            /** @var User $user */
            foreach ($users as $user) {
                $ids[] = $user->getId();
            }
        }

        $output->writeln('Envoi de '.count($ids).' "PhysicalProfile"');

        /** @var BackApiService $apiBack */
        $apiBack = $this->getContainer()->get('asmodine.front.back_api');

        for ($i = 0; $i < count($ids); ++$i) {
            $userId = $ids[$i];
            $user = $userRepo->find($userId);
            $pp = $physicalProfileRepo->findOneBy(['user' => $user]);
            if (is_null($pp)) {
                $output->writeln('idUser:'.str_pad($user->getId(), 5).' | idProfile: null');
            } else {
                $output->writeln('idUser:'.str_pad($user->getId(), 5).' | idProfile:'.str_pad($pp->getId(), 5).' | Email:'.$user->getEmail().' - '.$pp->getMorphoprofile().' '.$pp->getMorphotype());
                $output->writeln('   => '.$apiBack->updatePhysicalProfile($pp));
                sleep($sleep);
            }
        }
    }
}
