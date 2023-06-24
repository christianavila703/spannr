am5.ready(function () {
    let ytdAmountRaisedPerCategoryData = document.getElementById('investors-ytd-amount-raised-per-category').getAttribute('data-chart');

    let root = am5.Root.new("investors-ytd-amount-raised-per-category");
    let spannrTheme = am5.Theme.new(root);

    spannrTheme.rule("Label").setAll({
        fontSize: ".9rem"
    });

    root.setThemes([
        am5themes_Animated.new(root),
        spannrTheme
    ]);

    let chart = root.container.children.push(am5xy.XYChart.new(root, {
        panX: true,
        panY: true,
        wheelX: "panX",
        wheelY: "zoomX",
        pinchZoomX: true
    }));

    let cursor = chart.set("cursor", am5xy.XYCursor.new(root, {}));
    cursor.lineY.set("visible", false);


    let xRenderer = am5xy.AxisRendererX.new(root, {minGridDistance: 30});
    xRenderer.labels.template.setAll({
        rotation: -90,
        centerY: am5.p50,
        centerX: am5.p100,
        paddingRight: 15
    });

    xRenderer.grid.template.setAll({
        location: 1
    })

    let xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, {
        categoryField: "category",
        renderer: xRenderer,
        tooltip: am5.Tooltip.new(root, {})
    }));

    let yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
        renderer: am5xy.AxisRendererY.new(root, {
            strokeOpacity: 0.1
        })
    }));

    let series = chart.series.push(am5xy.ColumnSeries.new(root, {
        name: "Amount Raised Per Category",
        xAxis: xAxis,
        yAxis: yAxis,
        valueYField: "value",
        sequencedInterpolation: true,
        categoryXField: "category",
        tooltip: am5.Tooltip.new(root, {
            labelText: "{valueY}"
        })
    }));

    series.columns.template.setAll({cornerRadiusTL: 5, cornerRadiusTR: 5, strokeOpacity: 0});
    series.columns.template.adapters.add("fill", function (fill, target) {
        return chart.get("colors").getIndex(series.columns.indexOf(target));
    });

    series.columns.template.adapters.add("stroke", function (stroke, target) {
        return chart.get("colors").getIndex(series.columns.indexOf(target));
    });

    am5.net.load(ytdAmountRaisedPerCategoryData).then(function(result) {
        xAxis.data.setAll(am5.JSONParser.parse(result.response));
        series.data.setAll(am5.JSONParser.parse(result.response));
    }).catch(function(result) {
        console.log("Error loading " + result.xhr.responseURL);
    });

    series.appear(1000);
    chart.appear(1000, 100);
});
