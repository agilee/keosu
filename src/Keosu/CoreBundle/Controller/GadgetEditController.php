<?php
/************************************************************************
 Keosu is an open source CMS for mobile app
Copyright (C) 2014  Vincent Le Borgne, Pockeit

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as
published by the Free Software Foundation, either version 3 of the
License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
************************************************************************/
namespace Keosu\CoreBundle\Controller;

use Keosu\CoreBundle\Entity\Gadget;

use Keosu\CoreBundle\Entity\Page;

use Keosu\CoreBundle\Util\TemplateUtil;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Common class for editing gadget
 * This class will be wall by specific gadget controller
 * @author vleborgne
 *
 */
class GadgetEditController extends Controller {
	/**
	 * Clean a zone and delete all the gadgets inside
	 * @param  $page where we want to delete the gadget
	 * @param $zone where we want to delete the gadget
	 */
	public function deleteAction($page, $zone) {
		$appid = $this->container->get('keosu_core.curapp')
			->getCurApp($this->get('doctrine')->getManager(),
				$this->get("session"));
		//Look if there is a shared gadget in this zone
		$commonGadget=$this->get('doctrine')->getManager()
			->getRepository('KeosuCoreBundle:Gadget')->findSharedByZoneAndApp($zone,$appid);
		//If there is no share gadget we try to find the specific one
		if($commonGadget==null){
			$commonGadget = $this->get('doctrine')->getManager()
				->getRepository('KeosuCoreBundle:Gadget')
				->findOneBy(array('zone' => $zone, 'page' => $page));
		}
		//Delete the gadget
		$this->get('doctrine')->getManager()->remove($commonGadget);
		$this->get('doctrine')->getManager()->flush();
		
		//Redirect to the last page
		return $this
				->redirect(
						$this
								->generateUrl(
										'keosu_core_views_page',
										array(
												'id' => $page)));
	}
	/**
	 * Ading a new Gadget in a zone
	 * @param  $page where we want to add the gadget
	 * @param $zone where we want to add the gadget
	 */
	public function addAction($page, $zone) {
		//Call the common gadget function with gadget class in parameter
		//$this->getGadgetClass() is defined in the child object it return the full package of the gadget class
		// (for exemple Keosu\Gadget\ArticleGadgetBundle\ArticleGadget)
		$gadgetArrray = $this::addGadgetCommonAction($page, $zone,
				$this->getGadgetClass());
		//Specific gadget witch is an instance of getGadgetClass
		$specificGadget = $gadgetArrray['specific'];
		//Common gadget witch is an instance of Gadget Entity
		$commonGadget = $gadgetArrray['common']; 
		$oldGadget = $gadgetArrray['old'];
		return $this->formGadget($specificGadget, $commonGadget, $oldGadget);//Create form
	}
	/**
	 * Adding gadget process
	 */
	private function addGadgetCommonAction($page, $zone, $gadgetClass) {
	
		$appid = $this->container->get('keosu_core.curapp')
			->getCurApp($this->get('doctrine')->getManager(),
				$this->get("session"));
		//Find if there is already a gadget in the zone
		//We do this to delete it later
		$commonGadget=$this->get('doctrine')->getManager()
			->getRepository('KeosuCoreBundle:Gadget')->findSharedByZoneAndApp($zone,$appid);
		//If there is no share gadget we try to find the specific one
		if($commonGadget==null){
			$commonGadget = $this->get('doctrine')->getManager()
			->getRepository('KeosuCoreBundle:Gadget')
			->findOneBy(array('zone' => $zone, 'page' => $page));
		}
		$commonGadget = $this->get('doctrine')->getManager()
			->getRepository('KeosuCoreBundle:Gadget')
			->findOneBy(array('zone' => $zone, 'page' => $page));
	
		$oldGadget = null;
		if ($commonGadget != null) {
			$oldGadget = $commonGadget;
		}
		
		//Create a instance of the common gadget class and the specific one
		$commonGadget = new Gadget();
		$gadget = new $gadgetClass();
		$commonGadget->setStatic($gadget->isStatic());
		
		//Finding curent page and zone to store it in gadget object
		$pageObject = $this->get('doctrine')->getManager()
			->getRepository('KeosuCoreBundle:Page')->find($page);
		$gadget->setPage($pageObject);
		$gadget->setZone($zone);
	
		//Returning a array of 3 gadgets
		$gadgetArray = Array();
		$gadgetArray['specific'] = $gadget;
		$gadgetArray['common'] = $commonGadget;
		$gadgetArray['old'] = $oldGadget;
		return $gadgetArray;
	}
	/**
	 * Edit an existing gadget
	 * Same process as Add
	 */
	public function editAction($page, $zone) {
		//Call the common gadget function with gadget class in parameter
		$gadgetArrray = $this::editGadgetCommonAction($page, $zone,
				$this->getGadgetClass());
		$specificGadget = $gadgetArrray['specific'];
		$commonGadget = $gadgetArrray['common'];
		return $this->formGadget($specificGadget, $commonGadget, null);
	}
	
