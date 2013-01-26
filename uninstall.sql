SET @configuration_group_id=0;
SELECT @configuration_group_id:=configuration_group_id
FROM configuration_group
WHERE configuration_group_title= 'Simple SEO URL Configuration'
LIMIT 1;
DELETE FROM configuration WHERE configuration_group_id = @configuration_group_id;
DELETE FROM configuration_group WHERE configuration_group_id = @configuration_group_id;

#Zen Cart v1.5.0+ only Below! Skip if using an older version!
DELETE FROM admin_pages WHERE page_key = 'configSSU' LIMIT 1;
DELETE FROM admin_pages WHERE page_key = 'extrasSSU' LIMIT 1;