<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\NewsCategory;
use AppBundle\Entity\News;
use AppBundle\Entity\Testimonial;
use AppBundle\Entity\Banner;

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

        $testimonial = $this->getDoctrine()
            ->getRepository(Testimonial::class)
            ->findAll();

        return $this->render('homepage/index.html.twig', [
            'testimonial' => $testimonial,
            'blockPricesOnHomepage' => $blockPricesOnHomepage,
            'banners' => $banners,
            'showSlide' => true
        ]);
    }
}
