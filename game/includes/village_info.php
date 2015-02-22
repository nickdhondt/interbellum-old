<?php

// This file gets the needed data in order to fill in the building info.
// Before including this file, it's necessary to declare $building_type for this file to work.
// This file returns only the common variables used in all the buildings of the city.
// Not common variables are NOT calculated in this file.

//Pre-define values to avoid undeclared statements.
    $next_level_cost = array();
    $next_level = 0;
    $next_level_resources_per_hour = 0;
    $current_level = 0;
    $max_level = 0;
    $resource = "";

//Building Determination
$building_resource = explode(';', get_array_name_from_page());
    $building = $building_resource[0];
    $resource = $building_resource[1];

//Special Building Properties
    //Resource_Constant / capacity_constant
    //Base_gain / base_capacity
    if(($building == "storage") || ($building == "kitchen"))
    {
        $constant_factor = "capacity_constant";
        $base_factor = "base_capacity";
        $type = "stock";
    } else {
        $constant_factor = "resource_constant";
        $base_factor = "base_gain";
        $type = "make";
    }

if(($building != false) && ($resource != false)){
//Information Gathering
    //Gather the information required for this building
        //Variables with the need of the building name.
        $level_info = get_building_level_info()[$building];
        $buildings_data = get_buildings_data($city_id, $building);
        $current_level = $buildings_data[$building];

        //Variables without the need of the building name.
        $max_level = $level_info["max_level"];
        if($type == "make") $current_resources_per_hour = calculate_resource_per_hour($level_info[$base_factor], $current_level, $level_info[$constant_factor]);
        if($type == "stock") $current_resources_per_hour = calculate_storage_capacity($level_info[$base_factor], $current_level, $level_info[$constant_factor]);
        $buildings_next_level = buildings_next_level($city_id);

    //Check whether or not the last level has been reached.
    $maxed_out = false;
    if($current_level < $max_level)
    {
        $next_level = $buildings_next_level[$building];
        if($type == "make") $next_level_resources_per_hour = calculate_resource_per_hour($level_info[$base_factor], $next_level, $level_info[$constant_factor]);
        if($type == "stock") $next_level_resources_per_hour = calculate_storage_capacity($level_info[$base_factor], $next_level, $level_info[$constant_factor]);
        $next_level_cost = calculate_cost($level_info["base_steel_cost"], $level_info["base_coal_cost"], $level_info["base_wood_cost"], $next_level, $level_info["cost_constant"]);
    } else {
        if($max_level > $current_level) $maxed_out = true;
    }
}