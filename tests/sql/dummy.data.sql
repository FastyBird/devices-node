INSERT IGNORE INTO `fb_devices` (`device_id`, `parent_id`, `device_name`, `device_title`, `device_comment`, `device_state`, `device_enabled`, `params`, `created_at`, `updated_at`, `device_type`) VALUES
(_binary 0x69786D15FD0C4D9F937833287C2009FA, NULL, 'First device', NULL, NULL, 'init', 1, '[]', '2020-03-19 14:03:48', '2020-03-22 20:12:07', 'physical'),
(_binary 0xBF4CD8702AAC45F0A85EE1CEFD2D6D9A, NULL, 'Second device', 'Custom title', NULL, 'unknown', 1, '[]', '2020-03-20 21:54:32', '2020-03-20 21:54:32', 'physical'),
(_binary 0xE36A27881EF84CDFAB094735F191A509, NULL, 'Third device', NULL, 'Custom comment', 'unknown', 1, '[]', '2020-03-20 21:56:41', '2020-03-20 21:56:41', 'physical'),
(_binary 0xA1036FF86EE84405AAED58BAE0814596, _binary 0x69786D15FD0C4D9F937833287C2009FA, 'Child device', NULL, 'This is child', 'unknown', 1, '[]', '2020-03-20 21:56:41', '2020-03-20 21:56:41', 'physical');

INSERT IGNORE INTO `fb_physicals_devices` (`device_id`, `device_identifier`) VALUES
(_binary 0x69786D15FD0C4D9F937833287C2009FA, 'first-device'),
(_binary 0xBF4CD8702AAC45F0A85EE1CEFD2D6D9A, 'second-device'),
(_binary 0xE36A27881EF84CDFAB094735F191A509, 'third-device'),
(_binary 0xA1036FF86EE84405AAED58BAE0814596, 'child-device');

INSERT IGNORE INTO `fb_devices_properties` (`property_id`, `device_id`, `property_property`, `property_name`, `property_settable`, `property_queryable`, `property_datatype`, `property_unit`, `property_format`, `created_at`, `updated_at`) VALUES
(_binary 0xBBCCCF8C33AB431BA795D7BB38B6B6DB, _binary 0x69786D15FD0C4D9F937833287C2009FA, 'uptime', 'uptime', 0, 1, 'integer', NULL, NULL, '2020-03-20 09:18:20', '2020-03-20 09:18:20'),
(_binary 0x28BC0D382F7C4A71AA7427B102F8DF4C, _binary 0x69786D15FD0C4D9F937833287C2009FA, 'rssi', 'rssi', 0, 1, 'integer', NULL, NULL, '2020-03-20 09:18:20', '2020-03-20 09:18:20');

INSERT IGNORE INTO `fb_devices_controls` (`control_id`, `device_id`, `control_name`, `created_at`, `updated_at`) VALUES
(_binary 0x7C055B2B60C3401793DBE9478D8AA662, _binary 0x69786D15FD0C4D9F937833287C2009FA, 'reset', '2020-03-20 09:18:20', '2020-03-20 09:18:20');

INSERT IGNORE INTO `fb_devices_configuration` (`configuration_id`, `device_id`, `configuration_type`, `configuration_name`, `configuration_title`, `configuration_comment`, `configuration_default`, `created_at`, `updated_at`, `params`) VALUES
(_binary 0x138C6CFCED49476B9F1E6EE1DCB24F0B, _binary 0x69786D15FD0C4D9F937833287C2009FA, 'number', 'sensor_expected_power', NULL, NULL, NULL, '2019-11-26 18:59:07', '2019-12-10 20:35:50', '{"min_value":0,"max_value":500,"step_value":1}'),
(_binary 0x19EB2B65ABCE4061B5914FF1A4B1500F, _binary 0x69786D15FD0C4D9F937833287C2009FA, 'number', 'btn_delay', NULL, NULL, '500', '2019-08-30 18:22:04', '2019-12-10 20:35:50', '{"min_value":0,"max_value":1000,"step_value":100}'),
(_binary 0x1B061A974D9947329EA5EA2EB777E970, _binary 0x69786D15FD0C4D9F937833287C2009FA, 'select', 'sensor_energy_units', NULL, NULL, '0', '2019-11-26 18:59:07', '2019-11-26 18:59:07', '{"select_values":[{"value":"0","name":"joules"},{"value":"1","name":"kilowatts_hours"}]}'),
(_binary 0x20FBA951E76D4D6BA572DEC02C6D8DE8, _binary 0x69786D15FD0C4D9F937833287C2009FA, 'number', 'sensor_expected_current', NULL, NULL, NULL, '2019-11-26 18:59:07', '2019-12-10 20:35:50', '{"min_value":0,"max_value":500,"step_value":1}'),
(_binary 0x3FF0029F7FE3405EA3EFEDAAD08E2FFA, _binary 0x69786D15FD0C4D9F937833287C2009FA, 'number', 'sensor_expected_voltage', NULL, NULL, NULL, '2019-11-26 18:59:07', '2019-12-10 20:35:50', '{"min_value":0,"max_value":500,"step_value":1}'),
(_binary 0x4897727BB74E47AEA0E7953C8F3E4F06, _binary 0x69786D15FD0C4D9F937833287C2009FA, 'number', 'sensor_save_interval', NULL, NULL, '0', '2019-11-26 18:59:07', '2019-12-10 20:35:50', '{"min_value":0,"max_value":200,"step_value":1}'),
(_binary 0x81BDF07B7DC94E3A98B14DC5E9F16B55, _binary 0x69786D15FD0C4D9F937833287C2009FA, 'select', 'sensor_read_interval', NULL, NULL, '6', '2019-08-31 14:32:50', '2019-08-31 14:32:50', '{"select_values":[{"value":"1","name":"1"},{"value":"6","name":"6"},{"value":"10","name":"10"},{"value":"15","name":"15"},{"value":"30","name":"30"},{"value":"60","name":"60"},{"value":"300","name":"300"},{"value":"600","name":"600"},{"value":"900","name":"900"},{"value":"1800","name":"1800"},{"value":"3600","name":"3600"}]}'),
(_binary 0x8A41D824B1DB4548B07E606117DD7309, _binary 0x69786D15FD0C4D9F937833287C2009FA, 'number', 'sensor_report_interval', NULL, NULL, '10', '2019-08-31 14:32:50', '2019-12-10 20:35:50', '{"min_value":1,"max_value":60,"step_value":1}'),
(_binary 0x8D933E4C1FC94361BA09EEBEE4592776, _binary 0x69786D15FD0C4D9F937833287C2009FA, 'select', 'sensor_power_units', NULL, NULL, '0', '2019-11-26 18:59:07', '2019-11-26 18:59:07', '{"select_values":[{"value":"0","name":"watts"},{"value":"1","name":"kilowatts"}]}');

