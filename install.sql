DROP TABLE IF EXISTS ssu_cache;
CREATE TABLE `ssu_cache` (
  `id` int(11) NOT NULL auto_increment,
  `type` varchar(55) NOT NULL,
  `referring_id` int(11) NOT NULL,
  `file` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `type` (`type`,`referring_id`,`file`)
);

DROP TABLE IF EXISTS `links_aliases`;
CREATE TABLE IF NOT EXISTS `links_aliases` (
  `id` int(10) NOT NULL auto_increment,
  `link_url` varchar(255) NOT NULL,
  `link_alias` varchar(255) NOT NULL,
  `status` tinyint(1) unsigned NOT NULL default '1',
  `alias_type` varchar(100) NOT NULL default 'none',
  `referring_id` int(10) NOT NULL default '0',
  `permanent_link` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
);

SET @configuration_group_id=0;
SELECT (@configuration_group_id:=configuration_group_id) 
FROM configuration_group 
WHERE configuration_group_title = 'Simple SEO URL Configuration' 
LIMIT 1;
DELETE FROM configuration WHERE configuration_group_id = @configuration_group_id AND @configuration_group_id != 0;
DELETE FROM configuration_group WHERE configuration_group_id = @configuration_group_id AND @configuration_group_id != 0;

INSERT INTO configuration_group (configuration_group_id, configuration_group_title, configuration_group_description, sort_order, visible) VALUES (NULL, 'Simple SEO URL Configuration', 'Set Simple SEO URL Options', '1', '1');
SET @configuration_group_id=last_insert_id();
UPDATE configuration_group SET sort_order = @configuration_group_id WHERE configuration_group_id = @configuration_group_id;

INSERT INTO configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES 
('File extension', 'SSU_FILE_EXTENSION', '', 'Set the file extension you want (without the dot). Recommend: leave it blank. For more info please read the docs', @configuration_group_id, 1, NOW(), NOW(), NULL, NULL), 
('Name delimiter', 'SSU_NAME_DELIMITER', '-', 'Set delimiter to replace all non alpha-numeric characters in product/category names', @configuration_group_id, 1, NOW(), NOW(), NULL, 'zen_cfg_select_option(array(\'-\', \'.\'),'), 
('ID delimiter', 'SSU_ID_DELIMITER', '-', 'Set delimiter separate product/category names and their ids', @configuration_group_id, 1, NOW(), NOW(), NULL, 'zen_cfg_select_option(array(\'-\', \'.\'),'), 
('Set max category level', 'SSU_MAX_LEVEL', '2', 'When you visit sub categories, SSU will stack the name of the sub cat and their parent cats into the link. You may want to limit the number of category names should be in a link', @configuration_group_id, 1, NOW(), NOW(), NULL, NULL), 
('Exclude list', 'SSU_EXCLUDE_LIST', 'advanced_search_result,redirect,popup_image_additional,download,wordpress', 'Set the list of pages that should be excluded from using seo style links, separated by comma with no blank space. Do not change this if you are not sure what you are doing', @configuration_group_id, 1, NOW(), NOW(), NULL, NULL), 
('Set minimum word length', 'SSU_MINIMUM_WORD_LENGTH', '0', 'You can set a minimum word length here so SSU will remove any word shorter than then length from the product/category names displayed on the links. 1 or less mean no limit', @configuration_group_id, 1, NOW(), NOW(), NULL, NULL), 
('Set maximum name length', 'SSU_MAX_NAME_LENGTH', '0', 'You can set a maximum length here so SSU will trim your product/category names displayed on links to the set length. 0 or less means no limit', @configuration_group_id, 1, NOW(), NOW(), NULL, NULL), 
('Set SSU Status', 'SSU_STATUS', 'false', 'Turn SSU on or off', @configuration_group_id, 1, NOW(), NOW(), NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),'), 
('Set SSU Multi Language Status', 'SSU_MULTI_LANGUAGE_STATUS', 'false', 'Do not turn this on unless your site uses multi-languages', @configuration_group_id, 1, NOW(), NOW(), NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),'), 
('Set Query Key\'s Exclude List', 'SSU_QUERY_EXCLUDE_LIST', 'zenid,gclid,number_of_uploads,number_of_downloads,action,sort,page,disp_order,filter_id,alpha_filter_id,currency', 'Set the query keys that you want SSU to avoid converting, separated by comma with no blank space', @configuration_group_id, 1, NOW(), NOW(), NULL, NULL), 
('Set Auto Alias Status', 'SSU_AUTO_ALIAS', 'false', 'Let SSU automatically remove identifiers from links, you have to have ssu alias on.', @configuration_group_id, 1, NOW(), NOW(), NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),'), 
('Set Category Separator', 'SSU_CATEGORY_SEPARATOR', '/', 'Set separator to separate category names.', @configuration_group_id, 1, NOW(), NOW(), NULL, NULL), 
('Hide default language identifier', 'SSU_MULTI_LANGUAGE_DEFAULT_IDENTIFIER', 'true', 'This option is useful for sites that use multi-languages. You can tell SSU to not add language identifier into the links for the default language.', @configuration_group_id, 1, NOW(), NOW(), NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),'),
('Cache reset time', 'SSU_CACHE_RESET_TIME', '', 'This value is updated automatically, do not edit', @configuration_group_id, 18, NOW(), NOW(), NULL, NULL);

# Register the configuration page for Admin Access Control
INSERT IGNORE INTO admin_pages (page_key,language_key,main_page,page_params,menu_key,display_on_menu,sort_order) VALUES ('configSSU','BOX_CONFIGURATION_SSU','FILENAME_CONFIGURATION',CONCAT('gID=',@configuration_group_id),'configuration','Y',@configuration_group_id);

# Register the Extras page for Admin Access Control
INSERT IGNORE INTO admin_pages (`page_key`, `language_key`, `main_page`, `page_params`, `menu_key`, `display_on_menu`, `sort_order`) VALUES ('extrasSSU', 'BOX_SSU', 'FILENAME_SSU', '', 'extras', 'Y', @configuration_group_id);  