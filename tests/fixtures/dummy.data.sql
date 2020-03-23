INSERT IGNORE INTO `fb_channels` (`channel_id`, `device_id`, `channel_name`, `channel_title`, `channel_comment`, `channel_channel`, `params`, `created_at`, `updated_at`) VALUES
	(_binary 0x17C59DFA2EDD438E8C49FAA4E38E5A5E, _binary 0x69786D15FD0C4D9F937833287C2009FA, 'Channel one', NULL, NULL, 'channel-one', '[]', '2020-03-20 09:22:12', '2020-03-20 22:37:14'),
	(_binary 0x6821F8E9AE694D5C9B7CD2B213F1AE0A, _binary 0x69786D15FD0C4D9F937833287C2009FA, 'Channel two', NULL, NULL, 'channel-two', '[]', '2020-03-20 09:22:13', '2020-03-20 09:22:13');

INSERT IGNORE INTO `fb_devices` (`device_id`, `parent_id`, `device_name`, `device_title`, `device_comment`, `device_state`, `device_enabled`, `params`, `created_at`, `updated_at`, `device_type`) VALUES
	(_binary 0x69786D15FD0C4D9F937833287C2009FA, NULL, 'First device', NULL, NULL, 'init', 1, '[]', '2020-03-19 14:03:48', '2020-03-22 20:12:07', 'physical'),
	(_binary 0xBF4CD8702AAC45F0A85EE1CEFD2D6D9A, NULL, 'Second device', 'Custom title', NULL, NULL, 1, '[]', '2020-03-20 21:54:32', '2020-03-20 21:54:32', 'physical'),
	(_binary 0xE36A27881EF84CDFAB094735F191A509, NULL, 'Third device', NULL, 'Custom comment', NULL, 1, '[]', '2020-03-20 21:56:41', '2020-03-20 21:56:41', 'physical');

INSERT IGNORE INTO `fb_physicals_devices` (`device_id`, `device_identifier`) VALUES
	(_binary 0x69786D15FD0C4D9F937833287C2009FA, 'first-device'),
	(_binary 0xBF4CD8702AAC45F0A85EE1CEFD2D6D9A, 'second-device'),
	(_binary 0xE36A27881EF84CDFAB094735F191A509, 'third-device');

INSERT IGNORE INTO `fb_devices_properties` (`property_id`, `device_id`, `property_property`, `property_name`, `property_settable`, `property_queryable`, `property_datatype`, `property_unit`, `property_format`, `created_at`, `updated_at`) VALUES
	(_binary 0xBBCCCF8C33AB431BA795D7BB38B6B6DB, _binary 0x69786D15FD0C4D9F937833287C2009FA, 'uptime', 'uptime', 0, 0, 'integer', NULL, NULL, '2020-03-20 09:18:20', '2020-03-20 09:18:20');

INSERT IGNORE INTO `fb_physicals_devices_credentials` (`credentials_id`, `device_id`, `credentials_username`, `credentials_password`, `created_at`, `updated_at`) VALUES
	(_binary 0x09F28B106B4C4421839C4E484FA7011D, _binary 0x69786D15FD0C4D9F937833287C2009FA, 'username-one', 'password', '2020-03-19 14:03:49', '2020-03-19 14:03:49'),
	(_binary 0x40B6ED308451422281EEFEA4AB5C15D7, _binary 0xE36A27881EF84CDFAB094735F191A509, 'username-two', 'password', '2020-03-20 21:56:41', '2020-03-20 21:56:41'),
	(_binary 0x617D8E60365A4EEC8E8725B3092EBFCA, _binary 0xBF4CD8702AAC45F0A85EE1CEFD2D6D9A, 'username-three', 'password', '2020-03-20 21:54:32', '2020-03-20 21:54:32');

INSERT IGNORE INTO `fb_physicals_devices_firmwares` (`firmware_id`, `device_id`, `firmware_name`, `firmware_manufacturer`, `firmware_version`, `created_at`, `updated_at`) VALUES
	(_binary 0x06FEDDF463E248BCAF13F355F01FFBE0, _binary 0x69786D15FD0C4D9F937833287C2009FA, NULL, 'fastybird', NULL, '2020-03-22 15:09:54', '2020-03-22 21:27:32');

INSERT IGNORE INTO `fb_physicals_devices_hardwares` (`hardware_id`, `device_id`, `hardware_manufacturer`, `hardware_model`, `hardware_version`, `hardware_mac_address`, `created_at`, `updated_at`) VALUES
	(_binary 0x8059B830B76D4F98BE9C53BD06EAB9A5, _binary 0x69786D15FD0C4D9F937833287C2009FA, 'generic', 'custom', NULL, NULL, '2020-03-22 21:25:08', '2020-03-22 21:25:08');
