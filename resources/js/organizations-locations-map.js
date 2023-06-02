am5.ready(function () {
    let mapUrl = document.getElementById('organizations-locations-map').getAttribute('data-chart');

    let root = am5.Root.new("organizations-locations-map");

    root.setThemes([
        am5themes_Animated.new(root)
    ]);

    let chart = root.container.children.push(am5map.MapChart.new(root, {
        panX: "translateX",
        panY: "translateY",
        projection: am5map.geoMercator()
    }));

    // World Series
    let polygonSeries = chart.series.push(am5map.MapPolygonSeries.new(root, {
        geoJSON: am5geodata_worldLow,
        exclude: ["AQ"]
    }));

    polygonSeries.mapPolygons.template.setAll({
        interactive: false
    });

    polygonSeries.mapPolygons.template.set("fill", am5.color('#d3d3d3'));

    // USA Series
    let polygonSeriesUS = chart.series.push(am5map.MapPolygonSeries.new(root, {
        geoJSON: am5geodata_usaLow
    }));

    polygonSeriesUS.mapPolygons.template.setAll({
        interactive: false
    });

    polygonSeriesUS.mapPolygons.template.set("fill", am5.color('#d3d3d3'));

    chart.set("zoomControl", am5map.ZoomControl.new(root, {}));

    chart.chartContainer.get("background").events.on("click", function () {
        chart.goHome();
    });

    let tooltip = am5.Tooltip.new(root, {
        getFillFromSprite: false,
        labelText: "[bold]{title}[/]\n{type}"
    });

    tooltip.get("background").setAll({
        fill: am5.color("#000")
    });

    let pointSeries = chart.series.push(am5map.MapPointSeries.new(root, {}));

    let bulletTemplate = am5.Template.new({
        fill: am5.color("#555")
    });

    pointSeries.bullets.push(function () {
        let container = am5.Container.new(root, {
            tooltipText: "{title}",
            cursorOverStyle: "pointer"
        });

        container.events.on("click", (event) => {
            window.location.href = event.target.dataItem.dataContext.url;
        });

        container.children.push(
            am5.Circle.new(root, {
                radius: 4,
                tooltipY: 0,
                strokeOpacity: 0,
                templateField: "bulletSettings",
            }, bulletTemplate)
        );

        return am5.Bullet.new(root, {
            sprite: container,
        });
    });

    pointSeries.set("tooltip", tooltip);

    am5.net.load(mapUrl).then(function (result) {
        pointSeries.data.setAll(am5.JSONParser.parse(result.response));
    }).catch(function (result) {
        console.log("Error loading " + result.xhr.responseURL);
    });

    chart.appear(1000, 100);

});
