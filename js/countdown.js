
$(document).ready(function() {

    var currentDate, targetDate, timeDiff;

    $.getJSON("time.php", function(data){
        currentDate = data.currentTime;
        targetDate = data.targetTime;
        init();

    });
    function init() {

        var Days, Hours, Minutes, Seconds;

        timeDiff = targetDate - currentDate;

        function updateTime() {
            Seconds = timeDiff;

            Days = Math.floor(Seconds / 86400);
            Seconds -= Days * 86400;
            Hours = Math.floor(Seconds / 3600);
            Seconds -= Hours * 3600;
            Minutes = Math.floor(Seconds / 60);
            Seconds -= Minutes * 60;

            Seconds = Math.floor(Seconds);
        }

        function tick() {
            clearTimeout(timer);
            updateTime();
            displayTime();

            if (timeDiff > 0) {
                timeDiff--;
                timer = setTimeout(tick, 1 * 1000);
            } else {
                $("#timeDisplay").html("");
            }

        }


        function displayTime() {
            var out;

            out = "<div class='countdown-box' style='margin-left:0;'><div class='align-time'>" + Days + "<span class='text-count'><br>days</span> </div></div>" +
                "<div class='countdown-box'><div class='align-time'>" + Hours + "<span class='text-count'><br>hours</span> </div></div> " +
                "<div class='countdown-box'><div class='align-time'>" + Minutes + "<span class='text-count'><br>minutes</span> </div></div> " +
                "<div class='countdown-box'><div class='align-time'>" + Seconds + "<span class='text-count'><br>seconds</span> </div></div>";
            $("#timeDisplay").html(out);
        }

        var timer = setTimeout(tick, 1 * 1000);
    }
});