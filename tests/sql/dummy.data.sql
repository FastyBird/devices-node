INSERT IGNORE INTO `fb_physicals_devices_firmwares` (`firmware_id`, `device_id`, `firmware_name`, `firmware_manufacturer`, `firmware_version`, `created_at`, `updated_at`) VALUES
(_binary 0x06FEDDF463E248BCAF13F355F01FFBE0, _binary 0x69786D15FD0C4D9F937833287C2009FA, NULL, 'fastybird', NULL, '2020-03-22 15:09:54', '2020-03-22 21:27:32'),
(_binary 0x17C59DFA2EDD438E8C49FAA4E38E5A5E, _binary 0xE36A27881EF84CDFAB094735F191A509, NULL, 'fastybird', NULL, '2020-03-22 15:09:54', '2020-03-22 21:27:32');

INSERT IGNORE INTO `fb_physicals_devices_hardwares` (`hardware_id`, `device_id`, `hardware_manufacturer`, `hardware_model`, `hardware_version`, `hardware_mac_address`, `created_at`, `updated_at`) VALUES
(_binary 0x8059B830B76D4F98BE9C53BD06EAB9A5, _binary 0x69786D15FD0C4D9F937833287C2009FA, 'itead', 'sonoff_basic', 'rev1', '807d3a3dbe6d', '2020-03-22 21:25:08', '2020-03-22 21:25:08'),
(_binary 0xD8B0D2B4A47A4750A225DDFA973675D3, _binary 0xE36A27881EF84CDFAB094735F191A509, 'fastybird', 'fastybird_gw', 'rev1', '807d3a3dbe6d', '2020-03-22 21:25:08', '2020-03-22 21:25:08');

INSERT IGNORE INTO `fb_devices` (`device_id`, `parent_id`, `device_identifier`, `device_name`, `device_comment`, `device_state`, `device_enabled`, `params`, `created_at`, `updated_at`, `device_type`, `owner`) VALUES
(_binary 0x69786D15FD0C4D9F937833287C2009FA, NULL, 'first-device', 'First device', NULL, 'init', 1, '[]', '2020-03-19 14:03:48', '2020-03-22 20:12:07', 'network', '455354e8-96bd-4c29-84e7-9f10e1d4db4b'),
(_binary 0xBF4CD8702AAC45F0A85EE1CEFD2D6D9A, NULL, 'second-device', NULL, NULL, 'init', 1, '[]', '2020-03-20 21:54:32', '2020-03-20 21:54:32', 'network', '455354e8-96bd-4c29-84e7-9f10e1d4db4b'),
(_binary 0xE36A27881EF84CDFAB094735F191A509, NULL, 'third-device', 'Third device', 'Custom comment', 'unknown', 1, '[]', '2020-03-20 21:56:41', '2020-03-20 21:56:41', 'local', '455354e8-96bd-4c29-84e7-9f10e1d4db4b'),
(_binary 0xA1036FF86EE84405AAED58BAE0814596, _binary 0x69786D15FD0C4D9F937833287C2009FA, 'child-device', 'Child device', 'This is child', 'init', 1, '[]', '2020-03-20 21:56:41', '2020-03-20 21:56:41', 'local', null);

INSERT IGNORE INTO `fb_network_physicals_devices` (`device_id`, `hardware_id`, `firmware_id`) VALUES
(_binary 0x69786D15FD0C4D9F937833287C2009FA, binary 0x8059B830B76D4F98BE9C53BD06EAB9A5, binary 0x06FEDDF463E248BCAF13F355F01FFBE0),
(_binary 0xBF4CD8702AAC45F0A85EE1CEFD2D6D9A, null, null),
(_binary 0xE36A27881EF84CDFAB094735F191A509, 0xD8B0D2B4A47A4750A225DDFA973675D3, 0x17C59DFA2EDD438E8C49FAA4E38E5A5E);

INSERT IGNORE INTO `fb_local_physicals_devices` (`device_id`, `hardware_id`, `firmware_id`) VALUES
(_binary 0xA1036FF86EE84405AAED58BAE0814596, null, null);

