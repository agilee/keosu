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
namespace Keosu\CoreBundle\Util;

class FilesUtil {

	public static function deleteDir($dir) {
		if (is_dir($dir)) {
			//Liste le contenu du r�pertoire dans un tableau
			$dir_content = scandir($dir);
			//Est-ce bien un r�pertoire?
			if($dir_content !== FALSE){
				//Pour chaque entr�e du r�pertoire
				foreach ($dir_content as $entry)
				{
					//Raccourcis symboliques sous Unix, on passe
					if(!in_array($entry, array('.','..'))){
						//On retrouve le chemin par rapport au d�but
						$entry = $dir . '/' . $entry;
						//Cette entr�e n'est pas un dossier: on l'efface
						if(!is_dir($entry)){
							unlink($entry);
						}
						else{
							self::deleteDir($entry);
						}
					}
				}
			}
			rmdir($dir);
		}

	}

	public static function copyFolder($source, $dest) {

		foreach ($iterator = new \RecursiveIteratorIterator(
				new \RecursiveDirectoryIterator($source,
						\RecursiveDirectoryIterator::SKIP_DOTS),
				\RecursiveIteratorIterator::SELF_FIRST) as $item) {
			if ($item->isDir()) {
				mkdir($dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
			} else {
				copy($item,
						$dest . DIRECTORY_SEPARATOR
								. $iterator->getSubPathName());
			}
		}
	}

}
