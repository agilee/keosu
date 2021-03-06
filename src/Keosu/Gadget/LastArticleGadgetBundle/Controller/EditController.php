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
namespace Keosu\Gadget\LastArticleGadgetBundle\Controller;

use Keosu\CoreBundle\Controller\iGadgetController;

use Keosu\CoreBundle\Controller\GadgetEditController;

use Keosu\Gadget\LastArticleGadgetBundle\LastArticleGadget;

use Keosu\CoreBundle\Util\TemplateUtil;

use Keosu\CoreBundle\Entity\Gadget;

use Keosu\Gadget\ArticleGadgetBundle\ArticleGadget;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Specific gadget controller
 * extends Keosu\CoreBundle\Controller\GadgetEditController
 * implements
 * @author vleborgne
 *
 */
class EditController extends GadgetEditController implements iGadgetController{
	public function getGadgetClass(){
		return "Keosu\Gadget\LastArticleGadgetBundle\LastArticleGadget";
	}

	//No specific config for this gadget, we can only choose the template
	public function buildGadgetForm($formBuilder, $gadgetName) {
		$articlesPerPage = array(3=>3, 5=>5, 10=>10, 20=>20, 50=>50, 100=>100);
		$formBuilder
				->add('articlesPerPage','choice',
						array(
								'choices' => $articlesPerPage, 'required' => true))
				->add('shared', 'checkbox', array(
    					'label'     => 'Shared with all pages',
    					'required'  => false,))
				->add('gadgetTemplate', 'choice',
						array(
								'choices' => TemplateUtil::getTemplateGadgetList(
										$gadgetName), 'required' => true,'expanded'=>true));

	}

}
