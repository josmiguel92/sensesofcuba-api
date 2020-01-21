<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

class MigrationController extends AbstractController
{
    /**
     * @Route("/admin/migration", name="migration")
     */
    public function index()
    {
        if(!$this->isGranted('ROLE_SUPER_ADMIN'))
            throw new AccessDeniedHttpException();

        return $this->render('migration/index.html.twig', [
            'controller_name' => 'MigrationController',
        ]);
    }
//
//    /**
//     * @Route("/admin/migration/db/mig", name="migration_db_mig")
//     * @param KernelInterface $kernel
//     * @return Response
//     * @throws \Exception
//     */
//    public function dbAplyMigrations(KernelInterface $kernel)
//    {
////        echo "hola";
//        if(!$this->isGranted('ROLE_SUPER_ADMIN'))
//            throw new AccessDeniedHttpException();
//
//        $application = new Application($kernel);
//        $application->setAutoExit(false);
//
//        $input = new ArrayInput([
//            'command' => 'doc:mig:mig',
//            '--no-interaction' => '--no-interaction',
//            // (optional) define the value of command arguments
////            '--dump-sql',
////            '--force'=>'--force'
//        ]);
//
//        // You can use NullOutput() if you don't need the output
//        $output = new BufferedOutput();
//        $application->run($input, $output);
//
//        // return the output, don't use if you used NullOutput()
//        $content = $output->fetch();
//
//        // return new Response(""), if you used NullOutput()
//        return new Response($content);
//    }



}
