<div id="investors-year-over-year-bar-chart-race" data-chart="{{ route('public.content.investors.charts.year-over-year-bar-chart-race') }}"></div>
<script src="https://cdn.amcharts.com/lib/5/index.js"></script>
<script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
<script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>

<script>
    am5.ready(function () {

    let allData = {!! $data !!};

    let root = am5.Root.new("investors-year-over-year-bar-chart-race");

    root.numberFormatter.setAll({
        numberFormat: "#a",

        // Group only into M (millions), and B (billions)
        bigNumberPrefixes: [
            {number: 1e3, suffix: "k"},
            {number: 1e6, suffix: "M"},
            {number: 1e9, suffix: "B"}
        ],

        // Do not use small number prefixes at all
        smallNumberPrefixes: []
    });

    let stepDuration = 2200;

    root.setThemes([am5themes_Animated.new(root)]);

    let chart = root.container.children.push(am5xy.XYChart.new(root, {
        panX: true,
        panY: true,
        wheelX: "none",
        wheelY: "none"
    }));

    chart.zoomOutButton.set("forceHidden", true);

    let yRenderer = am5xy.AxisRendererY.new(root, {
        minGridDistance: 20,
        inversed: true
    });

    yRenderer.grid.template.set("visible", false);

    let yAxis = chart.yAxes.push(am5xy.CategoryAxis.new(root, {
        maxDeviation: 0,
        categoryField: "round",
        renderer: yRenderer
    }));

    let xAxis = chart.xAxes.push(am5xy.ValueAxis.new(root, {
        maxDeviation: 0,
        min: 0,
        strictMinMax: true,
        extraMax: 0.1,
        renderer: am5xy.AxisRendererX.new(root, {})
    }));

    xAxis.set("interpolationDuration", stepDuration / 10);
    xAxis.set("interpolationEasing", am5.ease.linear);

    let series = chart.series.push(am5xy.ColumnSeries.new(root, {
        xAxis: xAxis,
        yAxis: yAxis,
        valueXField: "value",
        categoryYField: "round",
        maskBullets: false
    }));

    series.columns.template.setAll({cornerRadiusBR: 5, cornerRadiusTR: 5});

    series.columns.template.adapters.add("fill", function (fill, target) {
        return chart.get("colors").getIndex(series.columns.indexOf(target));
    });

    series.columns.template.adapters.add("stroke", function (stroke, target) {
        return chart.get("colors").getIndex(series.columns.indexOf(target));
    });

    // Add label bullet
    series.bullets.push(function () {
        return am5.Bullet.new(root, {
            locationX: 1,
            sprite: am5.Label.new(root, {
                text: "{valueXWorking.formatNumber('#.# a')}",
                fill: am5.color('#000'),
                rightX: am5.p100,
                centerY: am5.p50,
                populateText: true
            })
        });
    });

    let label = chart.plotContainer.children.push(am5.Label.new(root, {
        text: {{ $startYear }},
        fontSize: "8em",
        opacity: 0.2,
        x: am5.p100,
        y: am5.p100,
        centerY: am5.p100,
        centerX: am5.p100
    }));

    // Get series item by category
    function getSeriesItem(category) {
        for (let i = 0; i < series.dataItems.length; i++) {
            let dataItem = series.dataItems[i];
            if (dataItem.get("categoryY") === category) {
                return dataItem;
            }
        }
    }

    // Axis sorting
    function sortCategoryAxis() {
        // sort by value
        series.dataItems.sort(function (x, y) {
            return y.get("valueX") - x.get("valueX"); // descending
            //return x.get("valueX") - y.get("valueX"); // ascending
        });

        // go through each axis item
        am5.array.each(yAxis.dataItems, function (dataItem) {
            // get corresponding series item
            let seriesDataItem = getSeriesItem(dataItem.get("category"));

            if (seriesDataItem) {
                // get index of series data item
                let index = series.dataItems.indexOf(seriesDataItem);
                // calculate delta position
                let deltaPosition =
                    (index - dataItem.get("index", 0)) / series.dataItems.length;
                // set index to be the same as series data item index
                if (dataItem.get("index") !== index) {
                    dataItem.set("index", index);
                    // set deltaPosition instanlty
                    dataItem.set("deltaPosition", -deltaPosition);
                    // animate delta position to 0
                    dataItem.animate({
                        key: "deltaPosition",
                        to: 0,
                        duration: stepDuration / 2,
                        easing: am5.ease.out(am5.ease.cubic)
                    });
                }
            }
        });
        yAxis.dataItems.sort(function (x, y) {
            return x.get("index") - y.get("index");
        });
    }

    let year = {{ $startYear }};

    let interval = setInterval(function () {
        year++;

        if (year > {{ $currentYear }}) {
            clearInterval(interval);
            clearInterval(sortInterval);
        }

        updateData();
    }, stepDuration);

    let sortInterval = setInterval(function () {
        sortCategoryAxis();
    }, 100);

    function setInitialData() {
        let d = allData[year];

        for (let n in d) {
            series.data.push({round: n, value: d[n]});
            yAxis.data.push({round: n});
        }
    }

    function updateData() {
        let itemsWithNonZero = 0;

        if (allData[year]) {
            label.set("text", year.toString());

            am5.array.each(series.dataItems, function (dataItem) {
                let category = dataItem.get("categoryY");
                let value = allData[year][category];

                if (value > 0) {
                    itemsWithNonZero++;
                }

                dataItem.animate({
                    key: "valueX",
                    to: value,
                    duration: stepDuration,
                    easing: am5.ease.linear
                });
                dataItem.animate({
                    key: "valueXWorking",
                    to: value,
                    duration: stepDuration,
                    easing: am5.ease.linear
                });
            });

            yAxis.zoom(0, itemsWithNonZero / yAxis.dataItems.length);
        }
    }

    setInitialData();
    setTimeout(function () {
      year++;
      updateData();
    }, 50);

    series.appear(1000);
    chart.appear(1000, 100);
});
</script>
