<?php

function generate_new_city($user_id, $map_density, $map_size, $static_area, $dynamic_area){
    global $conn;

    $coordinates = calculate_random_coordinates($map_density, $map_size, $static_area, $dynamic_area);

    $city_name = random_city_name();
    $update_successful = false;
    $focus_successful = false;

    $x = $coordinates["x"];
    $y = $coordinates["y"];
    $points = 0;

    $stmt = $conn->query("INSERT INTO city (x, y, city_name, user_id, points) VALUES ($x, $y, '$city_name', $user_id, $points)");

    $city_id = $conn->insert_id;

    if (update_data("user", "first_city", 0, "user_id", $user_id) === true) $update_successful = true;
    if (update_data("user", "focus_city", $city_id, "user_id", $user_id) === true) $focus_successful = true;

    if ($stmt) {
        if ($conn->affected_rows >= 1 && $update_successful === true && $focus_successful === true) return true;
        else return false;
    } else {
        return $conn->error;
    }
}

function random_city_name() {
    // A random city name is generated based on these parts
    $city_name_parts = array(
        array("man", "lich", "nor", "peter", "pre", "ports", "south", "wake", "new", "glas"),
        array("ting", "chef", "sunder", "wells", "hamp", "edin", "stir", "wolver", "swan"),
        array("ville", "port", "field", "ford", "bridge", "stol", "bury", "chester", "try", "ham")
    );

    // 0 based index
    $count_part_one = count($city_name_parts[0]) - 1;
    $count_part_two = count($city_name_parts[1]) - 1;
    $count_part_three = count($city_name_parts[2]) - 1;

    // Select random name parts and the first character is uppercase
    return ucfirst($city_name_parts[0][rand(0, $count_part_one)] . $city_name_parts[1][rand(0, $count_part_two)] . $city_name_parts[2][rand(0, $count_part_three)]);
}

function calculate_random_coordinates($map_density, $map_size, $static_area, $dynamic_area) {
    $x = 0;
    $y = 0;

    $cities_count = count_records(1, "city", 1);

    $density_length = sqrt($cities_count / $map_density);

    do {
        if (is_int(($map_size + 1) / 2)) {
            // Todo: even map size + 1
        } else {
            if ($density_length < (($static_area * 2) + 1)) {
                $min = ($map_size / 2) - $static_area;
                $max = ($map_size / 2) + $static_area;

                $x = rand($min, $max);
                $y = rand($min, $max);
            } else {
                $left_min = ($map_size / 2) - floor($density_length / 2) - $dynamic_area;
                $left_max = ($map_size / 2) - floor($density_length / 2);
                $right_max = ($map_size / 2) + floor($density_length / 2) + $dynamic_area;
                $right_min = ($map_size / 2) + floor($density_length / 2);

                if ($left_min < 0 || $right_max > $map_size) {
                    $x = rand(0, $map_size);
                    $y = rand(0, $map_size);
                } else {
                    $random_quadrant = rand(0, 9999);

                    $total_length = (($right_max - $left_min) * 2) + (($right_min - $left_max) * 2);

                    $long_piece = (($right_max - $left_min) / $total_length) * 10000;
                    $short_piece = (($right_min - $left_max) / $total_length) * 10000;

                    if ($random_quadrant < $long_piece) {
                        $x = rand($left_min, $right_max);
                        $y = rand($left_min, $left_max);
                    } elseif ($random_quadrant < $long_piece + $short_piece) {
                        $x = rand($right_min, $right_max);
                        $y = rand($left_max, $right_min);
                    } elseif ($random_quadrant < $long_piece + $long_piece + $short_piece) {
                        $x = rand($left_min, $right_max);
                        $y = rand($right_min, $right_max);
                    } else {
                        $x = rand($left_min, $left_max);
                        $y = rand($left_max, $right_min);
                    }
                }
            }
        }
        // Todo: cache requested data
    } while (city_exists($x, $y));

    return array("x" => $x, "y" => $y);
}

function city_exists($x, $y) {
    global $conn;

    // Perform a mysql query
    $stmt = $conn->query("SELECT COUNT(*) AS data_count FROM city WHERE x=" . $x . " AND y=" . $y);

    $row = $stmt->fetch_assoc();

    if ($stmt) {
        // Count the rows, more or equal than 1 return true
        if ($row["data_count"] >= 1) return true;
        else return false;
    } else {
        return $conn->error;
    }
}

function get_cities($x_min, $x_max, $y_min, $y_max) {
    global $conn;

    $stmt = $conn->query("SELECT c.city_name, c.user_id, c.x, c.y, u.username, c.points FROM city c INNER JOIN user u ON c.user_id=u.user_id WHERE x BETWEEN $x_min AND $x_max AND y BETWEEN $y_min AND $y_max");

    $cities = array();

    if ($stmt) {
        while($city = $stmt->fetch_assoc()) {
            $cities[] = $city;
        }

        return $cities;
    } else {
        return $conn->error;
    }
}

function focus_city_coordinates($user_id) {
    global $conn;

    $stmt = $conn->query("SELECT c.x, c.y FROM city c INNER JOIN user u ON u.user_id=c.user_id WHERE u.user_id=$user_id AND u.focus_city=c.city_id");

    if ($stmt) {
        return $stmt->fetch_assoc();
    } else {
        return $conn->error;
    }
}