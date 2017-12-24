<?php
/**
 * @copyright Copyright (c) 2017, Afterlogic Corp.
 * @license AGPL-3.0 or AfterLogic Software License
 *
 * This code is licensed under AGPLv3 license or AfterLogic Software License
 * if commercial version of the product was purchased.
 * For full statements of the licenses see LICENSE-AFTERLOGIC and LICENSE-AGPL3 files.
 */

namespace Aurora\Modules\ChatLinkPreviewPlugin;

class Manager extends \Aurora\System\Managers\AbstractManager
{	
	/**
	 * 
	 * @param \Aurora\System\Module\AbstractModule $oModule
	 */
	public function __construct(\Aurora\System\Module\AbstractModule $oModule = null)
	{
		parent::__construct($oModule);
	}
	
	/**
	 * Return preview for URL.
	 * 
	 * @param string $sURL URL.
	 * @return string
	 */
	public function GetPreview($sURL)
	{
		$sResult = '';
		if (!empty($sURL))
		{
			$sResult = "```" . $sURL . "```";
		}
		return $sResult;
	}
}
