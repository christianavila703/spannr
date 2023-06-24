am5.ready(function () {
    let dataUrl = document.getElementById('entities-by-type-pie-chart').getAttribute('data-chart');

    let root = am5.Root.new("entities-by-type-pie-chart");

    root.setThemes([
        am5themes_Animated.new(root)
    ]);

    let colorSet = am5.ColorSet.new(root, {
      colors: [
        am5.color("#0ac7fe"),
        am5.color("#ed008c"),
        am5.color("#5625ca"),
        am5.color("#88f200"),
        am5.color("#fe8c0c")
      ]
    });

    let chart = root.container.children.push(
        am5percent.PieChart.new(root, {
            endAngle: 270,
            layout: root.verticalLayout,
        })
    );

    let series = chart.series.push(
        am5percent.PieSeries.new(root, {
            valueField: "value",
            categoryField: "category",
            endAngle: 270,
            legendLabelText: "[{fill}]{category}[/]",
            legendValueText: "[bold {fill}]{value}[/]"
        })
    );

    series.labels.template.setAll({
        maxWidth: 150,
        oversizedBehavior: "wrap",
        text: "{category}",
        alignLabels: false
    });

    series.set("colors", colorSet);

    series.states.create("hidden", {
        endAngle: -90
    });

    am5.net.load(dataUrl).then(function (result) {
        series.data.setAll(am5.JSONParser.parse(result.response));

        if (window.innerWidth < 600) {
            series.labels.template.set("forceHidden", true);
            series.ticks.template.set("forceHidden", true);

            let legend = chart.children.push(
                am5.Legend.new(root, {
                    centerX: am5.percent(50),
                    x: am5.percent(50),
                    marginBottom: 20
                })
            );

            legend.data.setAll(series.dataItems);
        }
    }).catch(function (result) {
        console.log("Error loading " + result.xhr.responseURL);
    });

    series.appear(1000, 100);
});
