</main>
<footer>
    <div>
        <?php echo date("d F - H:i:s"); ?> &mdash; Gegenereerd in <?php

        // Displaying the serverdate and the execution time of the script
        // $page_cal_start is declared in functions.php at the beginning of each page (functions.php in included at the top of each page)
        $page_cal_microseconds = (microtime(true) - $page_cal_start) * 1000;
        echo number_format($page_cal_microseconds, 0, ",", ".");

        mysqli_close($connection);

        ?>ms
    </div>
    <div>
        &copy; Goldenratio Interactive
    </div>
</footer>
</div>
</body>
</html>