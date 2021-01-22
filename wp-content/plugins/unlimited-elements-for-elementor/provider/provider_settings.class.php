<?php
/**
 * @package Unlimited Elements
 * @author UniteCMS.net
 * @copyright (C) 2012 Unite CMS, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class UniteCreatorSettings extends UniteCreatorSettingsWork{

	
	/**
	 * add settings provider types
	 */
	protected function addSettingsProvider($type, $name,$value,$title,$extra ){
		
		$isAdded = false;
		
		return($isAdded);
	}
	
	
	/**
	 * show taxanomy
	 */
	private function showTax(){
										
		$showTax = UniteFunctionsUC::getGetVar("maxshowtax", "", UniteFunctionsUC::SANITIZE_NOTHING);
		$showTax = UniteFunctionsUC::strToBool($showTax);
		
		if($showTax == true){
			
			$args = array("taxonomy"=>"");
			$cats = get_categories($args);
			
			$arr1 = UniteFunctionsWPUC::getTaxonomiesWithCats();
			
			
			$arrPostTypes = UniteFunctionsWPUC::getPostTypesAssoc();
			$arrTax = UniteFunctionsWPUC::getTaxonomiesWithCats();
			$arrCustomTypes = get_post_types(array('_builtin' => false));
			
			$arr = get_taxonomies();
			
			$taxonomy_objects = get_object_taxonomies( 'post', 'objects' );
   			dmp($taxonomy_objects);
   			
			dmp($arrCustomTypes);
			dmp($arrPostTypes);
			exit();
		}
		
	}
	
	
	/**
	 * get categories from all post types
	 */
	protected function getCategoriesFromAllPostTypes($arrPostTypes){
		
		if(empty($arrPostTypes))
			return(array());

		$arrAllCats = array();
		$arrAllCats[__("All Categories", "unlimited-elements-for-elementor")] = "all";
		
		foreach($arrPostTypes as $name => $arrType){
		
			if($name == "page")
				continue;
			
			$postTypeTitle = UniteFunctionsUC::getVal($arrType, "title");
			
			$cats = UniteFunctionsUC::getVal($arrType, "cats");
			
			if(empty($cats))
				continue;
			
			foreach($cats as $catID => $catTitle){
				
				if($name != "post")
					$catTitle = $catTitle." ($postTypeTitle type)";
				
				$arrAllCats[$catTitle] = $catID;
				
			}
			
		}
		
		
		return($arrAllCats);
	}
	
	/**
	 * get taxonomies array for terms picker
	 */
	private function addPostTermsPicker_getArrTaxonomies($arrPostTypesWithTax){
		
		$arrAllTax = array();
		
		
		//make taxonomies data
		$arrTaxonomies = array();
		foreach($arrPostTypesWithTax as $typeName => $arrType){
			
			$arrItemTax = UniteFunctionsUC::getVal($arrType, "taxonomies");
			
			$arrTaxOutput = array();
			
			//some fix that avoid double names
			$arrDuplicateValues = UniteFunctionsUC::getArrayDuplicateValues($arrItemTax);
			
			foreach($arrItemTax as $slug => $taxTitle){
				
				$isDuplicate = array_key_exists($taxTitle, $arrDuplicateValues);
				
				//some modification for woo
				if($taxTitle == "Tag" && $slug != "post_tag")
					$isDuplicate = true;
				
				if(isset($arrAllTax[$taxTitle]))
					$isDuplicate = true;
					
				if($isDuplicate == true)
					$taxTitle = UniteFunctionsUC::convertHandleToTitle($slug);
				
				$taxTitle = ucwords($taxTitle);
				
				$arrTaxOutput[$slug] = $taxTitle;
				
				$arrAllTax[$taxTitle] = $slug;
			}
			
			if(!empty($arrTaxOutput))
				$arrTaxonomies[$typeName] = $arrTaxOutput;
		}
		
		$response = array();
		$response["post_type_tax"] = $arrTaxonomies;
		$response["taxonomies_simple"] = $arrAllTax;
		
		
		return($response);
	}

	
	/**
	 * add users picker
	 */
	protected function addUsersPicker($name,$value,$title,$extra){
		
		$arrRoles = UniteFunctionsWPUC::getRolesShort(true);
		
		//----- roles in -------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		
		if(!empty($arrRoles))
			$arrRoles = array_flip($arrRoles);
		
		$role = UniteFunctionsUC::getVal($value, $name."_role");
		if(empty($role))
			$role = UniteFunctionsUC::getArrFirstValue($arrRoles);
		
		$params["is_multiple"] = true;
		$params["placeholder"] = __("All Roles", "unlimited-elements-for-elementor");
		//$params["description"] = __("Get all the users if leave empty", "unlimited-elements-for-elementor");
		
		$this->addMultiSelect($name."_role", $arrRoles, __("Select Roles", "unlimited-elements-for-elementor"), $role, $params);
		
		
		//-------- exclude roles ---------- 
		
		$arrRoles = UniteFunctionsWPUC::getRolesShort();
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		
		if(!empty($arrRoles))
			$arrRoles = array_flip($arrRoles);
		
		$roleExclude = UniteFunctionsUC::getVal($value, $name."_role_exclude");
		
		$params["is_multiple"] = true;
		
		$this->addMultiSelect($name."_role_exclude", $arrRoles, __("Exclude Roles", "unlimited-elements-for-elementor"), $roleExclude, $params);
		
		
		
	}

	/**
	 * add users picker
	 */
	protected function addMenuPicker($name, $value, $title, $extra){
		
		$arrMenus = UniteFunctionsWPUC::getMenusListShort();
		
		$menuID = UniteFunctionsUC::getVal($value, $name."_id");
		
		if(empty($menuID))
			$menuID = UniteFunctionsUC::getFirstNotEmptyKey($arrMenus);
					
		$arrMenus = array_flip($arrMenus);
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		
		$this->addSelect($name."_id", $arrMenus, __("Select Menu", "unlimited-elements-for-elementor"), $menuID, $params);
		
		//add depth
		$arrDepth = array();
		$arrDepth["0"] = __("All Depths", "unlimited-elements-for-elementor");
		$arrDepth["1"] = __("1", "unlimited-elements-for-elementor");
		$arrDepth["2"] = __("2", "unlimited-elements-for-elementor");
		$arrDepth["3"] = __("3", "unlimited-elements-for-elementor");
		
		$arrDepth = array_flip($arrDepth);
		$depth = UniteFunctionsUC::getVal($value, $name."_depth", "0");
				
		$this->addSelect($name."_depth", $arrDepth, __("Max Depth", "unlimited-elements-for-elementor"), $depth, $params);
		
	}
	
	
	/**
	 * add post terms settings
	 */
	protected function addPostTermsPicker($name, $value, $title, $extra){
		
		$arrPostTypesWithTax = UniteFunctionsWPUC::getPostTypesWithTaxomonies(GlobalsProviderUC::$arrFilterPostTypes, false);
		
		$taxData = $this->addPostTermsPicker_getArrTaxonomies($arrPostTypesWithTax);
		
		$arrPostTypesTaxonomies = $taxData["post_type_tax"];
		
		$arrTaxonomiesSimple = $taxData["taxonomies_simple"];
				
		//----- add post types ---------
		
		//prepare post types array
		
		$arrPostTypes = array();
		foreach($arrPostTypesWithTax as $typeName => $arrType){
			
			$title = UniteFunctionsUC::getVal($arrType, "title");
			if(empty($title))
				$title = ucfirst($typeName);
			
			$arrPostTypes[$title] = $typeName;
		}
		
		$postType = UniteFunctionsUC::getVal($value, $name."_posttype");
		if(empty($postType))
			$postType = UniteFunctionsUC::getArrFirstValue($arrPostTypes);
		
		$params = array();
		
		$params[UniteSettingsUC::PARAM_CLASSADD] = "unite-setting-post-type";
		$dataTax = UniteFunctionsUC::encodeContent($arrPostTypesTaxonomies);
		
		$params[UniteSettingsUC::PARAM_ADDPARAMS] = "data-arrposttypes='$dataTax' data-settingtype='select_post_taxonomy' data-settingprefix='{$name}'";
		$params["datasource"] = "post_type";
		$params["origtype"] = "uc_select_special";
		
		$this->addSelect($name."_posttype", $arrPostTypes, __("Select Post Type", "unlimited-elements-for-elementor"), $postType, $params);
		
		//---------- add taxonomy ---------
				
		$params = array();
		$params["datasource"] = "post_taxonomy";
		$params[UniteSettingsUC::PARAM_CLASSADD] = "unite-setting-post-taxonomy";
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		
		$arrTax = UniteFunctionsUC::getVal($arrPostTypesTaxonomies, $postType, array());
		
		if(!empty($arrTax))
			$arrTax = array_flip($arrTax);
				
		$taxonomy = UniteFunctionsUC::getVal($value, $name."_taxonomy");
		if(empty($taxonomy))
			$taxonomy = UniteFunctionsUC::getArrFirstValue($arrTax);
				
		$this->addSelect($name."_taxonomy", $arrTaxonomiesSimple, __("Select Taxonomy", "unlimited-elements-for-elementor"), $taxonomy, $params);
		
		//---------- add exclude ---------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["placeholder"] = __("Example: cat1,cat2","unlimited-elements-for-elementor");
		$params["description"] = __("To exclude, enter comma saparated term slugs","unlimited-elements-for-elementor");
		
		$exclude = UniteFunctionsUC::getVal($value, $name."_exclude");
		
		$this->addTextBox($name."_exclude", $exclude, __("Exclude Terms", "unlimited-elements-for-elementor"), $params);
		
		// --------- add order by -------------
		
		$arrOrderBy = UniteFunctionsWPUC::getArrTermSortBy();
		$arrOrderBy = array_flip($arrOrderBy);
		
		$orderBy = UniteFunctionsUC::getVal($value, $name."_orderby", "name");
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		
		$this->addSelect($name."_orderby", $arrOrderBy, __("Order By", "unlimited-elements-for-elementor"), $orderBy, $params);
		
		//--------- add order direction -------------
		
		$arrOrderDir = UniteFunctionsWPUC::getArrSortDirection();
		
		$orderDir = UniteFunctionsUC::getVal($value, $name."_orderdir", UniteFunctionsWPUC::ORDER_DIRECTION_ASC);
		
		$arrOrderDir = array_flip($arrOrderDir);
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		
		$this->addSelect($name."_orderdir", $arrOrderDir, __("Order Direction", "unlimited-elements-for-elementor"), $orderDir, $params);
		
		//--------- add hide empty -------------
		
		$hideEmpty = UniteFunctionsUC::getVal($value, $name."_hideempty", "no_hide");
		
		
		$arrHide = array();
		$arrHide["no_hide"] = "Don't Hide";
		$arrHide["hide"] = "Hide";
		$arrHide = array_flip($arrHide);
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		
		$this->addSelect($name."_hideempty", $arrHide, __("Hide Empty", "unlimited-elements-for-elementor"), $hideEmpty, $params);
		
		
		//add hr
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		
		$this->addHr("post_terms_sap", $params);
	}
	
	
	/**
	 * add woo commerce categories picker
	 */
	protected function addWooCatsPicker($name, $value, $title, $extra){

		$conditionQuery = array(
			$name."_type" => "query",
		);
		
		$conditionManual = array(
			$name."_type" => "manual",
		);
		
		
		//---------- type choosing ---------
		
		$arrType = array();
		$arrType["query"] = __("Categories Query","unlimited-elements-for-elementor");
		$arrType["manual"] = __("Manual Selection","unlimited-elements-for-elementor");
		
		$arrType = array_flip($arrType);
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		
		$type = UniteFunctionsUC::getVal($value, $name."_type", "query");
		
		$this->addSelect($name."_type", $arrType, __("Selection Type", "unlimited-elements-for-elementor"), $type, $params);
		
		//---------- add hr ---------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		
		$this->addHr("woocommere_terms_sap_type", $params);
		
		
		//---------- add parent ---------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["placeholder"] = __("Example: cat1", "unlimited-elements-for-elementor");
		$params["description"] = __("Write parent category slug, if no parent leave empty", "unlimited-elements-for-elementor");
		$params["elementor_condition"] = $conditionQuery;
		
		$parent = UniteFunctionsUC::getVal($value, $name."_parent", "");
		
		$this->addTextBox($name."_parent", $parent, __("Parent Category", "unlimited-elements-for-elementor"), $params);
		
		
		//---------- include children ---------
		
		$includeChildren = UniteFunctionsUC::getVal($value, $name."_children", "not_include");
		
		$arrChildren = array();
		$arrChildren["not_include"] = __("Don't Include", "unlimited-elements-for-elementor");
		$arrChildren["include"] = __("Include", "unlimited-elements-for-elementor");
		$arrChildren = array_flip($arrChildren);
		
		
		//---------- add children ---------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["elementor_condition"] = $conditionQuery;
		
		$this->addSelect($name."_children", $arrChildren, __("Include Children", "unlimited-elements-for-elementor"), $includeChildren, $params);
		
		
		//---------- add exclude ---------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["placeholder"] = "Example: cat1,cat2";
		$params["description"] = "To exclude, enter comma saparated term slugs";
		$params["label_block"] = true;
		$params["elementor_condition"] = $conditionQuery;
		
		$exclude = UniteFunctionsUC::getVal($value, $name."_exclude");
		
		$this->addTextBox($name."_exclude", $exclude, __("Exclude Categories", "unlimited-elements-for-elementor"), $params);
		
		// --------- add exclude categorized -------------
		
		$excludeUncat = UniteFunctionsUC::getVal($value, $name."_excludeuncat", "exclude");
		
		
		$arrExclude = array();
		$arrExclude["exclude"] = __("Exclude","unlimited-elements-for-elementor");
		$arrExclude["no_exclude"] = __("Don't Exclude","unlimited-elements-for-elementor");
		$arrExclude = array_flip($arrExclude);
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["elementor_condition"] = $conditionQuery;
		
		$this->addSelect($name."_excludeuncat", $arrExclude, __("Exclude Uncategorized Category", "unlimited-elements-for-elementor"), $excludeUncat, $params);
		
		// --------- hr -------------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		$params["elementor_condition"] = $conditionQuery;
		
		$this->addHr("woocommere_terms_sap1", $params);
		
		// --------- add order by -------------
		
		$arrOrderBy = UniteFunctionsWPUC::getArrTermSortBy();
		$arrOrderBy = array_flip($arrOrderBy);
		
		$orderBy = UniteFunctionsUC::getVal($value, $name."_orderby", "name");
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["elementor_condition"] = $conditionQuery;
		
		$this->addSelect($name."_orderby", $arrOrderBy, __("Order By", "unlimited-elements-for-elementor"), $orderBy, $params);
		
		//--------- add order direction -------------
		
		$arrOrderDir = UniteFunctionsWPUC::getArrSortDirection();
		
		$orderDir = UniteFunctionsUC::getVal($value, $name."_orderdir", UniteFunctionsWPUC::ORDER_DIRECTION_ASC);
		
		$arrOrderDir = array_flip($arrOrderDir);
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["elementor_condition"] = $conditionQuery;
		
		$this->addSelect($name."_orderdir", $arrOrderDir, __("Order Direction", "unlimited-elements-for-elementor"), $orderDir, $params);
		
		
		//--------- add hide empty -------------
		
		$hideEmpty = UniteFunctionsUC::getVal($value, $name."_hideempty", "no_hide");
		
		$arrHide = array();
		$arrHide["no_hide"] = "Don't Hide";
		$arrHide["hide"] = "Hide";
		$arrHide = array_flip($arrHide);
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["elementor_condition"] = $conditionQuery;
		 
		$this->addSelect($name."_hideempty", $arrHide, __("Hide Empty", "unlimited-elements-for-elementor"), $hideEmpty, $params);
		
		//add hr
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		$params["elementor_condition"] = $conditionQuery;
		
		$this->addHr("woocommere_terms_sap", $params);

		
		//---------- include categories - manual selection ---------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["placeholder"] = __("Example: cat1, cat2", "unlimited-elements-for-elementor");
		$params["description"] = __("Include specific categories by slug", "unlimited-elements-for-elementor");
		$params["label_block"] = true;
		$params["elementor_condition"] = $conditionManual;
		
		$cats = UniteFunctionsUC::getVal($value, $name."_include", "");
		
		$this->addTextBox($name."_include", $cats, __("Include Specific Categories", "unlimited-elements-for-elementor"), $params);
		
	}
	
	
	/**
	 * add background settings
	 */
	protected function addBackgroundSettings($name, $value, $title, $param){
		
		$arrTypes = array();
		$arrTypes["none"] = __("No Background", "unlimited-elements-for-elementor");
		$arrTypes["solid"] = __("Solid", "unlimited-elements-for-elementor");
		$arrTypes["gradient"] = __("Gradient", "unlimited-elements-for-elementor");
		
		$arrTypes = array_flip($arrTypes);
		
		$type = UniteFunctionsUC::getVal($param, "background_type", "none");
		
		$this->addRadio($name."_type", $arrTypes, "Background Type", $type);
		
		$solid = UniteFunctionsUC::getVal($param, "solid_color");
		$gradient1 = UniteFunctionsUC::getVal($param, "gradient_color1");
		$gradient2 = UniteFunctionsUC::getVal($param, "gradient_color2");
		
		$this->addHr();
		
		//solid color
		$this->startBulkControl($name."_type", "show", "solid");
		
			$this->addColorPicker($name."_color_solid", $solid, __("Solid Color", "unlimited-elements-for-elementor"));
		
		$this->endBulkControl();
		
		//gradient color
		$this->startBulkControl($name."_type", "show", "gradient");
		
			$this->addColorPicker($name."_color_gradient1", $gradient1, __("Gradient Color1", "unlimited-elements-for-elementor"));
			$this->addColorPicker($name."_color_gradient2", $gradient2, __("Gradient Color2", "unlimited-elements-for-elementor"));
		
		$this->endBulkControl();
		
	}
	
	/**
	 * add post ID select
	 * Enter description here ...
	 */
	protected function addPostIDSelect($settingName, $text = null, $elementorCondition = null){
		
		if(empty($text))
			$text = __("Search and Select Posts", "unlimited-elements-for-elementor");
		
		$params = array();
		
		$params[UniteSettingsUC::PARAM_CLASSADD] = "unite-setting-special-select";
		
		/*
		$placeholder = __("All Posts", "unlimited-elements-for-elementor");
		$placeholder = UniteFunctionsUC::encodeContent($placeholder);
		*/
		
		$loaderText = __("Loading Data...", "unlimited-elements-for-elementor");
		$loaderText = UniteFunctionsUC::encodeContent($loaderText);
		
		$params[UniteSettingsUC::PARAM_ADDPARAMS] = "data-settingtype='post_ids' data-loadertext='$loaderText'";
		
		$params["datasource"] = "post_type";
		$params["origtype"] = "uc_select_special";
		$params["label_block"] = true;
		
		if(!empty($elementorCondition))
			$params["elementor_condition"] = $elementorCondition;
		
		$this->addSelect($settingName, array(), $text , "", $params);
		
	}
	
	
	/**
	 * add post list picker
	 */
	protected function addPostsListPicker($name,$value,$title,$extra){
		
 		$simpleMode = UniteFunctionsUC::getVal($extra, "simple_mode");
		$simpleMode = UniteFunctionsUC::strToBool($simpleMode);
		
		$allCatsMode = UniteFunctionsUC::getVal($extra, "all_cats_mode");
		$allCatsMode = UniteFunctionsUC::strToBool($allCatsMode);
		
		$isForWooProducts = UniteFunctionsUC::getVal($extra, "for_woocommerce_products");
		$isForWooProducts = UniteFunctionsUC::strToBool($isForWooProducts);
		
		$addCurrentPosts = UniteFunctionsUC::getVal($extra, "add_current_posts");
		$addCurrentPosts = UniteFunctionsUC::strToBool($addCurrentPosts);
		
		$arrPostTypes = UniteFunctionsWPUC::getPostTypesWithCats(GlobalsProviderUC::$arrFilterPostTypes);
		
		$isWpmlExists = UniteCreatorWpmlIntegrate::isWpmlExists();
		
		/*
		if($isWpmlExists == true){
			
			$objWpmlIntegrate = new UniteCreatorWpmlIntegrate();
			
			$arrLanguages = $objWpmlIntegrate->getLanguagesShort(true);
			$activeLanguege = $objWpmlIntegrate->getActiveLanguage();
		}
		*/
				
		//fill simple types
		$arrTypesSimple = array();
		
		if($simpleMode)
			$arrTypesSimple = array("Post"=>"post","Page"=>"page");
		else{
			
			foreach($arrPostTypes as $arrType){
				
				$postTypeName = UniteFunctionsUC::getVal($arrType, "name");
				$postTypeTitle = UniteFunctionsUC::getVal($arrType, "title");
				
				if(isset($arrTypesSimple[$postTypeTitle]))
					$arrTypesSimple[$postTypeName] = $postTypeName;
				else
					$arrTypesSimple[$postTypeTitle] = $postTypeName;
			}
			
		}
		
		
		//----- posts source ----
		//UniteFunctionsUC::showTrace();
		
		$arrNotCurrentElementorCondition = array();
		$arrCustomOnlyCondition = array();
		$arrRelatedOnlyCondition = array();
		$arrCurrentElementorCondition = array();
		$arrCustomAndCurrentElementorCondition = array();
		$arrNotManualElementorCondition = array();
		$arrCustomAndRelatedElementorCondition = array();
		$arrManualElementorCondition = array();
		
		
		if($addCurrentPosts == true){
						
			$arrCurrentElementorCondition = array(
				$name."_source" => "current",
			);
			
			$arrNotCurrentElementorCondition = array(
				$name."_source!" => "current",
			);
			
			$arrCustomAndCurrentElementorCondition = array(
				$name."_source" => array("current","custom"),
			);
			
			$arrCustomAndRelatedElementorCondition = array(
				$name."_source" => array("related","custom"), 
			);

			
			$arrCustomOnlyCondition = array(
				$name."_source" => "custom",
			);
			
			$arrRelatedOnlyCondition = array(
				$name."_source" => "related",
			);
			
			$arrNotInRelatedCondition = array(
				$name."_source!" => "related",
			);
			
			$arrNotManualElementorCondition = array(
				$name."_source!" => "manual",
			);
			
			$arrManualElementorCondition = array(
				$name."_source" => "manual",
			);
			
			
			$params = array();
			$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
			//$params["description"] = esc_html__("Choose the source of the posts list", "unlimited-elements-for-elementor");
			
			$source = UniteFunctionsUC::getVal($value, $name."_source", "custom");
			$arrSourceOptions = array();
			$arrSourceOptions[__("Current Query Posts", "unlimited-elements-for-elementor")] = "current";
			$arrSourceOptions[__("Custom Posts", "unlimited-elements-for-elementor")] = "custom";
			$arrSourceOptions[__("Related Posts", "unlimited-elements-for-elementor")] = "related";
			$arrSourceOptions[__("Manual Selection", "unlimited-elements-for-elementor")] = "manual";
			
			$this->addSelect($name."_source", $arrSourceOptions, esc_html__("Posts Source", "unlimited-elements-for-elementor"), $source, $params);
			
			//-------- add static text - current --------
			
			$params = array();
			$params["origtype"] = UniteCreatorDialogParam::PARAM_STATIC_TEXT;
			$params["description"] = esc_html__("Choose the source of the posts list", "unlimited-elements-for-elementor");
			$params["elementor_condition"] = $arrCurrentElementorCondition;
			
			$this->addStaticText("The current posts are being used in archive pages", $name."_currenttext", $params);

			//-------- add static text - related --------
			
			$params = array();
			$params["origtype"] = UniteCreatorDialogParam::PARAM_STATIC_TEXT;
			$params["elementor_condition"] = $arrRelatedOnlyCondition;
			
			$this->addStaticText("The related posts are being used in single page. Posts from same post type and terms", $name."_relatedtext", $params);
			
		}
		
		//----- post type -----
		
		$defaultPostType = "post";
		if($isForWooProducts == true)
			$defaultPostType = "product";
		
		$postType = UniteFunctionsUC::getVal($value, $name."_posttype", $defaultPostType);
				
		$params = array();
		
		if($simpleMode == false){
			$params["datasource"] = "post_type";
			$params[UniteSettingsUC::PARAM_CLASSADD] = "unite-setting-post-type";
			
			$dataCats = UniteFunctionsUC::encodeContent($arrPostTypes);
			
			$params[UniteSettingsUC::PARAM_ADDPARAMS] = "data-arrposttypes='$dataCats' data-settingtype='select_post_type' data-settingprefix='{$name}'";
		}
		
		$params["origtype"] = "uc_select_special";
		//$params["description"] = esc_html__("Select which Post Type or Custom Post Type you wish to display", "unlimited-elements-for-elementor");
		
		$params["elementor_condition"] = $arrCustomOnlyCondition;
		
		$params["is_multiple"] = true;
		
		$this->addMultiSelect($name."_posttype", $arrTypesSimple, esc_html__("Post Types", "unlimited-elements-for-elementor"), $postType, $params);
		
		
		//----- hr -------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		$params["elementor_condition"] = $arrCustomOnlyCondition;
		
		$this->addHr("post_before_include",$params);

		// --------- Include BY some options -------------
		
		$arrIncludeBy = array();
		$arrIncludeBy["sticky_posts"] = __("Include Sticky Posts", "unlimited-elements-for-elementor");
		
		if($isForWooProducts == true){
			$arrIncludeBy["products_on_sale"] = __("Produts On Sale","unlimited-elements-for-elementor");
		}
		
		if(!empty($arrIncludeBy)){
		
			$includeBy = UniteFunctionsUC::getVal($value, $name."_includeby");
			
			$arrIncludeBy = array_flip($arrIncludeBy);
			
			$params = array();
			$params["is_multiple"] = true;
			$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
			
			$params["elementor_condition"] = $arrCustomOnlyCondition;
			
			$this->addMultiSelect($name."_includeby", $arrIncludeBy, esc_html__("Include By", "unlimited-elements-for-elementor"), $includeBy, $params);
			
			//--- add hr ----
			
			$params = array();
			$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
			
			$params["elementor_condition"] = $arrCustomOnlyCondition;
			
			$this->addHr("after_include_by",$params);
		}
		
		//----- add categories -------
		
		$arrCats = array();
		
		if($simpleMode == true){
			
			$arrCats = $arrPostTypes["post"]["cats"];
			$arrCats = array_flip($arrCats);
			$firstItemValue = reset($arrCats);
			
		}else if($allCatsMode == true){
			
			$arrCats = $this->getCategoriesFromAllPostTypes($arrPostTypes);
			$firstItemValue = reset($arrCats);
			
		}else{
			$firstItemValue = "";
		}
		
		$category = UniteFunctionsUC::getVal($value, $name."_category", $firstItemValue);
		
		$params = array();
		
		if($simpleMode == false){
			$params["datasource"] = "post_category";
			$params[UniteSettingsUC::PARAM_CLASSADD] = "unite-setting-post-category";
		}
		
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["is_multiple"] = true;
		
		//$params["description"] = esc_html__("Filter Posts by Specific Term", "unlimited-elements-for-elementor");
		
		$params["elementor_condition"] = $arrCustomOnlyCondition;
		
		$this->addMultiSelect($name."_category", $arrCats, esc_html__("Include By Terms", "unlimited-elements-for-elementor"), $category, $params);
		
		
		// --------- Include by term relation -------------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		
		$params["elementor_condition"] = $arrCustomOnlyCondition;
		
		$relation = UniteFunctionsUC::getVal($value, $name."_category_relation", "AND");
		
		$arrRelationItems = array();
		$arrRelationItems["And"] = "AND";
		$arrRelationItems["Or"] = "OR";
				
		$this->addSelect($name."_category_relation", $arrRelationItems, __("Include By Terms Relation", "unlimited-elements-for-elementor"), $relation, $params);

		//--------- show children -------------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_RADIOBOOLEAN;
		
		$params["elementor_condition"] = $arrCustomOnlyCondition;
		
		
		$isIncludeChildren = UniteFunctionsUC::getVal($value, $name."_terms_include_children", false);
		$isIncludeChildren = UniteFunctionsUC::strToBool($isIncludeChildren);

		$this->addRadioBoolean($name."_terms_include_children", __("Include Terms Children", "unlimited-elements-for-elementor"), $isIncludeChildren, "Yes", "No", $params);
		
		
		//---- manual selection search and replace -----
		
		$this->addPostIDSelect($name."_manual_select_post_ids", null, $arrManualElementorCondition);
		
		
		// --------- add hr before exclude -------------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		$params["elementor_condition"] = $arrCustomOnlyCondition;
		
		$this->addHr("before_exclude_by",$params);
		
		
		// --------- add exclude by -------------
		
		$arrExclude = array();
		$arrExclude["current_post"] = __("Current Post", "unlimited-elements-for-elementor");
		
		if($isForWooProducts == true){
			$arrExclude["out_of_stock"] = __("Out Of Stock Products", "unlimited-elements-for-elementor");
		}
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["is_multiple"] = true;
		
		$params["elementor_condition"] = $arrNotManualElementorCondition;
		
		//$params["description"] = esc_html__("Exclude Posts By", "unlimited-elements-for-elementor");
		
		$arrExclude = array_flip($arrExclude);
		
		$arrExcludeValues = "";
		
		$this->addMultiSelect($name."_excludeby", $arrExclude, __("Exclude By", "unlimited-elements-for-elementor"), $arrExcludeValues, $params);
		
		//----- hr -------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		$params["elementor_condition"] = $arrNotManualElementorCondition;
		
		$this->addHr("post_after_exclude",$params);
		
		
		//------- max items - current only--------
		
		$maxPostsPerPage = get_option("posts_per_page");
		
		$params = array("unit"=>"posts");
		$maxItems = UniteFunctionsUC::getVal($value, $name."_maxitems_current");
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["placeholder"] = __("$maxPostsPerPage posts if empty","unlimited-elements-for-elementor");
		
		$params["elementor_condition"] = $arrCurrentElementorCondition;
		
		$this->addTextBox($name."_maxitems_current", $maxItems, esc_html__("Max Posts", "unlimited-elements-for-elementor"), $params);
		
		
		//------- max items --------
		
		$params = array("unit"=>"posts");
		$maxItems = UniteFunctionsUC::getVal($value, $name."_maxitems", 10);
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["placeholder"] = __("100 posts if empty","unlimited-elements-for-elementor");
		
		//$params["description"] = "Enter how many Posts you wish to display, -1 for unlimited";
		
		$params["elementor_condition"] = $arrCustomAndRelatedElementorCondition;
		
		$this->addTextBox($name."_maxitems", $maxItems, esc_html__("Max Posts", "unlimited-elements-for-elementor"), $params);
		
		
		//----- orderby --------
		
		$arrOrder = UniteFunctionsWPUC::getArrSortBy($isForWooProducts);
		$arrOrder = array_flip($arrOrder);
		
		$arrDir = UniteFunctionsWPUC::getArrSortDirection();
		$arrDir = array_flip($arrDir);
		
		//---- orderby for custom and current -----
		
		$params = array();
		
		//$params[UniteSettingsUC::PARAM_ADDFIELD] = $name."_orderdir1";
		
		$orderBY = UniteFunctionsUC::getVal($value, $name."_orderby", "default");
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		//$params["description"] = esc_html__("Select how you wish to order posts", "unlimited-elements-for-elementor");
		
		if(!empty($arrCustomAndCurrentElementorCondition))
			$params["elementor_condition"] = $arrCustomAndCurrentElementorCondition;
		
		$this->addSelect($name."_orderby", $arrOrder, __("Order By", "unlimited-elements-for-elementor"), $orderBY, $params);
		
		//--- meta value param -------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["class"] = "alias";
		
		$arrCondition = $arrCustomAndCurrentElementorCondition;
		$arrCondition[$name."_orderby"] = array(UniteFunctionsWPUC::SORTBY_META_VALUE, UniteFunctionsWPUC::SORTBY_META_VALUE_NUM);
		
		$params["elementor_condition"] = $arrCondition;
		
		$this->addTextBox($name."_orderby_meta_key1", "" , __("&nbsp;&nbsp;Custom Field Name","unlimited-elements-for-elementor"), $params);
		
		$this->addControl($name."_orderby", $name."_orderby_meta_key1", "show", UniteFunctionsWPUC::SORTBY_META_VALUE.",".UniteFunctionsWPUC::SORTBY_META_VALUE_NUM);
		
		//---- order dir -----
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		//$params["description"] = esc_html__("Select order direction. Descending A-Z or Accending Z-A", "unlimited-elements-for-elementor");
		
		$params["elementor_condition"] = $arrCustomAndCurrentElementorCondition;
		
		$orderDir1 = UniteFunctionsUC::getVal($value, $name."_orderdir1", "default" );
		$this->addSelect($name."_orderdir1", $arrDir, __("&nbsp;&nbsp;Order By Direction", "unlimited-elements-for-elementor"), $orderDir1, $params);
		
		//---- hr before query id -----
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		
		
		$this->addHr("hr_after_order_dir", $params);
				
		
		//---- query id -----
		
		$isPro = GlobalsUC::$isProVersion;
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		if($isPro == true){
			
			$title = __("Query ID", "unlimited-elements-for-elementor");
			$params["description"] = __("Give your Query unique ID to been able to filter it in server side using add_filter() function","unlimited-elements-for-elementor");
			
		}else{		//free version
			
			$params["description"] = __("Give your Query unique ID to been able to filter it in server side using add_filter() function. This feature exists in a PRO Version only","unlimited-elements-for-elementor");
			$title = __("Query ID (pro)", "unlimited-elements-for-elementor");
			$params["disabled"] = true;
		}
		
		$queryID = UniteFunctionsUC::getVal($value, $name."_queryid");
		
		$this->addTextBox($name."_queryid", $queryID, $title, $params);
		
		//---- show debug -----
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_RADIOBOOLEAN;
		$params["description"] = __("Show the query for debugging purposes. Don't forget to turn it off before page release", "unlimited-elements-for-elementor");
		
		$this->addRadioBoolean($name."_show_query_debug", __("Show Query Debug", "unlimited-elements-for-elementor"), false, "Yes", "No", $params);
		
	}
	
	
	
	
	
}