	/**
	 * Editing gadget process
	 */
	private function editGadgetCommonAction($page, $zone, $gadgetClass) {
		$appid = $this->container->get('keosu_core.curapp')
			->getCurApp($this->get('doctrine')->getManager(),
				$this->get("session"));
		//Look if there is a shared gadget in this zone
		$commonGadget=$this->get('doctrine')->getManager()
				->getRepository('KeosuCoreBundle:Gadget')->findSharedByZoneAndApp($zone,$appid);
		//If there is no share gadget we try to find the specific one
		if($commonGadget==null){
			$commonGadget = $this->get('doctrine')->getManager()
				->getRepository('KeosuCoreBundle:Gadget')
				->findOneBy(array('zone' => $zone, 'page' => $page));
		}		
	
		//Convert the common gadget to a specific gadget object
		$gadget = $gadgetClass::constructfromGadget($commonGadget);
		$gadgetArray = Array();
		$gadgetArray['specific'] = $gadget;
		$gadgetArray['common'] = $commonGadget;
		$gadgetArray['old'] = null;
		return $gadgetArray;
	}
	
	/**
	 * Create the form to edit/add the gadget
	 */
	private function formGadget($specificGadget, $commonGadget, $oldGadget) {
		$formBuilder = $this->createFormBuilder($specificGadget);
		//Build gadget form is defined in child class (the specific controller one)
		$this->buildGadgetForm($formBuilder, $specificGadget->getChildGadgetName());
		$form = $formBuilder->getForm();
	
		return $this::formCommonGadget($form, $specificGadget, $commonGadget,
				$oldGadget);
	}
	/**
	 * Form submit process
	 * Store the gadget in database
	 */
	public function formCommonGadget($form, $gadget, $commonGadget, $oldGadget) {

		$request = $this->get('request');
		//If we are in POST method, form is submit
		if ($request->getMethod() == 'POST') {
			$form->bind($request);

			if ($form->isValid()) {
				$em = $this->get('doctrine')->getManager();
				//If there is an existing gadget in the same page/zone we delete it
				if ($oldGadget != null) {
					$em->remove($oldGadget);
				}
				$gadget->convertAsExistingCommonGadget($commonGadget);  
				$commonGadget->setStatic($gadget->isStatic());
				$em->persist($commonGadget);
				$em->flush();

				//Export the app to see the changes in simulator
				$baseurl = $this->container->getParameter('url_base');
				$param = $this->container->getParameter('url_param');
				
				$appid = $this->container->get('keosu_core.curapp')
					->getCurApp($this->get('doctrine')->getManager(),
						$this->get("session"));
				$exporter = $this->container->get('keosu_core.exporter');
				$exporter
						->exportApp($this->get('doctrine')->getManager(),
								$baseurl, $param, $appid);

				return $this
						->redirect(
								$this
										->generateUrl(
												'keosu_core_views_page',
												array(
														'id' => $commonGadget
																->getPage()
																->getId())));
			}
		}
		return $this
				->render($this->getRenderEditTemplate(),
						array('form' => $form->createView(),
							  'gadgetDir'=>TemplateUtil::getTemplatePath($gadget->getGadgetName())));

	}
	
	/**
	 * Get a liveService
	 * @return a live service
	 */
	public function getLiveService($name, $id) {
		return $this->get($name)->setReader($id);
	}
	
	/**
	 * Can be overrided
	 */
	public function getRenderEditTemplate(){
		return 'KeosuCoreBundle:Page:editGadget.html.twig';
	}

}
