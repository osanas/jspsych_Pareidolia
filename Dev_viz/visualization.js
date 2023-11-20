// D3.js script for creating an interactive visualization of the collective map

document.addEventListener('DOMContentLoaded', function() {
    // Dimensions of the visualization
    const width = 960, height = 600;

    // Create an SVG container
    const svg = d3.select("body").append("svg")
        .attr("width", width)
        .attr("height", height)
        .call(d3.zoom().on("zoom", function (event) {
            svg.attr("transform", event.transform);
        }))
        .append("g");

    // Load data from the PHP script
    d3.json('process_and_visualize.php').then(data => {
        // Scale for circle radius based on word frequency
        const radiusScale = d3.scaleSqrt()
            .domain([d3.min(data, d => d.frequency), d3.max(data, d => d.frequency)])
            .range([5, 30]); // Min and max radius of circles

        // Define the color scale
        const colorScale = d3.scaleOrdinal(d3.schemeCategory10);

        // Create circles for each data point
        const circles = svg.selectAll(".word")
            .data(data)
            .enter().append("circle")
            .attr("class", "word")
            .attr("cx", d => d.position[0])
            .attr("cy", d => d.position[1])
            .attr("r", d => radiusScale(d.frequency))
            .style("fill", d => colorScale(d.fd))
            .style("opacity", 0.7);

        // Add labels to each circle
        const labels = svg.selectAll(".label")
            .data(data)
            .enter().append("text")
            .attr("class", "label")
            .attr("x", d => d.position[0])
            .attr("y", d => d.position[1])
            .text(d => d.word)
            .style("text-anchor", "middle")
            .style("fill", "black");

        // Filter functionality based on FD
        d3.selectAll("#fd-filter").on("change", function() {
            const selectedFD = this.value;
            circles.style("display", d => d.fd === selectedFD ? "block" : "none");
            labels.style("display", d => d.fd === selectedFD ? "block" : "none");
        });
    });
});
