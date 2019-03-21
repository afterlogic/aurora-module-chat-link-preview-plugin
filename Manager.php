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
 */
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
			$aContextOptions=array(
				"ssl"=>array(
					"verify_peer"=>false,
					"verify_peer_name"=>false,
				),
			);
			@$sHtml = \file_get_contents($sURL, false, stream_context_create($aContextOptions));
			if ($sHtml)
			{
				$sImage = '';
				$sTitle = '';
				$sDescription = '';

				$aMeta = $this->GetMetaTags($sHtml);
				foreach ($aMeta as $key => $value)
				{
					switch ($key)
					{
						case 'og:image':
							$sImage = $value;
							break;
						case 'og:title':
							$sTitle = $value;
							break;
						case 'og:description':
							$sDescription = $value;
							break;
					}
				}

				if ($sImage && $sTitle && $sDescription)
				{
					$sResult = '<div>
						<a href="' . $sURL . '">
						<div style="color: #fff;
							background-image: url(' . $sImage . ');
							background-size: cover;
							height: 150px;
							width:300px;
							border-radius: 7px; 
							display: table-cell;
							vertical-align: bottom">
							<div style="background: rgba(86, 86, 86, 0.5); padding: 10px; border-radius: 7px;">
								<h3>' . $sTitle . '</h3>
								<span>' . parse_url($sURL, PHP_URL_HOST) . '</span>
							</div>
						</div>
					</a></div>';
				}
			}
		}
		return $sResult;
	}
	
	public function GetMetaTags($sStr)
	{
		$aOut = [];
		$sPattern = '
		~<\s*meta\s

		# using lookahead to capture type to $1
		  (?=[^>]*?
		  \b(?:name|property|http-equiv)\s*=\s*
		  (?|"\s*([^"]*?)\s*"|\'\s*([^\']*?)\s*\'|
		  ([^"\'>]*?)(?=\s*/?\s*>|\s\w+\s*=))
		)

		# capture content to $2
		[^>]*?\bcontent\s*=\s*
		  (?|"\s*([^"]*?)\s*"|\'\s*([^\']*?)\s*\'|
		  ([^"\'>]*?)(?=\s*/?\s*>|\s\w+\s*=))
		[^>]*>

		~ix';

		if(preg_match_all($sPattern, $sStr, $aOut))
		{
			return array_combine($aOut[1], $aOut[2]);
		}
		return array();
	}
}
