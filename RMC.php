<?php 

namespace Core;

class RMC{
	
    const DOOR_TYPE = 1;
    const WATER_TYPE = 2;
    const LIGHT_TYPE = 3;
    const TEMP_HUMIDITY_TYPE = 4;
    const TEMP_TYPE = 5;
    const G_SENSOR_TYPE = 6;
    const RAP_TYPE = 7;
    const GPIO_TYPE = 8;
    const RELAY_TYPE = 9;
    const VOLTAGE_TYPE = 10;
    const NOISE_TYPE = 11;
    const SMOK_TYPE = 12;
    const ELECTRIC_METER_TYPE = 13;
    const EXT_TEMP_TYPE = 14;
    const EXT_RAP_TYPE = 15;
    const RTC_TYPE = 16;

    const HEADER = "a1a2a3";
    const FOOTER = "a3a2a1";
}

?>