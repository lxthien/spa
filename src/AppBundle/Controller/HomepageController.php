<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use AppBundle\Entity\NewsCategory;
use AppBundle\Entity\News;
use AppBundle\Entity\Banner;
use AppBundle\Entity\Contact;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;

class HomepageController extends Controller
{
    public function indexAction(Request $request)
    {
        $listPrices = $this->get('settings_manager')->get('listPrices');
        $blockPricesOnHomepage = array();

        if (!empty($listPrices)) {
            $listPricesArray = explode(',', $listPrices);
            if (is_array($listPricesArray) && count($listPricesArray) > 0) {
                
                for ($i = 0; $i < count($listPricesArray); $i++) {
                    $post = $this->getDoctrine()
                                ->getRepository(News::class)
                                ->find($listPricesArray[$i]);
                    if ($post) {
                        $blockPricesOnHomepage[] = $post;
                    }
                }
            }
        }

        $banners = $this->getDoctrine()
            ->getRepository(Banner::class)
            ->findBy(
                array('bannercategory' => 1),
                array('createdAt' => 'DESC')
            );

        $imagesHomepage = $this->getDoctrine()
            ->getRepository(Banner::class)
            ->findBy(
                array('bannercategory' => 2),
                array('createdAt' => 'DESC')
            );

        $videosHomepage = $this->getDoctrine()
            ->getRepository(Banner::class)
            ->findBy(
                array('bannercategory' => 3),
                array('createdAt' => 'DESC')
            );

        return $this->render('homepage/index.html.twig', [
            'blockPricesOnHomepage' => $blockPricesOnHomepage,
            'banners' => $banners,
            'imagesHomepage' => $imagesHomepage,
            'videosHomepage' => $videosHomepage,
            'showSlide' => true
        ]);
    }

    public function formContactAction()
    {
        $contact = new Contact();
        
        $form = $this->createFormBuilder($contact)
            ->setAction($this->generateUrl('homepagecontact'))
            ->add('name', TextType::class, array('label' => 'Tên của bạn'))
            ->add('phone', TextType::class, array('label' => 'Số điện thoại'))
            ->add('contents', TextType::class, array('label' => 'Dịch vụ'))
            ->add('send', ButtonType::class, array('label' => 'Gửi'))
            ->getForm();

        return $this->render('layout/contactFooter.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/homepage-contact", name="homepagecontact")
     * 
     * @return JSON
     */
    public function handleContactFormAction(Request $request, \Swift_Mailer $mailer)
    {
        if (!$request->isXmlHttpRequest()) {
            return new Response(
                json_encode(
                    array(
                        'status'=>'error',
                        'message' => 'You can access this only using Ajax!'
                    )
                )
            );
        } else {
            $contact = new Contact();
            
            $form = $this->createFormBuilder($contact)
                ->add('name', TextType::class)
                ->add('phone', TextType::class)
                ->add('contents', TextType::class)
                ->getForm();

            $form->handleRequest($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($contact);
                $em->flush();

                if (null !== $contact->getId()) {
                    $message = \Swift_Message::newInstance()
                        ->setSubject($this->get('translator')->trans('comment.email.title', ['%siteName%' => $this->get('settings_manager')->get('siteName')]))
                        ->setFrom(['hotro.xaydungminhduy@gmail.com' => $this->get('settings_manager')->get('siteName')])
                        ->setTo($this->get('settings_manager')->get('emailContact'))
                        ->setBody(
                            $this->renderView(
                                'Emails/contact.html.twig',
                                array(
                                    'name' => $request->request->get('form')['name'],
                                    'contents' => $request->request->get('form')['contents'],
                                    'phone' => $request->request->get('form')['phone']
                                )
                            ),
                            'text/html'
                        )
                    ;

                    $mailer->send($message);
    
                    return new Response(
                        json_encode(
                            array(
                                'status'=>'success',
                                'message' => 'Chúng tôi đã nhận được thông tin của bạn. Chúng tôi sẽ kiểm tra và liên hệ sớm đến bạn!'
                            )
                        )
                    );
                } else {
                    return new Response(
                        json_encode(
                            array(
                                'status'=>'error',
                                'message' => $this->get('translator')->trans('comment.have_a_problem_on_your_request')
                            )
                        )
                    );
                }
            } else {
                return new Response(
                    json_encode(
                        array(
                            'status'=>'error',
                            'message' => $this->get('translator')->trans('comment.have_a_problem_on_your_request')
                        )
                    )
                );
            }
        }
    }
}
