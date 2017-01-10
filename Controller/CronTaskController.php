<?php

/**
 *
 * @author Pierre Feyssaguet <pfeyssaguet@gmail.com>
 * @since 01/11/15 12:59
 */

namespace DspSofts\CronManagerBundle\Controller;

use DspSofts\CronManagerBundle\Entity\CronTask;
use DspSofts\CronManagerBundle\Form\CronTaskType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CronTaskController extends Controller
{
    public function helloAction()
    {
        return new Response("Hello world");
    }

    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        $cronTaskRepo = $em->getRepository('DspSoftsCronManagerBundle:CronTask');

        $cronTaskList = $cronTaskRepo->findAll();

        return $this->render('@DspSoftsCronManager/CronTask/list.html.twig', array('cronTaskList' => $cronTaskList));
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
        $form = $this->createForm(CronTaskType::class, $cronTask);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em->persist($cronTask);
                $em->flush();

                $request->getSession()->getFlashBag()->add(
                    'info',
                    sprintf('Task %s créée avec succès.', $cronTask->getName())
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

        $form = $this->createForm(CronTaskType::class, $cronTask);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em->persist($cronTask);
                $em->flush();

                $request->getSession()->getFlashBag()->add(
                    'info',
                    sprintf('Task %s mise à jour avec succès.', $cronTask->getName())
                );

                return $this->redirect($this->generateUrl('dsp_cm_crontasks_list'));
            }
        }

        return $this->render('@DspSoftsCronManager/CronTask/form.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
