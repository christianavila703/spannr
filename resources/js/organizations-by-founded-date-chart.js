let foundedDateData = document.getElementById('organizations-by-founded-date').getAttribute('data-chart');

am5.ready(function () {
    let root = am5.Root.new("organizations-by-founded-date");
    let spannrTheme = am5.Theme.new(root);

    spannrTheme.rule("Label").setAll({
        fontSize: ".9rem"
    });

    root.setThemes([
        am5themes_Animated.new(root),
        spannrTheme
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
        am5xy.GaplessDateAxis.new(root, {
            baseInterval: {
                timeUnit: "year",
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
        labelText: "[bold]{value}[/]{label}",
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
        dateFormat: "yyyy"
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
    am5.net.load(foundedDateData).then(function(result) {
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

});
