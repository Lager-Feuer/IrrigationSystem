const humidityChartCanvas = document.getElementById('humidityChartCanvas');
var hchart;
var timestamps = [];
var humidity_levels = [];

updateChart(false);

function updateChart(reset = true) {
    if (reset) {
        //Reset chart
        hchart.destroy();
        humidity_levels = [];
        timestamps = [];
    }

    // Get values from datepicker elements
    var fromTimestamp = document.getElementById("fromTimestamp").value;
    var toTimestamp = document.getElementById("toTimestamp").value;

    //When the page loads the first time
    if (!fromTimestamp || !toTimestamp) {
        // Set Values for datepicker elements
        document.getElementById("fromTimestamp").value = getCurrentDate(24);
        document.getElementById("toTimestamp").value = getCurrentDate();
        fromTimestamp = document.getElementById("fromTimestamp").value;
        toTimestamp = document.getElementById("toTimestamp").value;
    }

    // Get Chart data between timestamps
    $.ajax({
        method: "GET",
        dataType: "html",
        url: "/php/hchart.php",
        data: {
            fromTimestamp: fromTimestamp,
            toTimestamp: toTimestamp
        },
        success: function(data) {
            var obj = JSON.parse(data);
            for (var i in obj) {
                timestamps.push(obj[i].timestamp);
                humidity_levels.push(obj[i].humidity_level);
            }

            const chartData = {
                labels: timestamps,
                datasets: [{
                    label: 'Feuchtigkeitslevel ihres Bewässerungssystems',
                    fill: true,
                    data: humidity_levels,
                    borderWidth: 2,
                    backgroundColor: "#00AFD3",
                    borderColor: "#003758"
                }]
            }

            const config = {
                type: 'line',
                data: chartData,
                options: {
                    scales: {
                        x: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Zeitstempel'
                            },
                            ticks: {
                                major: {
                                    enabled: true
                                },
                                color: (context) => context.tick && context.tick.major && '#FF0000',
                                font: function(context) {
                                    if (context.tick && context.tick.major) {
                                        return {
                                            weight: 'bold'
                                        };
                                    }
                                }
                            }
                        },
                        y: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Feuchtigkeitslevel in %'
                            },
                            min: 0,
                            max: 100
                        }
                    }
                }
            }
            hchart = new Chart(humidityChartCanvas, config);
        },
        error: function(error) {
            alert('Error occured: ' + error);
        }
    })
}


//return Date in format (YYYY-MM-DD )
function getCurrentDate(offsetHours = 0) {
    var cDate = new Date(Date.now());
    if( offsetHours > 0 ) cDate.setHours(cDate.getHours() - offsetHours)
    var cDateString = cDate.getFullYear();

    cDateString += "-" + appendLeadingZero(cDate.getMonth() + 1);
    cDateString += "-" + appendLeadingZero(cDate.getDate());
    cDateString += " " + appendLeadingZero(cDate.getHours());
    cDateString += ":" + appendLeadingZero(cDate.getMinutes());
    cDateString += ":" + appendLeadingZero(cDate.getSeconds());
    return cDateString;
}

function appendLeadingZero(cDatetime) {
    if (cDatetime < 10) return "0" + cDatetime;
    else return cDatetime;
}