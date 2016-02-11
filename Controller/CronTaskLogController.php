<?php

/**
 *
 * @author Pierre Feyssaguet <pfeyssaguet@gmail.com>
 * @since 10/11/15 08:37
 */

namespace DspSofts\CronManagerBundle\Controller;

use DspSofts\CronManagerBundle\Entity\CronTask;
use DspSofts\CronManagerBundle\Entity\CronTaskLog;
use DspSofts\CronManagerBundle\Entity\Repository\CronTaskLogRepository;
use DspSofts\CronManagerBundle\Entity\Search\CronTaskLogSearch;
use DspSofts\CronManagerBundle\Form\CronTaskLogSearchType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Process;

class CronTaskLogController extends Controller
{
    public function logAction(Request $request)
    {
        $cronTaskLogSearch = new CronTaskLogSearch();

        $formSearch = $this->createForm(new CronTaskLogSearchType(), $cronTaskLogSearch);
        if ($request->isMethod('POST')) {
            $formSearch->handleRequest($request);
        }

        $em = $this->getDoctrine()->getManager();
        /** @var CronTaskLogRepository $cronTaskLogRepo */
        $cronTaskLogRepo = $em->getRepository('DspSoftsCronManagerBundle:CronTaskLog');

        $cronTaskLogRunningList = $cronTaskLogRepo->searchRunning($cronTaskLogSearch);
        $cronTaskLogFinishedList = $cronTaskLogRepo->searchFinished($cronTaskLogSearch);

        $cronTaskLogList = array_merge($cronTaskLogRunningList, $cronTaskLogFinishedList);

        return $this->render('@DspSoftsCronManager/CronTaskLog/log.html.twig', array(
            'cronTaskLogList' => $cronTaskLogList,
            'formSearch' => $formSearch->createView(),
        ));
    }

    public function logViewAction(CronTaskLog $cronTaskLog)
    {
        $logDir = $this->getParameter('dsp_softs_cron_manager.logs_dir');
        $filePath = $logDir . $cronTaskLog->getFilePath();

        return new Response(file_get_contents($filePath), 200, array('Content-type' => 'text/plain'));
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

    public function relaunchAction(CronTask $cronTask)
    {
        $em = $this->getDoctrine()->getManager();
        $cronTask->setRelaunch(true);
        $em->persist($cronTask);
        $em->flush();

        return $this->redirectToRoute('dsp_cm_crontasks_log');
    }
}
