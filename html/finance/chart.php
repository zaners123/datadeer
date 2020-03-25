<?php require "/var/www/php/header.php"; ?>
	<title>Line Chart</title>
	<script src="https://www.chartjs.org/dist/2.7.3/Chart.bundle.js"></script>
	<script src="https://www.chartjs.org/samples/latest/utils.js"></script>
	<style>
		canvas{
			-moz-user-select: none;
			-webkit-user-select: none;
			-ms-user-select: none;
		}
	</style>
<?php require "/var/www/php/bodyTop.php"; ?>
<div style="width: 90%">
	<canvas id="canvas">Use a newer browser</canvas>
</div>

<br>
<br>
<form onsubmit="return applySettings()" >
	<table>
		<tr>
			<td><label>Show Income:<input id="formIncome" onclick="applySettings()" checked="true" type="checkbox" class="bigbox"></label><br></td>
			<td><label>Show Expenses:<input id="formExpense" onclick="applySettings()" checked="true" type="checkbox" class="bigbox"></label><br></td>
			<td><label>Show Net Income:<input id="formNet" onclick="applySettings()" checked="true" type="checkbox" class="bigbox"></label><br></td>

<!--<td><label>Only show last year<input id="formLastYear" type="checkbox" class="bigbox"></label><br></td>-->
		</tr>
<tr><td style="visibility: hidden">.</td></tr>

		<!--TODO grouping by year<tr>
			<td><label for="dateUnit">Group by Month<input type="radio" id="dateUnit" value="month"></label><br></td>
			<td><label for="dateUnit">Group by Year<input type="radio" id="dateUnit" value="year"></label><br></td>
		</tr>-->

		<tr><td style="visibility: hidden">.</td></tr>


		<tr>
			<td><label>Month From:<input id="formFrom" type="date" required></label><br></td>
			<td><label>Month To:<input id="formTo" type="date" required></label><br></td>
		</tr>
	</table>
<br>
	<input type="submit" value="Update">
