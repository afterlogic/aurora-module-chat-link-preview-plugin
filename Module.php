<?php
/**
 * This code is licensed under AGPLv3 license or AfterLogic Software License
 * if commercial version of the product was purchased.
 * For full statements of the licenses see LICENSE-AFTERLOGIC and LICENSE-AGPL3 files.
 */

namespace Aurora\Modules\ChatLinkPreviewPlugin;

/**
 * @license https://www.gnu.org/licenses/agpl-3.0.html AGPL-3.0
 * @license https://afterlogic.com/products/common-licensing AfterLogic Software License
 * @copyright Copyright (c) 2019, Afterlogic Corp.
 *
 * @package Modules
 */
class Module extends \Aurora\System\Module\AbstractModule
{
	public $oApiPreviewManager = null;
	
	/**
	 * Subscribes on the event that is broadcasted in the process of executing of the GetPosts method in the Chat module.
	 */
	public function init()
	{
		$this->oApiPreviewManager = new Manager($this);
		$this->subscribeEvent('Chat::GetPosts', array($this, 'onGetPosts'));
	}
	
	/**
	 * Replace links on preview in the user posts in the chat.
	 * 
	 * @param array $aArgs Array with data of the user post in the chat.
	 * @param mixed $mResult Parameter is passed by reference for further filling with result.
	 */
	public function onGetPosts(&$aArgs, &$mResult)
	{
		if(is_array($aArgs) && count($aArgs) > 0)
		{
			foreach ($aArgs as &$aPost)
			{
				if (isset($aPost['text']))
				{
					$sPattern = '/http(s)?:\/\/[^\s]*/';
					$aMatches = [];

					preg_match($sPattern, $aPost['text'], $aMatches);
					array_unique($aMatches);
					foreach ($aMatches as $sURL)
					{
						if (filter_var($sURL, FILTER_VALIDATE_URL))
						{
							$aPost['text'] = $this->ReplaseLink($sURL, htmlspecialchars($aPost['text']));
							$aPost['isHtml'] = true;
						}
					}
				}
			}
		}
	}

	public function ReplaseLink($Link, $Text)
	{
		$sReplacement = $this->oApiPreviewManager->GetPreview($Link);
		$sTagLink = '<a href="' . $Link . '" target=_blank>' . $Link . '</a>';
		$Text = \str_replace($Link, $sTagLink, $Text);
		return $Text . $sReplacement;
	}
}
