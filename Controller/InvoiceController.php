<?php

/*
 * This file is part of the SGLFLTSBundle package.
 *
 * (c) Simon Guillem-Lessard <s.g.lessard@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SGL\FLTSBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Invoice controller.
 *
 * @Route("/")
 */
class InvoiceController extends Controller
{
    /**
     * Show invoice (HTML)
     *
     * @Route("/{id}/show", name="sgl_flts_invoice")
     * @Template("SGLFLTSBundle:Bill:Invoice/content.html.twig")
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $bill = $em->getRepository('SGLFLTSBundle:Bill')->findWithPartProjectClientWorks($id);

        if (!$bill) {
            throw $this->createNotFoundException('Unable to find Bill entity.');
        }

        return array(
            'bill'           => $bill,
            'part'    => $bill->getPart(),
            'project' => $bill->getPart()->getProject(),
            'client'  => $bill->getPart()->getProject()->getClient(),
            'business_logo_src'    => $this->container->getParameter('sgl_flts.business_invoice_logo_src'),
            'business_logo_width'  => $this->container->getParameter('sgl_flts.business_invoice_logo_width'),
            'business_name'  => $this->container->getParameter('sgl_flts.business_name'),
            'business_address'  => $this->container->getParameter('sgl_flts.business_address'),
            'business_phone'  => $this->container->getParameter('sgl_flts.business_phone'),
            'gst_registration_number' => $this->container->getParameter('sgl_flts.bill_gst_registration_number'),
            'pst_registration_number' => $this->container->getParameter('sgl_flts.bill_pst_registration_number'),
            'hst_registration_number' => $this->container->getParameter('sgl_flts.bill_hst_registration_number'),
        );
    }

    /**
     * @param integer $id
     *
     * @Route("/{id}/pdf", name="sgl_flts_invoice_pdf")
     */
    public function showPDFAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $bill = $em->getRepository('SGLFLTSBundle:Bill')->findWithPartProjectClientWorks($id);

        if (!$bill) {
            throw $this->createNotFoundException('Unable to find Bill entity.');
        }

        $filename = 'Invoice-'.$bill->getBilledAt()->format('Y').'-'.$bill->getNumber().'.pdf';

        return new Response(
            $this->get('knp_snappy.pdf')->getOutput($this->generateUrl('sgl_flts_invoice', array('id' => $bill->getId()), true)),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'attachment; filename="'.$filename.'"'
            )
        );
    }
}