INSERT IGNORE INTO `fb_devices_properties` (`property_id`, `device_id`, `property_property`, `property_name`, `property_settable`, `property_queryable`, `property_datatype`, `property_unit`, `property_format`, `created_at`, `updated_at`) VALUES
(_binary 0xBBCCCF8C33AB431BA795D7BB38B6B6DB, _binary 0x69786D15FD0C4D9F937833287C2009FA, 'uptime', 'uptime', 0, 1, 'integer', NULL, NULL, '2020-03-20 09:18:20', '2020-03-20 09:18:20'),
(_binary 0x28BC0D382F7C4A71AA7427B102F8DF4C, _binary 0x69786D15FD0C4D9F937833287C2009FA, 'rssi', 'rssi', 0, 1, 'integer', NULL, NULL, '2020-03-20 09:18:20', '2020-03-20 09:18:20'),
(_binary 0x3FF0029F7FE3405EA3EFEDAAD08E2FFA, _binary 0x69786D15FD0C4D9F937833287C2009FA, 'status_led', 'status_led', 1, 1, 'enum', NULL, 'on,off', '2020-03-20 09:18:20', '2020-03-20 09:18:20');

INSERT IGNORE INTO `fb_devices_controls` (`control_id`, `device_id`, `control_name`, `created_at`, `updated_at`) VALUES
(_binary 0x7C055B2B60C3401793DBE9478D8AA662, _binary 0x69786D15FD0C4D9F937833287C2009FA, 'configure', '2020-03-20 09:18:20', '2020-03-20 09:18:20');

INSERT IGNORE INTO `fb_devices_configuration` (`configuration_id`, `device_id`, `configuration_type`, `configuration_configuration`, `configuration_name`, `configuration_comment`, `configuration_default`, `created_at`, `updated_at`, `params`) VALUES
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
(_binary 0x7C055B2B60C3401793DBE9478D8AA662, _binary 0x69786D15FD0C4D9F937833287C2009FA, 'deviceusrname', 'supersecretpassword', '2020-03-20 09:18:20', '2020-03-20 09:18:20');

INSERT IGNORE INTO `fb_channels` (`channel_id`, `device_id`, `channel_name`, `channel_comment`, `channel_channel`, `params`, `created_at`, `updated_at`) VALUES
(_binary 0x17C59DFA2EDD438E8C49FAA4E38E5A5E, _binary 0x69786D15FD0C4D9F937833287C2009FA, 'Channel one', NULL, 'channel-one', '[]', '2020-03-20 09:22:12', '2020-03-20 22:37:14'),
(_binary 0x6821F8E9AE694D5C9B7CD2B213F1AE0A, _binary 0x69786D15FD0C4D9F937833287C2009FA, 'Channel two', NULL, 'channel-two', '[]', '2020-03-20 09:22:13', '2020-03-20 09:22:13'),
(_binary 0xBBCCCF8C33AB431BA795D7BB38B6B6DB, _binary 0xBF4CD8702AAC45F0A85EE1CEFD2D6D9A, NULL, NULL, 'channel-one', '[]', '2020-03-20 09:22:13', '2020-03-20 09:22:13');

INSERT IGNORE INTO `fb_channels_properties` (`property_id`, `channel_id`, `property_property`, `property_name`, `property_settable`, `property_queryable`, `property_datatype`, `property_unit`, `property_format`, `created_at`, `updated_at`) VALUES
(_binary 0xBBCCCF8C33AB431BA795D7BB38B6B6DB, _binary 0x17C59DFA2EDD438E8C49FAA4E38E5A5E, 'switch', 'switch', 1, 1, 'enum', NULL, 'on,off,toggle', '2019-12-09 23:19:45', '2019-12-09 23:19:49'),
(_binary 0x28BC0D382F7C4A71AA7427B102F8DF4C, _binary 0x6821F8E9AE694D5C9B7CD2B213F1AE0A, 'temperature', 'temperature', 0, 1, 'float', '°C', NULL, '2019-12-08 18:17:39', '2019-12-09 23:09:56'),
(_binary 0x24C436F4A2E44D2BB9101A3FF785B784, _binary 0x6821F8E9AE694D5C9B7CD2B213F1AE0A, 'humidity', 'humidity', 0, 1, 'float', '%', NULL, '2019-12-08 18:17:39', '2019-12-09 23:10:00');