</form>
<!--<button id="randomizeData">Randomize Data</button>
<button id="addDataset">Add Dataset</button>
<button id="removeDataset">Remove Dataset</button>
<button id="addData">Add Data</button>
<button id="removeData">Remove Data</button>-->
<script>

	document.getElementById("formFrom").value = new Date().toISOString().substring(0,10);

	let datePlusYear = new Date();datePlusYear.setFullYear(datePlusYear.getFullYear()+1);
	document.getElementById("formTo").value = datePlusYear.toISOString().substring(0,10);

	let chartJsonData = JSON.parse('<?php
		require "backend.php";
		echo json_encode(getDoc("finance",$_SESSION["username"],$fin));?>');

	let config;
	let MONTHS;
    window.onload = function() {
        applySettings();
    };

    function sortJSONToChart(JSONData) {
        let divisionsSingle = {};

        for(let num in JSONData) {
            let partRead = JSONData[num];
            let year = new Date(partRead.time*1000).getFullYear();
            let month = new Date(partRead.time*1000).getMonth();

            if (divisionsSingle[year*12+month]==null) divisionsSingle[year*12+month]=[];
            divisionsSingle[year*12+month].push(partRead.amount);
        }

        return divisionsSingle;
    }

    //main applySettings is called when you want to update the graph
    function applySettings() {
        MONTHS = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        config = {
            type: 'line',
            data: {
                labels: [],
                datasets: []
            },
            options: {
                responsive: true,
                title: {
                    display: false,
                    text: 'Chart'
                },
                tooltips: {
                    mode: 'armybase.php',
                    intersect: false,
                },
                hover: {
                    mode: 'nearest',
                    intersect: true
                },
                scales: {
                    xAxes: [{
                        display: true,
	                    // stacked: true, (for bar charts)
                        scaleLabel: {
                            display: true,
                            labelString: 'Month'
                        }
                    }],
                    yAxes: [{
                        display: true,
	                    ticks: {
                            beginAtZero: true
	                    },
                        scaleLabel: {
                            display: true,
                            labelString: 'Dollars per Month'
                        }
                    }]
                }
            }
        };

        //main get data on single purchases grouped by month
        let singleMonthData = sortJSONToChart(chartJsonData.single);

		console.log(singleMonthData);


		//get the month to start at
	    let monthStart = new Date(document.getElementById("formFrom").value);
	    monthStart = monthStart.getFullYear()*12 + monthStart.getMonth();
	    //get the month to end at
	    let endMonthNum = new Date(document.getElementById("formTo").value);
        endMonthNum = endMonthNum.getFullYear()*12 + endMonthNum.getMonth();

        //main turn repeated income into single incomes

        for (; monthStart < endMonthNum; monthStart++) {
            for (let repKey in chartJsonData.multiple) {
                if (singleMonthData[monthStart] == null) singleMonthData[monthStart] = [];
                singleMonthData[monthStart].push(
                    //repData
                    chartJsonData.multiple[repKey].amount
                );
            }
        }

        console.log(singleMonthData);


        //main sort the chart monthly groups by month
        Object.keys(singleMonthData).sort().forEach(function (key) {
            singleMonthData[key] = singleMonthData[key];
        });

        //main apply divisionsSorted to the graph
	    config.data.datasets = [];
		let datasetNum = -1;

        if (document.getElementById("formNet").checked) {
            datasetNum++;
            config.data.datasets.push({
                label: 'Net Income',
                backgroundColor: "rgba(127,127, 127,.50)",
                borderColor: "rgb(0, 0,0)",
                data: [
                    //numbers, one per month
                ],
                fill: true,
            });
            for (let part in singleMonthData) {
                part = singleMonthData[part];
                let sum = 0;
                for (let val in part) {
                    // console.log(part[val]);
                    sum += Number(part[val]);
                }
                // console.log(sum);
                config.data.datasets[datasetNum].data.push(sum);
            }
        }

        if (document.getElementById("formExpense").checked) {
            datasetNum++;
            config.data.datasets.push({
                label: 'Expense',
                backgroundColor: "rgb(255,0, 0)",
                borderColor: "rgb(255, 0,0)",
                data: [
                    //numbers, one per month
                ],
                fill: true,
            });

            for (let part in singleMonthData) {
                part = singleMonthData[part];
                let sum = 0;
                for (let val in part) {
                    // console.log(part[val]);
                    if (part[val]<0) sum-=Number(part[val]);
                }
                // console.log(sum);
                config.data.datasets[datasetNum].data.push(sum);
            }
        }

        if (document.getElementById("formIncome").checked) {
            datasetNum++;
            config.data.datasets.push({
                label: 'Income',
                backgroundColor: "rgb(0, 255, 0)",
                borderColor: "rgb(0, 255, 0)",
                data: [
                    //numbers, one per month
                ],
                fill: true,
            });
            for (let part in singleMonthData) {
                part = singleMonthData[part];
                let sum = 0;
                for (let val in part) {
                    // console.log(part[val]);
                    if (part[val]>0) sum+=Number(part[val]);
                }
                // console.log(sum);
                config.data.datasets[datasetNum].data.push(sum);
            }
        }

        //main get x-axis labels
        config.data.labels=[];
        monthStart = new Date(document.getElementById("formFrom").value).getMonth();
        for (let monthN=0;monthN<config.data.datasets[datasetNum].data.length;monthN++) {
            config.data.labels.push(MONTHS[(monthN+monthStart)%12]);
        }


	    //draw once done
        let ctx = document.getElementById('canvas').getContext('2d');
        window.myLine = new Chart(ctx, config);
        return false;
    }

    /*document.getElementById('randomizeData').addEventListener('click', function() {
        config.data.datasets.forEach(function(dataset) {
            dataset.data = dataset.data.map(function() {
                return randomScalingFactor();
            });

        });

        window.myLine.update();
    });

    let colorNames = Object.keys(window.chartColors);


    document.getElementById('addData').addEventListener('click', function() {
        if (config.data.datasets.length > 0) {
            let month = MONTHS[config.data.labels.length % MONTHS.length];
            config.data.labels.push(month);

            config.data.datasets.forEach(function(dataset) {
                dataset.data.push(randomScalingFactor());
            });

            window.myLine.update();
        }
    });

    document.getElementById('removeDataset').addEventListener('click', function() {
        config.data.datasets.splice(0, 1);
        window.myLine.update();
    });

    document.getElementById('removeData').addEventListener('click', function() {
        config.data.labels.splice(-1, 1); // remove the label first

        config.data.datasets.forEach(function(dataset) {
            dataset.data.pop();
        });

        window.myLine.update();
    });*/
</script>
<?php require "/var/www/php/footer.php"; ?>