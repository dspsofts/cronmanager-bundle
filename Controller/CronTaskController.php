<?php

/**
 *
 * @author Pierre Feyssaguet <pfeyssaguet@gmail.com>
 * @since 01/11/15 12:59
 */

namespace DspSofts\CronManagerBundle\Controller;

use DspSofts\CronManagerBundle\Entity\CronTask;
use DspSofts\CronManagerBundle\Entity\CronTaskLog;
use DspSofts\CronManagerBundle\Form\CronTaskType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/crontasks")
 */
class CronTaskController extends Controller
{
	public function helloAction()
	{
		return new Response("Hello world");
	}

	/**
	 * @Route("/test", name="dsp_cm_crontasks_test")
	 */
	public function testAction()
	{
		$entity = new CronTask();

		$entity
			->setName('Example asset symlinking task')
			->setPlanification('* * * * *') // Run once every hour
			->setCommands(array(
				'assets:install --symlink web'
			));

		$em = $this->getDoctrine()->getManager();
		$em->persist($entity);
		$em->flush();

		return new Response('OK!');
	}

	/**
	 * @Route("/list", name="dsp_cm_crontasks_list")
	 */
	public function listAction()
	{
		$em = $this->getDoctrine()->getManager();
		$cronTaskRepo = $em->getRepository('DspSoftsCronManagerBundle:CronTask');

		$cronTaskList = $cronTaskRepo->findAll();

		return $this->render('@DspSoftsCronManager/CronTask/list.html.twig', array('cronTaskList' => $cronTaskList));
	}

	/**
	 * @Route("/log", name="dsp_cm_crontasks_log")
	 */
	public function logAction()
	{
		$em = $this->getDoctrine()->getManager();
		$cronTaskLogRepo = $em->getRepository('DspSoftsCronManagerBundle:CronTaskLog');

		$cronTaskLogList = $cronTaskLogRepo->findAll();

		return $this->render('@DspSoftsCronManager/CronTask/log.html.twig', array('cronTaskLogList' => $cronTaskLogList));
	}

	/**
	 * @Route("/log/{cronTaskLog}", name="dsp_cm_crontasks_log_view")
	 */
	public function logViewAction(CronTaskLog $cronTaskLog)
	{
		$logDir = $this->getParameter('dsp_softs_cron_manager.logs_dir');
		$filePath = $logDir . $cronTaskLog->getFilePath();
		return new Response(file_get_contents($filePath) , 200, array('Content-type' => 'text/plain'));
	}

	/**
	 * @Route("/add", name="dsp_cm_crontasks_add")
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
	 * @Route("/{cronTask}", name="dsp_cm_crontasks_edit")
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
}
