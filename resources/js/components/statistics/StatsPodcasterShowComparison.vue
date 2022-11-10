<template>
    <div
        ref="d3chart"
    >
        <svg
            :viewBox="viewBox"
        >
            <multi-line-chart :data="dataset"></multi-line-chart>
        </svg>
        <b-button @click="updateChart">Update Chart</b-button>
    </div>
</template>

<script>
import * as d3 from "d3";
import MultiLineChart from "./MultiLineChart";

export default {
    name: "StatsPodcasterShowComparison",

    components: {
        MultiLineChart
    },

    data() {
        return {
            dataset: [],
            g:null,
            svg: null,
            margin: 200,
            svg_height: 270,
            svg_width: 500
        }
    },

    props: {
        width: {
            default: 500,
            type: Number,
        },
        height: {
            default: 800,
            type: Number,
        }
    },

    methods: {
        scaleX: function(){
            return d3.scaleLinear()
                .domain([0,50])
                .range([0,this.width - this.margin])
                .nice()
        },
        scaleY: function(){
            return d3.scaleLinear()
                .domain([0,500])
                .range([this.height - this.margin,0])
                .nice()
        },
        updateChart() {
            //this.dataset = [];
            this.g.selectAll('.bar')
                .data(this.dataset)
                .enter()
                .append("rect")
                .attr("class", "bar")
                .attr("x", function(d) { return this.scaleX(d.x) - 5; })
                .attr("y", function(d) { return this.scaleY(d.y); })
                .attr("width", 1)
                .attr("height", function(d) { return this.svg_height - this.scaleY(d.y); })
                .exit()
                .remove()
        },
        labels() {
            const xLabel = 'Sepal Length';
            const yLabel = 'Petal Length';
            const margin = { left: 120, right: 30, top: 20, bottom: 120 };

            let svg = d3.select(this.$refs.d3chart);
            const g = svg.append('g')
                .attr('transform', `translate(${margin.left},${margin.top})`);
            const xAxisG = g.append('g')
                .attr('transform', `translate(0, ${innerHeight})`);
            const yAxisG = g.append('g');

            xAxisG.append('text')
                .attr('class', 'axis-label')
                .attr('x', innerWidth / 2)
                .attr('y', 100)
                .text(xLabel);

            yAxisG.append('text')
                .attr('class', 'axis-label')
                .attr('x', -innerHeight / 2)
                .attr('y', -60)
                .attr('transform', `rotate(-90)`)
                .style('text-anchor', 'middle')
                .text(yLabel);
        }
    },

    mounted() {
        this.dataset = [[4, 8, 15, 16, 23, 42], [14, 18, 25, 26, 33, 52], [24, 28, 35, 46, 53, 1]];

        this.svg = d3.select(this.$refs.d3chart)
            .append("svg")
            .attr("width", this.width )
            .attr("height", this.height);

        this.g = this.svg
            .append("g")
            .attr("transform", "translate(" + 50 + "," + 50 + ")");

        this.g.append("g")
            .attr("transform", "translate(0,"+ this.svg_height + ")")
            .call(d3.axisBottom(this.scaleX()).tickFormat(function (d) {
                return d;
            }).ticks(25))

        this.g.append("g")
            .call(d3.axisLeft(this.scaleY()).tickFormat(function (d) {
                return d;
            }).ticks(20));

        this.g.selectAll(".bar")
            .data(this.dataset)
            .enter()
            .append("rect")
            .attr("class", "bar")
            .on("mouseover", onMouseOver)
            .on("mouseout", onMouseOut)
            .attr("x", function(d) { return this.scaleX(d.x) - 5; })
            .attr("y", function(d) { return this.scaleY(d.y); })
            .attr("width", 1)
            .attr("height", function(d) { return this.svg_height - this.scaleY(d.y); })

        function onMouseOut() {
            d3.select(this)
                .attr('class', 'bar');
            d3.select(this)
                .transition()
                .duration(400)
                .style("fill", "steelblue")
                .attr('width', 1)
                .attr("y", function(d) { return this.scaleY(d.y); })
                .attr("height", function(d) { return this.svg_height - this.scaleY(d.y); });

            d3.selectAll('.val')
                .remove()
        }

        function onMouseOver(d) {
            d3.select(this)
                .transition()
                .duration(400)
                .style("fill", "orange")
                .attr('width', 15)
                .attr("y", function(d) { return this.scaleY(d.y); })
                .attr("height", function(d) { return this.svg_height - this.scaleY(d.y); });

            this.g.append("text")
                .attr('class', 'val') // add class to text label
                .attr('x', function() {
                    return this.scaleX(d.x - 3);
                })
                .attr('y', function() {
                    return this.scaleY(d.y + 10);
                })
                .text(function() {
                    return [ "x:"+d.x+" "+"y:"+ d.y +"g"];  // Value of the text
                });
        }

        this.labels();
    },
    computed: {
        viewBox() {
            return `0 0 ${this.svg_width} ${this.svg_height}`;
        }
    },
}
</script>

<style scoped>
    .bar {
        fill: steelblue;
    }
    .selectedbar {
        fill: orangered;
    }
</style>
