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
namespace Keosu\CoreBundle\Service;
use Keosu\CoreBundle\Util\ZipUtil;

use Keosu\CoreBundle\Util\ThemeUtil;

use Keosu\CoreBundle\Util\FilesUtil;

use Keosu\CoreBundle\Util\ExporterUtil;

use Keosu\CoreBundle\Delegate\ExporterDelegate;

use Keosu\CoreBundle\Util\StringUtil;

use Keosu\CoreBundle\Model\ZoneModel;

use Keosu\CoreBundle\Util\TemplateUtil;

use Keosu\CoreBundle\Entity\Page;
use Symfony\Component\DomCrawler\Crawler;

class CurApp {
	public function getCurApp($manager,$session) {
		$appid=$session->get("appid");
		if($appid==null){
			$apps = $manager->getRepository('KeosuCoreBundle:App')->findAll();
			if($apps==null){
				return 0;
			}
			$firstApp = reset($apps);
			$appid = $firstApp->getId();
		}
		return $appid;
	}
}
?>