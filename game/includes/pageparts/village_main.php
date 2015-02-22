<h2>Uitbreidingopties</h2>
    Huidige level: Level <?php echo $current_level ?> <br/>
    <?php if ($maxed_out === false) { ?>
    Volgende level: Level <?php echo $next_level ?> <br/> <?php } ?>
    Maximum level: Level <?php echo $max_level ?> <br/>

<h2>Productiestatistieken</h2>
    productie op <em>huidige</em> level :  <?php echo $current_resources_per_hour." ".$resource ?> <br/>
    <?php if ($maxed_out === false) { ?>
    productie op <em>volgende</em> level :  <?php echo $next_level_resources_per_hour." ".$resource ?> <br/> <?php } ?>

<h2>Overhead kosten</h2>
    <?php if ($maxed_out === false) { ?>
    Uitbreidingskosten naar Level <?php echo $next_level ?>:
    <ul class="show_resources">
        <li>Staal: <?php echo $next_level_cost["steel"] ?></li>
        <li>Steenkool: <?php echo $next_level_cost["coal"] ?></li>
        <li>Hout: <?php echo $next_level_cost["wood"] ?></li>
    </ul>
    <br/> <?php } ?>

<!-- These lines are added by the programmer and are references to a possible future extention to the game. THIS IS NOT INCLUDED IN THE DATABASE; -->
<?php if ($maxed_out === false) { ?>
    Vereiste arbeiders op Level <?php echo $current_level ?>:
    <ul class="show_resources">
        <li>Arbeiders: 5</li>
        <li>Bedienden: 1</li>
    </ul>
    <br/>
    Vereiste arbeiders op Level<?php echo $next_level ?>:
    <ul class="show_resources">
        <li>Arbeiders: 10</li>
        <li>Bedienden: 2</li>
    </ul>
    <br/> <?php } ?>
<!-- These lines are added by the programmer and are references to a possible future extention to the game. THIS IS NOT INCLUDED IN THE DATABASE; -->

</div>
<?php

// Inclusion of the footer file
include "includes/pageparts/footer.php";
?>