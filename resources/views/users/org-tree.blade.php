@extends('layouts.app')

@section('content')

<div class="container-fluid mt-3">

    <div class="d-flex justify-content-between align-items-center mb-2">
        <h4>Organization Structure</h4>

        <!-- Controls -->
        <div>
            <button class="btn btn-light btn-sm" onclick="chart.zoomIn()">+</button>
            <button class="btn btn-light btn-sm" onclick="chart.zoomOut()">-</button>
            <button class="btn btn-light btn-sm" onclick="chart.fit()">⛶</button>
        </div>
    </div>

    <div id="tree" style="width:100%; height:85vh; border:1px solid #ddd;"></div>
</div>

@endsection
<script src="https://balkan.app/js/OrgChart.js"></script>
<script>
let chart;

document.addEventListener("DOMContentLoaded", function () {

    chart = new OrgChart(document.getElementById("tree"), {

        layout: OrgChart.mixed, // 🔥 hybrid layout

        enableSearch: true,
        mouseScrool: OrgChart.action.zoom,
        scaleInitial: 0.7,

        nodeBinding: {
            field_0: "name",
            field_1: "title",
            field_2: "emp_id"
        },

        collapse: {
            level: 2
        },

        nodes: @json($nodes)
    });

    // 🔥 CUSTOM CARD (Zoho-like)
    chart.nodeTemplate = function (node) {

        let color = "#6c757d"; // default

        if (node.title && node.title.toLowerCase().includes("manager")) {
            color = "#e91e63"; // pink
        } else if (node.title && node.title.toLowerCase().includes("head")) {
            color = "#00bcd4"; // cyan
        } else if (node.title && node.title.toLowerCase().includes("executive")) {
            color = "#9c27b0"; // purple
        }

        return `
        <div style="
            background:#fff;
            border-radius:10px;
            padding:10px;
            min-width:180px;
            border-left:5px solid ${color};
            box-shadow:0 4px 12px rgba(0,0,0,0.1);
            text-align:left;
        ">

            <div style="display:flex; align-items:center;">
                <div style="
                    width:35px;
                    height:35px;
                    border-radius:50%;
                    background:#eee;
                    margin-right:10px;
                "></div>

                <div>
                    <div style="font-weight:600; font-size:13px;">
                        ${node.name}
                    </div>

                    <div style="font-size:11px; color:#666;">
                        ${node.title ?? ''}
                    </div>

                    <div style="font-size:10px; color:#999;">
                        Emp ID: ${node.emp_id}
                    </div>
                </div>
            </div>

            ${node.count > 0 ? `
                <div style="
                    margin-top:6px;
                    font-size:11px;
                    background:#f1f1f1;
                    padding:3px 8px;
                    border-radius:20px;
                    display:inline-block;
                ">
                    👥 ${node.count}
                </div>
            ` : ''}

        </div>
        `;
    };

    // 🔥 CLICK → OPEN PROFILE
    chart.on('click', function(sender, args){
        window.location.href = "/users/" + args.node.id;
    });

});
</script>