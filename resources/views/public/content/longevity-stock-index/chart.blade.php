<!-- HTML -->
<div id="chart" data-chart="{{ route('public.content.longevity-index.chart') }}"></div>

<style>
    #chart {
        width: 100%;
        height: 500px;
    }
</style>

<script src="https://cdn.amcharts.com/lib/5/index.js"></script>
<script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
<script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>
<script src="{{ Vite::asset('resources/js/longevity-stock-chart.js') }}"></script>
