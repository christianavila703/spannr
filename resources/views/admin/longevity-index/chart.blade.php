<!-- HTML -->
<div id="chart" data-chart="{{ route('longevity-index.chart-data') }}"></div>


<style>
    #chart {
        width: 100%;
        height: 500px;
    }
</style>

<!-- Resources -->
<script src="https://cdn.amcharts.com/lib/5/index.js"></script>
<script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
<script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>

<!-- Chart code -->
<script>
    // const chartData = JSON.parse(document.getElementById('chartdiv').getAttribute('data-chart'));
    const dataUrl = document.getElementById('chart').getAttribute('data-chart');
    console.log(dataUrl);

    am5.ready(function() {

        // Create root element
        // https://www.amcharts.com/docs/v5/getting-started/#Root_element
        let root = am5.Root.new("chart");

        root.setThemes([
            am5themes_Animated.new(root),
        ]);

        var chart = root.container.children.push(
            am5xy.XYChart.new(root, {
                panY: false,
                wheelY: "zoomX",
                layout: root.verticalLayout
            })
        );

        // Axis
        let xAxis = chart.xAxes.push(
            am5xy.DateAxis.new(root, {
                baseInterval: {
                    timeUnit: "day",
                    count: 1
                },
                renderer: am5xy.AxisRendererX.new(root, {})
            })
        );

        let yAxis = chart.yAxes.push(
            am5xy.ValueAxis.new(root, {
                renderer: am5xy.AxisRendererY.new(root, {})
            })
        );

        // Init Tooltip
        let tooltip = am5.Tooltip.new(root, {
            getFillFromSprite: false,
            autoTextColor: false,
            labelText: "[bold]{formatted_date}[/]\n---\n{formatted_value}      Δ {change}",
        });

        tooltip.get("background").setAll({
            fill: am5.color('#ed008c'),
            fillOpacity: 1,
            stroke: am5.color('#ed008c'),
            strokeOpacity: 1
        });

        tooltip.label.setAll({
            fill: am5.color('#fff')
        });

        // Create Series
        let series = chart.series.push(
            am5xy.LineSeries.new(root, {
                name: "Series",
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "value",
                valueXField: "date",
            })
        );
        series.set("tooltip", tooltip);

        // Create Series Processor
        series.data.processor = am5.DataProcessor.new(root, {
            numericFields: ["value"],
            dateFields: ["date"],
            dateFormat: "yyyy-MM-dd"
        });

        // Series Template
        series.set("fill", am5.color('#0ac7fe'));
        series.set("stroke", am5.color('#5625ca'));

        series.fills.template.setAll({
            fillOpacity: .5,
            visible: true
        });

        series.strokes.template.setAll({
            strokeWidth: 2,
        });

        // Series Bullets
        series.bullets.push(function(root) {
            return am5.Bullet.new(root, {
                sprite: am5.Circle.new(root, {
                    radius: 0,
                    fill: series.get('fill'),
                    stroke: series.get('stroke'),
                    strokeWidth: 2
                })
            });
        });

        // Load JSON
        am5.net.load(dataUrl).then(function(result) {
            series.data.setAll(am5.JSONParser.parse(result.response));
        }).catch(function(result) {
            console.log("Error loading " + result.xhr.responseURL);
        });

        // Add cursor
        // https://www.amcharts.com/docs/v5/charts/xy-chart/cursor/
        var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {
            xAxis: xAxis,
            behavior: "none"
        }));
        // cursor.lineY.set("visible", false);

        // add scrollbar
        var scrollbarX = am5.Scrollbar.new(root, {
            orientation: "horizontal"
        });

        chart.set("scrollbarX", scrollbarX);
        chart.bottomAxesContainer.children.push(scrollbarX);

        // Animate on Load
        chart.appear(1000, 100);

    }); // end am5.ready()
</script>
