<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\HttpClient;

/**
 * @Route(host="%app.helper_domain%")
 */
class EmailsQueueController extends AbstractController
{
    /**
     * @Route("/emails/queue", name="emails_queue")
     */
    public function index()
    {


$client = HttpClient::create();
$response = $client->request('GET', 'https://api.github.com/repos/symfony/symfony-docs');

$statusCode = $response->getStatusCode();
// $statusCode = 200
$contentType = $response->getHeaders()['content-type'][0];
// $contentType = 'application/json'
$content = $response->getContent();
// $content = '{"id":521583, "name":"symfony-docs", ...}'
$content = $response->toArray();

        return $this->render('emails_queue/index.html.twig', [
            'controller_name' => 'EmailsQueueController',
        ]);
    }

    /**
     * @Route("/emails/queue/consume", name="emails_queue_consume")
     */
    public function consume(KernelInterface $kernel)
    {
        set_time_limit(120);

         $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'messenger:consume',
            '--limit' => '5',
            '--memory-limit' => '64M',
            '--time-limit' => '40',
            'async',
            'failed'
            // (optional) define the value of command arguments
//            '--dump-sql',
//            '--force'=>'--force'
        ]);

        // You can use NullOutput() if you don't need the output
        $output = new NullOutput();
        $application->run($input, $output);

        // return the output, don't use if you used NullOutput()
//        $content = $output->fetch();

        // return new Response(""), if you used NullOutput()
        return new Response();
    }
}
