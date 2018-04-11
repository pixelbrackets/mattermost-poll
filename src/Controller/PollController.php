<?php

namespace App\Controller;

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
    public function new(Request $request)
    {
        $requestContent = $request->getContent() ?: [];
        $json = json_decode($requestContent, true);
        if ($request->getContentType() === 'form') {
            // Fix because mattermost sends slash commands as
            // »application/x-www-form-urlencoded« instead of application/json
            // Issue: https://github.com/mattermost/mattermost-server/issues/1649
            $json = $request->request->all();
        }

        if (false === isset($json['channel_id'], $json['user_id'], $json['text'])) {
            return $this->json([
                'response_type' => 'ephemeral',
                'text' => 'Missing required parameters'
            ]);
        }

        $text = 'Hello World';
        if ($json['channel_id'] === 'niah6qa') {
            $text = 'Hi Team! 😀';
        }

        return $this->json([
            'response_type' => 'in_channel',
            'text' => $text
        ]);
    }
}
