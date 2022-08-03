<div>
    <canvas id="myChart"></canvas>
</div>
<div id="error" style="display: none;">
    <div class="lead"><i class="fa-duotone fa-spider-black-widow"></i></div>
    Oops - we're having trouble loading this chart
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    #error {
        padding: 2rem;
        text-align: center;
        border: 1px solid #e0e0e0;
        color: #555;
    }
    .tooltip-table {
        color: #fff;
    }

    .tooltip-table th {
        font-size: 1.15rem;
        border-bottom: 1px solid rgba(255, 255, 255, .5);
    }
</style>

<script>

    const getJSON = async url => {
        const response = await fetch(url);
        if (!response.ok) {
            throw new Error(response.statusText);
        }
        return response.json(); // returns a promise, which resolves to this data value
    }

    let labels;
    let dataOpen;
    let dataClose;
    let dataHigh;
    let dataLow;

    console.log("Fetching data...");
    getJSON("/admin/organizations/{{ $slug }}/stock-data").then(data => {

        labels = data.chartLabels.map(function (e) {
            return e;
        });

        dataOpen = data.valuesOpen.map(function (e) {
            return {x: e.x, y: e.y};
        });

        dataClose = data.valuesClose.map(function (e) {
            return {x: e.x, y: e.y};
        });

        dataHigh = data.valuesHigh.map(function (e) {
            return {x: e.x, y: e.y};
        });

        dataLow = data.valuesLow.map(function (e) {
            return {x: e.x, y: e.y};
        });

        console.log(labels);
        console.log(dataOpen);

        const chartData = {
            labels: labels,
            datasets: [
                {
                    label: 'Open',
                    backgroundColor: 'transparent',
                    borderColor: 'transparent',
                    data: dataOpen,
                },
                {
                    label: 'Close',
                    backgroundColor: '#5625ca',
                    borderColor: '#0ac7fe',
                    data: dataClose,
                },
                {
                    label: 'Low',
                    backgroundColor: 'transparent',
                    borderColor: 'transparent',
                    data: dataLow,
                },
                {
                    label: 'High',
                    backgroundColor: 'transparent',
                    borderColor: 'transparent',
                    data: dataHigh,
                }
            ]
        };

        const getOrCreateTooltip = (chart) => {
            let tooltipEl = chart.canvas.parentNode.querySelector('div');

            if (!tooltipEl) {
                tooltipEl = document.createElement('div');
                tooltipEl.style.background = '#ed008c';
                tooltipEl.style.borderRadius = '3px';
                tooltipEl.style.color = '#fff';
                tooltipEl.style.opacity = 1;
                tooltipEl.style.pointerEvents = 'none';
                tooltipEl.style.position = 'absolute';
                tooltipEl.style.transform = 'translate(-50%, 0)';
                tooltipEl.style.transition = 'all .1s ease';

                const table = document.createElement('table');
                table.classList.add('tooltip-table');
                table.style.margin = '0px';

                tooltipEl.appendChild(table);
                chart.canvas.parentNode.appendChild(tooltipEl);
            }

            return tooltipEl;
        };

        const externalTooltipHandler = (context) => {
            // Tooltip Element
            const {chart, tooltip} = context;
            const tooltipEl = getOrCreateTooltip(chart);

            // Hide if no tooltip
            if (tooltip.opacity === 0) {
                tooltipEl.style.opacity = 0;
                return;
            }

            // Set Text
            if (tooltip.body) {
                console.log(tooltip.body);
                const titleLines = tooltip.title || [];
                const bodyLines = tooltip.body.map(b => b.lines);

                const tableHead = document.createElement('thead');

                titleLines.forEach(title => {
                    const tr = document.createElement('tr');
                    tr.style.borderWidth = '0px';

                    const th = document.createElement('th');
                    const text = document.createTextNode(title);

                    th.appendChild(text);
                    tr.appendChild(th);
                    tableHead.appendChild(tr);
                });

                const tableBody = document.createElement('tbody');
                bodyLines.forEach((body, i) => {

                    const tr = document.createElement('tr');
                    tr.style.backgroundColor = 'inherit';
                    tr.style.borderWidth = '0px';

                    const td = document.createElement('td');
                    td.style.borderWidth = '0px';

                    const text = document.createTextNode(body);

                    td.appendChild(text);
                    tr.appendChild(td);
                    tableBody.appendChild(tr);
                });

                const tableRoot = tooltipEl.querySelector('table');

                // Remove old children
                while (tableRoot.firstChild) {
                    tableRoot.firstChild.remove();
                }

                // Add new children
                tableRoot.appendChild(tableHead);
                tableRoot.appendChild(tableBody);
            }

            const {offsetLeft: positionX, offsetTop: positionY} = chart.canvas;

            // Display, position, and set styles for font
            tooltipEl.style.opacity = 1;
            tooltipEl.style.left = positionX + tooltip.caretX + 'px';
            tooltipEl.style.top = positionY + tooltip.caretY + 'px';
            tooltipEl.style.font = tooltip.options.bodyFont.string;
            tooltipEl.style.padding = tooltip.options.padding + 'px ' + tooltip.options.padding + 'px';
        };

        const config = {
            type: 'line',
            data: chartData,
            options: {
                x: {
                    type: 'time',
                    time: {
                        // Luxon format string
                        tooltipFormat: 'DD T'
                    },
                    title: {
                        display: true,
                        text: 'Date'
                    },
                },
                scales: {
                    x: {
                        grid: {
                            // tickColor: 'red'
                        },
                        ticks: {
                            color: 'black',
                            autoSkip: true,
                            autoSkipPadding: 20,
                            maxRotation: 0
                        }
                    }
                },
                elements: {
                    point: {
                        pointBackgroundColor: '#5625ca',
                        pointBorderColor: '#5625ca',
                        radius: 0,
                        pointHoverRadius: 0,
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: false,
                        position: 'nearest',
                        callbacks: {
                            label: function (context) {
                                let label = context.dataset.label || '';

                                if (label) {
                                    label += ': ';
                                }

                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('en-US', {
                                        style: 'currency',
                                        currency: 'USD'
                                    }).format(context.parsed.y);
                                }

                                return label;
                            }
                        },
                        external: externalTooltipHandler
                    }
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                },
            }
        };

        const myChart = new Chart(
            document.getElementById('myChart'),
            config
        );
    }).catch(error => {
        console.error(error);
        document.getElementById('myChart').style.display = 'none';
        document.getElementById('error').style.display = 'block';
    });

</script>
