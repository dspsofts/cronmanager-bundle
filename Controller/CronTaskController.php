<?php

/**
 *
 * @author Pierre Feyssaguet <pfeyssaguet@gmail.com>
 * @since 01/11/15 12:59
 */

namespace DspSofts\CronManagerBundle\Controller;

use DspSofts\CronManagerBundle\Entity\CronTask;
use DspSofts\CronManagerBundle\Entity\CronTaskLog;
use DspSofts\CronManagerBundle\Entity\Repository\CronTaskLogRepository;
use DspSofts\CronManagerBundle\Form\CronTaskType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Process;

class CronTaskController extends Controller
{
    public function helloAction()
    {
        return new Response("Hello world");
    }

    public function testAction()
    {
        $entity = new CronTask();

        $entity
            ->setName('Example asset symlinking task')
            ->setPlanification('* * * * *')// Run once every hour
            ->setCommand('assets:install --symlink web');

        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();

        return new Response('OK!');
    }

    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        $cronTaskRepo = $em->getRepository('DspSoftsCronManagerBundle:CronTask');

        $cronTaskList = $cronTaskRepo->findAll();

        return $this->render('@DspSoftsCronManager/CronTask/list.html.twig', array('cronTaskList' => $cronTaskList));
    }

    public function logAction(Request $request)
    {
        $dateStart = $request->get('dateStart');
        if ($dateStart === null) {
            $dateStart = new \DateTime();
        } else {
            $dateStart = \DateTime::createFromFormat('Y-m-d', $dateStart);
        }
        $dateStart->setTime(0, 0, 0);

        $em = $this->getDoctrine()->getManager();
        /** @var CronTaskLogRepository $cronTaskLogRepo */
        $cronTaskLogRepo = $em->getRepository('DspSoftsCronManagerBundle:CronTaskLog');

        $cronTaskLogRunningList = $cronTaskLogRepo->findByPidNotNull();
        $cronTaskLogFinishedList = $cronTaskLogRepo->findFinishedByDate($dateStart);

        $cronTaskLogList = array_merge($cronTaskLogRunningList, $cronTaskLogFinishedList);

        return $this->render('@DspSoftsCronManager/CronTask/log.html.twig', array(
            'dateStart' => $dateStart,
            'cronTaskLogList' => $cronTaskLogList,
        ));
    }

    public function logViewAction(CronTaskLog $cronTaskLog)
    {
        $logDir = $this->getParameter('dsp_softs_cron_manager.logs_dir');
        $filePath = $logDir . $cronTaskLog->getFilePath();

        return new Response(file_get_contents($filePath), 200, array('Content-type' => 'text/plain'));
    }

    /**
     *
     * @param Request $request
     * @return Response
     */
    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $cronTask = new CronTask();
        $form = $this->createForm(new CronTaskType(), $cronTask);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em->persist($cronTask);
                $em->flush();

                $request->getSession()->getFlashBag()->add(
                    'info',
                    'Task ' . $cronTask->getName() . ' créée avec succès.'
                );

                return $this->redirect($this->generateUrl('dsp_cm_crontasks_list'));
            }
        }

        return $this->render('@DspSoftsCronManager/CronTask/form.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     *
     * @param Request $request
     * @param CronTask $cronTask
     * @return Response
     */
    public function editAction(Request $request, CronTask $cronTask)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(new CronTaskType(), $cronTask);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em->persist($cronTask);
                $em->flush();

                $request->getSession()->getFlashBag()->add(
                    'info',
                    'Task ' . $cronTask->getName() . ' mise à jour avec succès.'
                );

                return $this->redirect($this->generateUrl('dsp_cm_crontasks_list'));
            }
        }

        return $this->render('@DspSoftsCronManager/CronTask/form.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function killAction(CronTaskLog $cronTaskLog)
    {
        $process = new Process('kill -kill ' . $cronTaskLog->getPid());
        $exitCode = $process->run();
        if ($exitCode !== 0) {
            return new Response("Exit code $exitCode " . $process->getOutput() . $process->getErrorOutput());
        }
        return $this->redirect($this->generateUrl('dsp_cm_crontasks_log'));
    }
}
