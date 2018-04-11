<?php

namespace App\Controller;

use App\Entity\Poll;
use App\Entity\Answer;
use App\Entity\Vote;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Flex\Response;

class PollController extends Controller
{
    /**
     * @Route("/", name="root")
     */
    public function index()
    {
        return $this->json([
            'meta' => [
                'description' => 'Available endpoints in this app',
                'resources' => [
                    'poll' => $this->generateUrl('poll-new')
                ]
            ],
            'links' => [
                'self' => $this->generateUrl('root')
            ]
        ]);
    }

    /**
     * Create poll with incoming slashcommand webhook
     *
     * @Route("/new", name="poll-new", methods="POST")
     */
    public function new(Request $request, EntityManagerInterface $entityManager, \Psr\Log\LoggerInterface $logger)
    {
        $requestContent = $request->getContent() ?: [];
        $json = json_decode($requestContent, true);
        if ($request->getContentType() === 'form') {
            // Fix because mattermost sends slash commands as
            // »application/x-www-form-urlencoded« instead of application/json
            // Issue: https://github.com/mattermost/mattermost-server/issues/1649
            $json = $request->request->all();
        }

        $logger->info('New Poll');
        $logger->debug('Incoming slash command request', [$json]);

        if (false === isset($json['channel_id'], $json['user_id'], $json['text'])) {
            return $this->json([
                'response_type' => 'ephemeral',
                'text' => 'Missing required parameters'
            ]);
        }

        $pollCommand = str_getcsv($json['text'], ' ');

        $poll = new Poll();
        $poll->setTitle($pollCommand[0]);
        $poll->setCreationDate();
        $poll->setModificationDate(new \DateTime());
        $poll->setVisibility(true);

        $text = 'Poll »' . $pollCommand[0] . '« opened' . PHP_EOL;

        foreach ($pollCommand as $key => $pollCommandAnswer) {
            if ($key < 1) {
                // skip poll slash command and title
                continue;
            }
            $answer = new Answer();
            $answer->setTitle($pollCommandAnswer);
            $entityManager->persist($answer);

            $poll->addAnswer($answer);

            $text .= '  * ' . $pollCommandAnswer . PHP_EOL;
        }

        $entityManager->persist($poll);
        $entityManager->flush();

        return $this->json([
            'response_type' => 'ephemeral',
            'text' => $text,
        ]);
    }

    /**
     * Vote for an answer in a poll from incoming interactice message buttons
     *
     * @Route("/vote", name="poll-vote", methods="POST")
     */
    public function vote(Request $request, EntityManagerInterface $entityManager, \Psr\Log\LoggerInterface $logger)
    {
        $requestContent = $request->getContent() ?: [];
        $json = json_decode($requestContent, true);
        if ($request->getContentType() === 'form') {
            // Fix because mattermost sends slash commands as
            // »application/x-www-form-urlencoded« instead of application/json
            // Issue: https://github.com/mattermost/mattermost-server/issues/1649
            $json = $request->request->all();
        }

        $logger->info('Voting');
        $logger->debug('Incoming button request', [$json]);

        if (false === isset($json['channel_id'], $json['user_id'], $json['context']['answer'])) {
            return $this->json([
                'response_type' => 'ephemeral',
                'text' => 'Missing required parameters'
            ]);
        }

        $answer = $this->getDoctrine()
            ->getRepository(Answer::class)
            ->find((int)$json['context']['answer']);

        $poll = $this->getDoctrine()
            ->getRepository(Poll::class)
            ->findOneByIdJoinedToAnswer((int)$json['context']['answer']);

        if ($answer === NULL || $poll === NULL || $poll->getVisibility() === false) {
            return $this->json([
                'response_type' => 'ephemeral',
                'text' => 'Poll not valid (forged answer/poll ID, poll closed already)'
            ]);
        }

        $existingVote = $this->getDoctrine()
            ->getRepository(Vote::class)
            ->findBy([
                'answer' => (int)$json['context']['answer'],
                'user' => $json['user_id']
            ]);

        $logger->debug('Existing Vote', [$existingVote]);

        if (false === empty($existingVote)) {
            return $this->json([
                'response_type' => 'ephemeral',
                'text' => 'Already voted for this answer'
            ]);
        }

        $vote = new Vote();
        $vote->setAnswer($answer);
        $vote->setUser($json['user_id']);
        $entityManager->persist($vote);
        $entityManager->flush();

        // prepare view
        $text = 'Voting for poll »' . $poll->getTitle() . '« successful';
        return $this->json([
            'response_type' => 'ephemeral',
            'text' => $text
        ]);
    }

    /**
     * End voting for poll from incoming interactice message buttons
     *
     * @Route("/close", name="poll-close", methods="POST")
     */
    public function close(Request $request, EntityManagerInterface $entityManager, \Psr\Log\LoggerInterface $logger)
    {
        $requestContent = $request->getContent() ?: [];
        $json = json_decode($requestContent, true);
        if ($request->getContentType() === 'form') {
            // Fix because mattermost sends slash commands as
            // »application/x-www-form-urlencoded« instead of application/json
            // Issue: https://github.com/mattermost/mattermost-server/issues/1649
            $json = $request->request->all();
        }

        $logger->info('Close poll');
        $logger->debug('Incoming button request', [$json]);

        if (false === isset($json['channel_id'], $json['user_id'], $json['context']['poll'])) {
            return $this->json([
                'response_type' => 'ephemeral',
                'text' => 'Missing required parameters'
            ]);
        }

        $poll = $this->getDoctrine()
            ->getRepository(Poll::class)
            ->findOneBy(['uid' => (int)$json['context']['poll']]);

        if ($poll === NULL || $poll->getVisibility() === false) {
            return $this->json([
                'response_type' => 'ephemeral',
                'text' => 'Poll not valid (forged poll ID, poll closed already)'
            ]);
        }

        $logger->debug('Existing Poll', [$poll]);

        // prepare view
        $text = 'Poll »' . $poll->getTitle() . '« closed' . PHP_EOL;
        $answers = $poll->getAnswers();
        foreach ($answers as $answer) {
            $logger->debug('Answer', [$answer->getUid()]);

            $voteCount = $this->getDoctrine()
                ->getRepository(Vote::class)
                ->countByAnswer($answer->getUid());

            $text .= '  * ' . $answer->getTitle() . ' – ' . $voteCount . 'x'. PHP_EOL;
            $logger->debug('Votes for answer ' . $answer->getTitle(), [$voteCount]);
        }

        $poll->setModificationDate(new \DateTime());
        $poll->setVisibility(false);
        $entityManager->persist($poll);
        $entityManager->flush();

        return $this->json([
            'update' => [
                'message' => $text
            ]
        ]);
    }
}
