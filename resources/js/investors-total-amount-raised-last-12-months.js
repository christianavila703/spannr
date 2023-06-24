am5.ready(function () {
    let totalAmountRaisedLast12MonthsData = document.getElementById('investors-total-amount-raised-last-12-months').getAttribute('data-chart');

    let root = am5.Root.new("investors-total-amount-raised-last-12-months");
    let spannrTheme = am5.Theme.new(root);

    spannrTheme.rule("Label").setAll({
        fontSize: ".9rem"
    });

    root.setThemes([
        am5themes_Animated.new(root),
        spannrTheme
    ]);

    let chart = root.container.children.push(
        am5xy.XYChart.new(root, {
            panY: false,
            wheelY: "zoomX",
            layout: root.verticalLayout
        })
    );

    // Axis
    let xAxis = chart.xAxes.push(am5xy.DateAxis.new(root, {
        maxDeviation: 0.1,
        groupData: true,
        baseInterval: {
            timeUnit: "day",
            count: 1
        },
        renderer: am5xy.AxisRendererX.new(root, {}),
        tooltip: am5.Tooltip.new(root, {})
    }));

    let yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
        maxDeviation: 0.2,
        renderer: am5xy.AxisRendererY.new(root, {})
    }));

    let tooltip = am5.Tooltip.new(root, {
        getFillFromSprite: false,
        autoTextColor: false,
        labelText: "{label}",
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

    let series = chart.series.push(
        am5xy.LineSeries.new(root, {
            name: "Total Amount Raised Last 12 Months",
            xAxis: xAxis,
            yAxis: yAxis,
            valueYField: "value",
            valueXField: "date",
        })
    );
    series.set("tooltip", tooltip);

    series.data.processor = am5.DataProcessor.new(root, {
        numericFields: ["value"],
        dateFields: ["date"],
        dateFormat: "yyyy-MM-dd",
    });

    series.set("fill", am5.color('#0ac7fe'));
    series.set("stroke", am5.color('#5625ca'));

    series.fills.template.setAll({
        fillOpacity: .5,
        visible: true
    });

    series.strokes.template.setAll({
        strokeWidth: 2,
    });

    series.bullets.push(function(root) {
        return am5.Bullet.new(root, {
            sprite: am5.Circle.new(root, {
                radius: 0,
                fill: series.get('stroke'),
                stroke: series.get('stroke'),
                strokeWidth: 2
            })
        });
    });

    am5.net.load(totalAmountRaisedLast12MonthsData).then(function(result) {
        series.data.setAll(am5.JSONParser.parse(result.response));
    }).catch(function(result) {
        console.log("Error loading " + result.xhr.responseURL);
    });

    let cursor = chart.set("cursor", am5xy.XYCursor.new(root, {
        xAxis: xAxis,
        yAxis: yAxis
    }));

    let scrollbarX = am5.Scrollbar.new(root, {
        orientation: "horizontal"
    });

    chart.set("scrollbarX", scrollbarX);
    chart.bottomAxesContainer.children.push(scrollbarX);

    chart.appear(1000, 100);

});
