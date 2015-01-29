</main>
<footer>
    <div>
        <?php echo date("d F - H:i:s"); ?> &mdash; Gegenereerd in <?php

        $page_cal_microseconds = (microtime(true) - $page_cal_start) * 1000;
        echo number_format($page_cal_microseconds, 0, ",", ".");

        ?>ms
    </div>
    <div>
        &copy; Goldenratio
    </div>
</footer>
</div>
</body>
</html>