INSERT IGNORE INTO `fb_physicals_devices_credentials` (`credentials_id`, `device_id`, `credentials_username`, `credentials_password`, `created_at`, `updated_at`) VALUES
(_binary 0x09F28B106B4C4421839C4E484FA7011D, _binary 0x69786D15FD0C4D9F937833287C2009FA, 'username-one', 'password', '2020-03-19 14:03:49', '2020-03-19 14:03:49'),
(_binary 0x40B6ED308451422281EEFEA4AB5C15D7, _binary 0xE36A27881EF84CDFAB094735F191A509, 'username-two', 'password', '2020-03-20 21:56:41', '2020-03-20 21:56:41'),
(_binary 0x617D8E60365A4EEC8E8725B3092EBFCA, _binary 0xBF4CD8702AAC45F0A85EE1CEFD2D6D9A, 'username-three', 'password', '2020-03-20 21:54:32', '2020-03-20 21:54:32');

INSERT IGNORE INTO `fb_physicals_devices_firmwares` (`firmware_id`, `device_id`, `firmware_name`, `firmware_manufacturer`, `firmware_version`, `created_at`, `updated_at`) VALUES
(_binary 0x06FEDDF463E248BCAF13F355F01FFBE0, _binary 0x69786D15FD0C4D9F937833287C2009FA, NULL, 'fastybird', NULL, '2020-03-22 15:09:54', '2020-03-22 21:27:32');

INSERT IGNORE INTO `fb_physicals_devices_hardwares` (`hardware_id`, `device_id`, `hardware_manufacturer`, `hardware_model`, `hardware_version`, `hardware_mac_address`, `created_at`, `updated_at`) VALUES
(_binary 0x8059B830B76D4F98BE9C53BD06EAB9A5, _binary 0x69786D15FD0C4D9F937833287C2009FA, 'itead', 'sonoff_basic', 'rev1', '807d3a3dbe6d', '2020-03-22 21:25:08', '2020-03-22 21:25:08');

INSERT IGNORE INTO `fb_channels` (`channel_id`, `device_id`, `channel_name`, `channel_title`, `channel_comment`, `channel_channel`, `params`, `created_at`, `updated_at`) VALUES
(_binary 0x17C59DFA2EDD438E8C49FAA4E38E5A5E, _binary 0x69786D15FD0C4D9F937833287C2009FA, 'Channel one', NULL, NULL, 'channel-one', '[]', '2020-03-20 09:22:12', '2020-03-20 22:37:14'),
(_binary 0x6821F8E9AE694D5C9B7CD2B213F1AE0A, _binary 0x69786D15FD0C4D9F937833287C2009FA, 'Channel two', NULL, NULL, 'channel-two', '[]', '2020-03-20 09:22:13', '2020-03-20 09:22:13');

INSERT IGNORE INTO `fb_channels_properties` (`property_id`, `channel_id`, `property_property`, `property_name`, `property_settable`, `property_queryable`, `property_datatype`, `property_unit`, `property_format`, `created_at`, `updated_at`) VALUES
(_binary 0xBBCCCF8C33AB431BA795D7BB38B6B6DB, _binary 0x17C59DFA2EDD438E8C49FAA4E38E5A5E, 'switch', 'switch', 1, 1, 'enum', NULL, 'on,off,toggle', '2019-12-09 23:19:45', '2019-12-09 23:19:49'),
(_binary 0x28BC0D382F7C4A71AA7427B102F8DF4C, _binary 0x6821F8E9AE694D5C9B7CD2B213F1AE0A, 'temperature', 'temperature', 0, 1, 'float', '°C', NULL, '2019-12-08 18:17:39', '2019-12-09 23:09:56'),
(_binary 0x24C436F4A2E44D2BB9101A3FF785B784, _binary 0x6821F8E9AE694D5C9B7CD2B213F1AE0A, 'humidity', 'humidity', 0, 1, 'float', '%', NULL, '2019-12-08 18:17:39', '2019-12-09 23:10:00');
