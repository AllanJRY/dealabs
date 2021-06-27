<?php

namespace App\Command;

use App\Service\Mailer;
use App\Repository\DealRepository;
use App\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class MailDealDayAlarmCommand extends Command
{
    protected static $defaultName = 'app:mail-deal-day-alarm:send';
    protected static $defaultDescription = 'Send email at 00:00 with all deal of the day by alarm user';
    /**
     * @var DealRepository
     */
    private $dealRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var Mailer
     */
    private $mailer;
    /**
     * @var ParameterBagInterface
     */
    private $params;

    public function __construct(DealRepository $dealRepository, UserRepository $userRepository, Mailer $mailer, ParameterBagInterface $params)
    {
        parent::__construct(null);
        $this->dealRepository = $dealRepository;
        $this->userRepository = $userRepository;
        $this->mailer = $mailer;
        $this->params = $params;
    }

    protected function configure(): void
    {
        $this->setDescription(self::$defaultDescription);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $users = $this->userRepository->findAll();
        $io->progressStart(count($users));
        foreach ($users as $user) {
            $io->progressAdvance();
            $deals = $this->dealRepository->findNewDealOneDayByAlarmUserOrderByDateDesc($user);
            if (count($deals) > 0 and $user != null) {
                $io->writeln(count($deals));
                $this->mailer->send('mail/deal/alarm_deals.html.twig',
                    [
                        'deals' => $deals,
                    ],
                    $this->params->get('admin_mail'),
                    $user->getEmail(),
                );
            }
        }

        $io->progressFinish();

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return 0;
    }
}