INSERT IGNORE INTO `fb_channels_controls` (`control_id`, `channel_id`, `control_name`, `created_at`, `updated_at`) VALUES
(_binary 0x15DB9BEF3B574A87BF67E3C19FC3BA34, _binary 0x17C59DFA2EDD438E8C49FAA4E38E5A5E, 'configure', '2020-03-20 09:18:20', '2020-03-20 09:18:20'),
(_binary 0x177D6FC719054FD9B847E2DA8189DD6A, _binary 0x6821F8E9AE694D5C9B7CD2B213F1AE0A, 'configure', '2020-03-20 09:18:20', '2020-03-20 09:18:20');

INSERT IGNORE INTO `fb_channels_configuration` (`configuration_id`, `channel_id`, `configuration_type`, `configuration_configuration`, `configuration_name`, `configuration_comment`, `configuration_default`, `created_at`, `updated_at`, `params`) VALUES
(_binary 0x008D911FE6D44B17AA28939839581CDE, _binary 0x17C59DFA2EDD438E8C49FAA4E38E5A5E, 'select', 'pulse_mode', NULL, NULL, '0', '2019-12-09 23:19:46', '2019-12-09 23:19:46', '{"select_values":[{"value":"0","name":"disabled"},{"value":"1","name":"normally_off"},{"value":"2","name":"normally_on"}]}'),
(_binary 0x31669D328CFA4A71BD06D536A2F94C2C, _binary 0x17C59DFA2EDD438E8C49FAA4E38E5A5E, 'select', 'relay_boot', NULL, NULL, '0', '2019-12-09 23:19:46', '2019-12-09 23:19:46', '{"select_values":[{"value":"0","name":"always_off"},{"value":"1","name":"always_on"},{"value":"2","name":"same_before"},{"value":"3","name":"toggle_before"}]}'),
(_binary 0x3F83999EC7904F429E8E4DB749D0E6D4, _binary 0x17C59DFA2EDD438E8C49FAA4E38E5A5E, 'number', 'pulse_time', NULL, NULL, '1', '2019-12-09 23:19:46', '2019-12-10 20:34:43', '{"min_value":1,"max_value":60,"step_value":0.1}'),
(_binary 0xC747CFDD654C4E5097156D14DBF20552, _binary 0x17C59DFA2EDD438E8C49FAA4E38E5A5E, 'select', 'on_disconnect', NULL, NULL, '0', '2019-12-09 23:19:46', '2019-12-09 23:19:46', '{"select_values":[{"value":"0","name":"no_change"},{"value":"1","name":"turn_off"},{"value":"2","name":"turn_on"}]}'),
(_binary 0x1FA8E5ACD2FB4531BA643C69863AEEA3, _binary 0x6821F8E9AE694D5C9B7CD2B213F1AE0A, 'select', 'pulse_mode', NULL, NULL, '0', '2019-12-08 16:49:29', '2019-12-08 16:49:29', '{"select_values":[{"value":"0","name":"disabled"},{"value":"1","name":"normally_off"},{"value":"2","name":"normally_on"}]}'),
(_binary 0x3134BA8EF1344BF29C80C977C4DEB0FB, _binary 0x6821F8E9AE694D5C9B7CD2B213F1AE0A, 'select', 'on_disconnect', NULL, NULL, '0', '2019-12-08 16:49:29', '2019-12-08 16:49:29', '{"select_values":[{"value":"0","name":"no_change"},{"value":"1","name":"turn_off"},{"value":"2","name":"turn_on"}]}'),
(_binary 0xD8B0D2B4A47A4750A225DDFA973675D3, _binary 0x6821F8E9AE694D5C9B7CD2B213F1AE0A, 'select', 'relay_boot', NULL, NULL, '0', '2019-12-08 16:49:29', '2019-12-08 16:49:29', '{"select_values":[{"value":"0","name":"always_off"},{"value":"1","name":"always_on"},{"value":"2","name":"same_before"},{"value":"3","name":"toggle_before"}]}');

INSERT IGNORE INTO `vmq_auth_acl` (`account_id`, `mountpoint`, `client_id`, `username`, `password`, `publish_acl`, `subscribe_acl`) VALUES
(_binary 0x1A46CF0ACCE94AB58B7575E2965F2957, '', '', 'deviceusrname', 'adc471fdc75ccdc1cc5c7e3a9fb3ff704d40b0cf72c71fe832ba266a5ecff236', '[{"pattern":"/fb/newDeviceUsername/#"}]', '[{"pattern":"/fb/newDeviceUsername/#"}]');
